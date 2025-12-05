<?php
require_once __DIR__ . "/../helpers/Conexion.php";

class ProductosController {

    private $conn;

    public function __construct()
    {
        $db = new Conexion();
        $this->conn = $db->conectar();
    }

    // LISTAR + FILTROS (incluye cantidad desde inventario)
    public function getAll($filtros = [])
    {
        $sql = "SELECT p.*, i.cantidad 
                FROM productos p
                LEFT JOIN inventario i ON p.id = i.id_producto
                WHERE 1=1";

        $params = [];

        if (!empty($filtros['nombre'])) {
            $sql .= " AND p.nombre LIKE ?";
            $params[] = "%" . $filtros['nombre'] . "%";
        }

        if (!empty($filtros['categoria'])) {
            $sql .= " AND p.categoria = ?";
            $params[] = $filtros['categoria'];
        }

        if (!empty($filtros['genero'])) {
            $sql .= " AND p.genero = ?";
            $params[] = $filtros['genero'];
        }

        if (!empty($filtros['precio_min'])) {
            $sql .= " AND p.precio >= ?";
            $params[] = $filtros['precio_min'];
        }

        if (!empty($filtros['precio_max'])) {
            $sql .= " AND p.precio <= ?";
            $params[] = $filtros['precio_max'];
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $sql = "SELECT p.*, i.cantidad, i.comentario
                FROM productos p
                LEFT JOIN inventario i ON p.id = i.id_producto
                WHERE p.id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // INSERTAR producto y su inventario
    public function insert($data, $inventario)
    {
        try {
            $this->conn->beginTransaction();

            $sql = "INSERT INTO productos (nombre, descripcion, precio, categoria, genero, imagen)
                    VALUES (?, ?, ?, ?, ?, ?)";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                $data['nombre'],
                $data['descripcion'],
                $data['precio'],
                $data['categoria'],
                $data['genero'],
                $data['imagen'] ?? null
            ]);

            $id_producto = $this->conn->lastInsertId();

            // Insertar inventario
            $sqlInv = "INSERT INTO inventario (id_producto, cantidad, comentario)
                    VALUES (?, ?, ?)";

            $stmtInv = $this->conn->prepare($sqlInv);
            $stmtInv->execute([
                $id_producto,
                $inventario['cantidad'],
                $inventario['comentario'] ?? null
            ]);

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }


    // ACTUALIZAR producto e inventario
    public function update($data, $id)
    {
        try {
            $this->conn->beginTransaction();

            // Actualizar producto
            $sql = "UPDATE productos SET nombre = ?, descripcion = ?, categoria = ?, precio = ? WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                $data["nombre"],
                $data["descripcion"],
                $data["categoria"],
                $data["precio"],
                $id
            ]);

            // Actualizar inventario
            $sql2 = "UPDATE inventario SET cantidad = ? WHERE id_producto = ?";
            $stmt2 = $this->conn->prepare($sql2);
            $stmt2->execute([
                $data["cantidad"],
                $id
            ]);

            $this->conn->commit();
            return true;

        } catch (PDOException $e) {
            return false;
        }
    }


    // ELIMINAR producto y inventario
    public function delete($id)
    {
        try {
            $this->conn->beginTransaction();

            // Eliminar inventario
            $stmtInv = $this->conn->prepare("DELETE FROM inventario WHERE id_producto = ?");
            $stmtInv->execute([$id]);

            // Eliminar producto
            $stmt = $this->conn->prepare("DELETE FROM productos WHERE id = ?");
            $stmt->execute([$id]);

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
}
