<?php
require_once __DIR__ . "/../helpers/Conexion.php";

class PedidosController {
    
    private $conn;

    public function __construct()
    {
        $db = new Conexion();
        $this->conn = $db->conectar();
    }

    
    /**
     * Listar pedidos con filtros:
     * filtros: cliente (nombre), estado, fecha_inicio, fecha_fin
     */
    public function getAll($filtros = [])
    {
        $sql = "SELECT ped.*, u.nombre AS cliente
                FROM pedidos ped
                LEFT JOIN usuarios u ON ped.id_usuario = u.id
                WHERE 1=1";

        $params = [];

        if (!empty($filtros['cliente'])) {
            $sql .= " AND u.nombre LIKE ?";
            $params[] = "%" . $filtros['cliente'] . "%";
        }

        if (!empty($filtros['estado'])) {
            $sql .= " AND ped.estado = ?";
            $params[] = $filtros['estado'];
        }

        if (!empty($filtros['fecha_inicio'])) {
            $sql .= " AND ped.fecha_pedido >= ?";
            $params[] = $filtros['fecha_inicio'];
        }

        if (!empty($filtros['fecha_fin'])) {
            $sql .= " AND ped.fecha_pedido <= ?";
            $params[] = $filtros['fecha_fin'];
        }

        $sql .= " ORDER BY ped.fecha_pedido DESC, ped.id DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener header del pedido + cliente + total
     */
    public function getById($id)
    {
        $sql = "SELECT ped.*, u.nombre AS cliente, u.correo, u.telefono, u.direccion
                FROM pedidos ped
                LEFT JOIN usuarios u ON ped.id_usuario = u.id
                WHERE ped.id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener detalles del pedido (líneas)
     */
    public function getDetalles($id_pedido)
    {
        $sql = "SELECT dp.*, p.nombre AS producto
                FROM detalles_pedidos dp
                LEFT JOIN productos p ON dp.id_producto = p.id
                WHERE dp.id_pedido = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id_pedido]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener lista de productos (id, nombre, precio) para el selector
     */
    public function getProducts()
    {
        $sql = "SELECT id, nombre, precio FROM productos ORDER BY nombre";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener lista de usuarios (clientes) para el selector
     * Filtra por rol = cliente
     */
    public function getUsers()
    {
        $sql = "SELECT id, nombre FROM usuarios WHERE rol = 'cliente' ORDER BY nombre";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Insertar pedido + detalles (transaccional)
     * $data: ['fecha_pedido','id_usuario','estado']
     * $detalles: array de arrays ['id_producto','cantidad','precio_unitario']
     */
   public function insert($data, $detalles)
{
    try {
        $this->conn->beginTransaction();

        $estadoNuevo = $data['estado'] ?? 'Pendiente';

        // calcular costo_total
        $costo_total = 0;
        foreach ($detalles as $d) {
            $line_total = floatval($d['precio_unitario']) * intval($d['cantidad']);
            $costo_total += $line_total;
        }

        // insertar pedido
        $sql = "INSERT INTO pedidos (fecha_pedido, id_usuario, costo_total, estado)
                VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            $data['fecha_pedido'],
            $data['id_usuario'],
            $costo_total,
            $estadoNuevo
        ]);

        $id_pedido = $this->conn->lastInsertId();

        // preparar detalle
        $sqlDet = "INSERT INTO detalles_pedidos (id_pedido, id_producto, cantidad, precio_cantidad)
                   VALUES (?, ?, ?, ?)";
        $stmtDet = $this->conn->prepare($sqlDet);

        $debeDescontar = ($estadoNuevo === "Entregado");

        foreach ($detalles as $d) {

            // Recuperar precio desde productos
            $stmtProd = $this->conn->prepare("SELECT precio FROM productos WHERE id = ?");
            $stmtProd->execute([$d['id_producto']]);
            $precio_unitario = $stmtProd->fetchColumn();

            if ($precio_unitario === false) {
                throw new Exception("Producto no encontrado: ID " . $d['id_producto']);
            }

            $line_total = floatval($precio_unitario) * intval($d['cantidad']);

            // insertar detalle
            $stmtDet->execute([
                $id_pedido,
                $d['id_producto'],
                $d['cantidad'],
                $line_total
            ]);

            /**
             * ----------------------------------
             *   DESCONTAR INVENTARIO SOLO SI:
             *
             *   estado = ENTREGADO
             * ----------------------------------
             */
            if ($debeDescontar) {

                // VALIDAR STOCK antes de descontar
                $stmtStock = $this->conn->prepare("SELECT cantidad FROM inventario WHERE id_producto = ?");
                $stmtStock->execute([$d['id_producto']]);
                $stock = $stmtStock->fetchColumn();

                if ($stock === false) {
                    throw new Exception("Inventario no encontrado para producto: " . $d['id_producto']);
                }

                if ($stock < $d['cantidad']) {
                    throw new Exception(
                        "Stock insuficiente para producto ID: " . $d['id_producto']
                    );
                }

                // descontar inventario
                $stmtUpd = $this->conn->prepare("
                    UPDATE inventario
                    SET cantidad = cantidad - ?
                    WHERE id_producto = ?
                ");
                $stmtUpd->execute([$d['cantidad'], $d['id_producto']]);
            }
        }

        $this->conn->commit();
        return true;

    } catch (Exception $e) {
        $this->conn->rollBack();
        error_log("Error INSERT pedido: " . $e->getMessage());
        return false;
    }
}



    /**
     * Actualizar pedido + detalles: strategy -> update header, delete detalles antiguos, insertar nuevos
     * $data: ['fecha_pedido','id_usuario','estado']
     * $detalles: same as insert
     */
    public function update($id, $data, $detalles)
{
    try {
        $this->conn->beginTransaction();

        // Obtener estado anterior
        $stmtOldState = $this->conn->prepare("SELECT estado FROM pedidos WHERE id = ?");
        $stmtOldState->execute([$id]);
        $estadoAnterior = $stmtOldState->fetchColumn();

        $estadoNuevo = $data['estado'] ?? 'Pendiente';

        // Obtener detalles anteriores
        $stmtOld = $this->conn->prepare("SELECT id_producto, cantidad FROM detalles_pedidos WHERE id_pedido = ?");
        $stmtOld->execute([$id]);
        $detallesAnteriores = $stmtOld->fetchAll(PDO::FETCH_ASSOC);

        /**
         * ---------------------------------------------------
         *    REGLAS DEFINIDAS POR EL USUARIO
         * ---------------------------------------------------
         *
         * 1. SI pasa de Pendiente → Entregado  → DESCONTAR
         * 2. SI pasa de Entregado → Cancelado → RESTAURAR
         * 
         *  NO SE HACE NINGÚN OTRO MOVIMIENTO
         */
        $debeDescontar = ($estadoAnterior === "Pendiente" && $estadoNuevo === "Entregado");
        $debeRestaurar = ($estadoAnterior === "Entregado" && $estadoNuevo === "Cancelado");


        // 1. Restaurar inventario SOLO SI se cancela un pedido entregado
        if ($debeRestaurar) {
            foreach ($detallesAnteriores as $old) {
                $stmtRestore = $this->conn->prepare("
                    UPDATE inventario
                    SET cantidad = cantidad + ?
                    WHERE id_producto = ?
                ");
                $stmtRestore->execute([$old['cantidad'], $old['id_producto']]);
            }
        }


        // 2. Recalcular total
        $costo_total = 0;
        foreach ($detalles as $d) {
            $costo_total += (float)$d['precio_unitario'] * (int)$d['cantidad'];
        }


        // 3. Actualizar encabezado
        $stmt = $this->conn->prepare("
            UPDATE pedidos 
            SET fecha_pedido = ?, id_usuario = ?, costo_total = ?, estado = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $data['fecha_pedido'],
            $data['id_usuario'],
            $costo_total,
            $estadoNuevo,
            $id
        ]);


        // 4. Eliminar detalles anteriores
        $stmtDel = $this->conn->prepare("DELETE FROM detalles_pedidos WHERE id_pedido = ?");
        $stmtDel->execute([$id]);


        // 5. Insertar nuevos detalles
        $sqlDet = "INSERT INTO detalles_pedidos (id_pedido, id_producto, cantidad, precio_cantidad)
                   VALUES (?, ?, ?, ?)";
        $stmtDet = $this->conn->prepare($sqlDet);


        foreach ($detalles as $d) {

            // Si se va a descontar, validar stock
            if ($debeDescontar) {
                $stmtStock = $this->conn->prepare("SELECT cantidad FROM inventario WHERE id_producto = ?");
                $stmtStock->execute([$d['id_producto']]);
                $stock = $stmtStock->fetchColumn();

                if ($stock < $d['cantidad']) {
                    throw new Exception("Stock insuficiente para producto ID: " . $d['id_producto']);
                }
            }

            // Insertar detalle
            $line_total = (float)$d['precio_unitario'] * (int)$d['cantidad'];
            $stmtDet->execute([
                $id,
                $d['id_producto'],
                $d['cantidad'],
                $line_total
            ]);

            // Descontar inventario SOLO si se cumple la regla
            if ($debeDescontar) {
                $stmtUpd = $this->conn->prepare("
                    UPDATE inventario
                    SET cantidad = cantidad - ?
                    WHERE id_producto = ?
                ");
                $stmtUpd->execute([$d['cantidad'], $d['id_producto']]);
            }
        }


        $this->conn->commit();
        return true;

    } catch (Exception $e) {
        $this->conn->rollBack();
        error_log("Error en update pedido: " . $e->getMessage());
        return false;
    }
}



    /**
     * Eliminar pedido + detalles
     */
public function delete($id)
{
    $transactionStarted = false;

    try {
        $stmt = $this->conn->prepare("SELECT estado FROM pedidos WHERE id = ?");
        $stmt->execute([$id]);
        $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$pedido) {
            throw new Exception("El pedido no existe.");
        }

        $estado = $pedido['estado'];

        $this->conn->beginTransaction();
        $transactionStarted = true;

        $stmt = $this->conn->prepare("SELECT id_producto, cantidad FROM detalles_pedidos WHERE id_pedido = ?");
        $stmt->execute([$id]);
        $detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Restaurar inventario correctamente
        if ($estado === "Pendiente" || $estado === "Cancelado") {
            foreach ($detalles as $d) {
                $stmtInv = $this->conn->prepare("
                    UPDATE inventario SET cantidad = cantidad + ? WHERE id_producto = ?
                ");
                $stmtInv->execute([$d['cantidad'], $d['id_producto']]);
            }
        }

        $stmtDelDet = $this->conn->prepare("DELETE FROM detalles_pedidos WHERE id_pedido = ?");
        $stmtDelDet->execute([$id]);

        $stmtDel = $this->conn->prepare("DELETE FROM pedidos WHERE id = ?");
        $stmtDel->execute([$id]);

        $this->conn->commit();

        header("Location: ../views/admin/listar_pedidos.php?msg=eliminado");
        exit();

    } catch (Exception $e) {

        if ($transactionStarted) {
            $this->conn->rollBack();
        }

        error_log("Error al eliminar pedido: " . $e->getMessage());

        header("Location: ../views/admin/listar_pedidos.php?msg=error");
        exit();
    }
}



    /**
     * Cambiar estado (helper)
     */
    public function changeEstado($id, $nuevoEstado)
    {
        $stmt = $this->conn->prepare("UPDATE pedidos SET estado = ? WHERE id = ?");
        return $stmt->execute([$nuevoEstado, $id]);
    }
}

if (basename(__FILE__) === basename($_SERVER["SCRIPT_FILENAME"])) {

    // Mostrar errores para depuración
    ini_set("display_errors", 1);
    ini_set("display_startup_errors", 1);
    error_reporting(E_ALL);

    $action = $_GET["action"] ?? null;
    $id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

    require_once "PedidosController.php";
    $controller = new PedidosController();

    if ($action === "delete") {

        if ($id <= 0) {
            header("Location: ../views/admin/listar_pedidos.php?msg=id_invalido");
            exit();
        }

        $ok = $controller->delete($id);

        if ($ok) {
            header("Location: ../views/admin/listar_pedidos.php?msg=eliminado");
        } else {
            header("Location: ../views/admin/listar_pedidos.php?msg=error");
        }

        exit();
    }

    echo "Acción no válida.";
    exit();
}
