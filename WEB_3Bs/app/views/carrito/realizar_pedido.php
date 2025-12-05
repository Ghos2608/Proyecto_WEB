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

// Obtener IDs de productos del carrito
$ids = array_keys($_SESSION["carrito"]);
$ids_str = implode(",", $ids);

// Obtener productos para calcular total
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

// Insertar pedido en la tabla pedidos
$sqlPedido = "
    INSERT INTO pedidos (id_usuario, fecha_pedido, costo_total)
    VALUES ($usuario_id, NOW(), $total)
";

$conexion->query($sqlPedido);

// ID del pedido creado
$id_pedido = $conexion->insert_id;

// Insertar detalles del pedido
foreach ($productos as $p) {
    $id_prod = $p["id"];
    $cantidad = $p["cantidad"];
    $subtotal = $p["subtotal"];

    $sqlDetalle = "
        INSERT INTO detalles_pedidos (id_pedido, id_producto, cantidad, precio_cantidad)
        VALUES ($id_pedido, $id_prod, $cantidad, $subtotal)
    ";

    $conexion->query($sqlDetalle);
}

// Vaciar carrito
unset($_SESSION["carrito"]);

// Redirigir a pedidos.php
header("Location: ../pedidos.php");
exit;
?>
