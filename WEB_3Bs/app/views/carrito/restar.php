<?php
session_start();

$id = $_GET["id"];

if (isset($_SESSION["carrito"][$id])) {

    // Si ya está en 1, NO disminuir más
    if ($_SESSION["carrito"][$id] > 1) {
        $_SESSION["carrito"][$id]--;
    }
}

header("Location: " . $_SERVER["HTTP_REFERER"]);
exit;

