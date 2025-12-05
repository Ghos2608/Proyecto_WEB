<?php
require_once "../../controllers/ProductosController.php";

$controller = new ProductosController();
$mensaje = "";

// Validar ID recibido
if (!isset($_GET["id"])) {
    header("Location: productos.php?msg=error_id");
    exit();
}

$id = $_GET["id"];
$producto = $controller->getById($id);

// Validar que exista
if (!$producto) {
    header("Location: productos.php?msg=no_encontrado");
    exit();
}

// Si se confirma la eliminación
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if ($controller->delete($id)) {
        header("Location: productos.php?msg=eliminado");
        exit();
    } else {
        $mensaje = "Error al eliminar el producto.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Eliminar Producto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container my-5">

    <h2 class="fw-bold mb-4 text-center text-danger">Eliminar Producto</h2>

    <div class="card shadow-sm border-danger">
        <div class="card-body">

            <?php if ($mensaje): ?>
                <div class="alert alert-danger"><?= $mensaje ?></div>
            <?php endif; ?>

            <div class="alert alert-warning">
                <strong>¿Estás seguro?</strong><br>
                Estás a punto de eliminar el siguiente producto:
            </div>

            <ul class="list-group mb-3">
                <li class="list-group-item"><strong>Nombre:</strong> <?= $producto["nombre"] ?></li>
                <li class="list-group-item"><strong>Categoría:</strong> <?= $producto["categoria"] ?></li>
                <li class="list-group-item"><strong>Precio:</strong> $<?= $producto["precio"] ?></li>
                <li class="list-group-item"><strong>Stock:</strong> <?= $producto["cantidad"] ?></li>
            </ul>

            <form method="POST" class="d-flex gap-3">
                <button class="btn btn-danger w-50">Eliminar</button>
                <a href="productos.php" class="btn btn-secondary w-50">Cancelar</a>
            </form>

        </div>
    </div>

</div>

</body>
</html>
