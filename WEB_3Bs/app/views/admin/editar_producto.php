<?php
require_once "../../controllers/ProductosController.php";

$controller = new ProductosController();
$mensaje = "";

// Verificar que venga ID
if (!isset($_GET["id"])) {
    header("Location: productos.php?msg=error_id");
    exit();
}

$id = $_GET["id"];
$producto = $controller->getById($id);

// Validar producto existente
if (!$producto) {
    header("Location: productos.php?msg=no_encontrado");
    exit();
}

// Si se envía el formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $data = [
        "id"          => $id,
        "nombre"      => $_POST["nombre"],
        "descripcion" => $_POST["descripcion"],
        "categoria"   => $_POST["categoria"],
        "precio"      => $_POST["precio"],
       "cantidad"     => $_POST["stock"] 
    ];

    if ($controller->update($data, $id)) {
        header("Location: productos.php?msg=actualizado");
        exit();
    } else {
        $mensaje = "Error al actualizar el producto.";
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Producto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container my-5">

    <h2 class="fw-bold mb-4 text-center">Editar Producto</h2>

    <div class="card shadow-sm">
        <div class="card-body">

            <?php if ($mensaje): ?>
                <div class="alert alert-danger"><?= $mensaje ?></div>
            <?php endif; ?>

            <form method="POST" class="row g-3">

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Nombre</label>
                    <input 
                        type="text"
                        name="nombre"
                        class="form-control"
                        value="<?= $producto['nombre'] ?>"
                        required
                    >
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Categoría</label>
                    <input 
                        type="text"
                        name="categoria"
                        class="form-control"
                        value="<?= $producto['categoria'] ?>"
                        required
                    >
                </div>

                <div class="col-md-12">
                    <label class="form-label fw-semibold">Descripción</label>
                    <textarea 
                        name="descripcion"
                        class="form-control"
                        rows="3"
                    ><?= $producto['descripcion'] ?></textarea>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Precio</label>
                    <input 
                        type="number"
                        step="0.01"
                        name="precio"
                        class="form-control"
                        value="<?= $producto['precio'] ?>"
                        required
                    >
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Stock (Inventario)</label>
                    <input 
                        type="number"
                        name="stock"
                        class="form-control"
                        value="<?= $producto['cantidad'] ?>"
                        required
                    >
                </div>

                <div class="col-md-4 d-flex align-items-end">
                    <button class="btn btn-primary w-100">Actualizar</button>
                </div>

            </form>

            <a href="productos.php" class="btn btn-secondary mt-3">Volver</a>

        </div>
    </div>

</div>

</body>
</html>
