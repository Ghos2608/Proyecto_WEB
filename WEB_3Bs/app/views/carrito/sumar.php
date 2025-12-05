<?php
session_start();

$id = $_GET["id"];

if (isset($_SESSION["carrito"][$id])) {
    $_SESSION["carrito"][$id]++;
}

header("Location: " . $_SERVER["HTTP_REFERER"]);
exit;
