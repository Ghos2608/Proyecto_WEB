<?php
session_start();

// Eliminar todas las variables de sesión
$_SESSION = [];

// Destruir la sesión completamente
session_destroy();


header("Location: ../../index.html");
exit;