<?php
session_start();

$id = $_GET["id"];

// Conexión BD
include_once "../../helpers/Conexion.php";

$db = new Conexion();
$conexion = $db->conectar();

// Obtener stock del producto
$sql = "SELECT cantidad FROM inventario WHERE id_producto = $id";
$res = $conexion->query($sql);
$inv = $res->fetch(PDO::FETCH_ASSOC);

$stockDisponible = $inv["cantidad"];

// Cantidad actual en el carrito
$cantidadActual = isset($_SESSION["carrito"][$id]) ? $_SESSION["carrito"][$id] : 0;

// Validar stock
if ($cantidadActual < $stockDisponible) {
    // Aumentar si aún hay stock disponible
    $_SESSION["carrito"][$id] = $cantidadActual + 1;
} else {
    // Crear alerta temporal en SESSION
    $_SESSION["error_stock"] = "No puedes agregar más unidades. Stock disponible: $stockDisponible";
}

// Regresar a la misma página
header("Location: " . $_SERVER["HTTP_REFERER"]);
exit;
?>
