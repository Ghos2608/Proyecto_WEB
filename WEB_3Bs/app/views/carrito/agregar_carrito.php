<?php
session_start();

// Obtener el id del producto enviado por la URL
$id_producto = $_GET["id"];

// Si el carrito NO existe, lo creamos
if (!isset($_SESSION["carrito"])) {
    $_SESSION["carrito"] = [];
}

// Si el producto NO está en el carrito, lo agregamos con cantidad 1
if (!isset($_SESSION["carrito"][$id_producto])) {
    $_SESSION["carrito"][$id_producto] = 1;
} else {
    // Si ya existe, aumentamos la cantidad
    $_SESSION["carrito"][$id_producto]++;
}

// Regresar a la página anterior automáticamente
header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
