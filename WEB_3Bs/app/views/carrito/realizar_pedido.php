<?php
session_start();

// Validar sesión
if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../login.php");
    exit;
}

$usuario_id = $_SESSION["usuario_id"];

// Validar carrito
if (!isset($_SESSION["carrito"]) || empty($_SESSION["carrito"])) {
    header("Location: carrito.php");
    exit;
}

// Conexión a BD
$conexion = new mysqli("localhost", "root", "", "base_3bs");
if ($conexion->connect_error) {
    die("Error en la conexión: " . $conexion->connect_error);
}

// Verificar que el usuario existe
$checkUser = $conexion->query("SELECT id FROM usuarios WHERE id = $usuario_id");
if ($checkUser->num_rows == 0) {
    die("ERROR: El usuario no existe en la base de datos.");
}

// Obtener IDs del carrito
$ids = array_keys($_SESSION["carrito"]);
$ids_str = implode(",", $ids);

// Obtener productos
$sql = "SELECT id, precio FROM productos WHERE id IN ($ids_str)";
$res = $conexion->query($sql);

$total = 0;
$productos = [];

while ($p = $res->fetch_assoc()) {
    $id = $p["id"];
    $cantidad = $_SESSION["carrito"][$id];
    $subtotal = $cantidad * $p["precio"];

    $productos[] = [
        "id" => $id,
        "cantidad" => $cantidad,
        "subtotal" => $subtotal
    ];

    $total += $subtotal;
}

if ($total <= 0) {
    die("ERROR: El total del pedido no es válido.");
}

// Insertar pedido
$sqlPedido = "
    INSERT INTO pedidos (id_usuario, fecha_pedido, costo_total)
    VALUES ($usuario_id, NOW(), $total)
";

$conexion->query($sqlPedido);

// ID del nuevo pedido
$id_pedido = $conexion->insert_id;

// Insertar detalles
foreach ($productos as $p) {
    $sqlDetalle = "
        INSERT INTO detalles_pedidos (id_pedido, id_producto, cantidad, precio_cantidad)
        VALUES ($id_pedido, {$p['id']}, {$p['cantidad']}, {$p['subtotal']})
    ";
    $conexion->query($sqlDetalle);
}

// Vaciar carrito
unset($_SESSION["carrito"]);

// Redirigir
header("Location: ../pedidos.php");
exit;
?>
