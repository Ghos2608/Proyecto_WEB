<?php
session_start();

$id = $_GET["id"];

if (isset($_SESSION["carrito"][$id])) {
    $_SESSION["carrito"][$id]--;

    // Si la cantidad llega a 0, eliminar producto
    if ($_SESSION["carrito"][$id] <= 0) {
        unset($_SESSION["carrito"][$id]);
    }
}

header("Location: " . $_SERVER["HTTP_REFERER"]);
exit;
