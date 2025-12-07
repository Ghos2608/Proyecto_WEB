<?php
session_start();

// Eliminar todo el carrito
unset($_SESSION["carrito"]);

header("Location: " . $_SERVER["HTTP_REFERER"]);
exit;
