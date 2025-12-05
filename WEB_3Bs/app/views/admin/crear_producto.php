<?php
require_once "../../controllers/ProductosController.php";

$controller = new ProductosController();
$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Datos del producto
    $data = [
        "nombre"      => $_POST["nombre"],
        "descripcion" => $_POST["descripcion"],
        "categoria"   => $_POST["categoria"],
        "genero"      => $_POST["genero"],
        "precio"      => $_POST["precio"],
    ];

    // Imagen
    if (!empty($_FILES["imagen"]["name"])) {
        $nombreImg = time() . "_" . basename($_FILES["imagen"]["name"]);
        $rutaDestino = "../../../public/img" . $nombreImg;

        if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $rutaDestino)) {
            $data["imagen"] = $nombreImg;
        } else {
            $mensaje = "Error al subir la imagen.";
        }
    }

    // Inventario
    $inventario = [
        "cantidad"  => $_POST["cantidad"],
        "comentario" => $_POST["comentario"]
    ];

    if ($controller->insert($data, $inventario)) {
        header("Location: productos.php?msg=creado");
        exit;
    } else {
        $mensaje = "Error al crear el producto.";
    }
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Producto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container my-5">

    <h2 class="fw-bold mb-4 text-center">Registrar Nuevo Producto</h2>

    <div class="card shadow-sm">
        <div class="card-body">

            <?php if ($mensaje): ?>
                <div class="alert alert-danger"><?= $mensaje ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="row g-3">

                <!-- Nombre -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Nombre</label>
                    <input type="text" name="nombre" class="form-control" required>
                </div>

                <!-- Categoría -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Categoría</label>
                    <input type="text" name="categoria" class="form-control" required>
                </div>

                <!-- Descripción -->
                <div class="col-md-12">
                    <label class="form-label fw-semibold">Descripción</label>
                    <textarea name="descripcion" class="form-control" rows="3"></textarea>
                </div>

                <!-- Género -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Género</label>
                    <select name="genero" class="form-control">
                        <option value="Hombre">Hombre</option>
                        <option value="Mujer">Mujer</option>
                    </select>
                </div>

                <!-- Precio -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Precio</label>
                    <input type="number" step="0.01" name="precio" class="form-control" required>
                </div>

                <!-- Cantidad Inventario -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Cantidad en Inventario</label>
                    <input type="number" name="cantidad" class="form-control" required>
                </div>

                <!-- Comentario Inventario -->
                <div class="col-md-12">
                    <label class="form-label fw-semibold">Comentario (Inventario)</label>
                    <textarea name="comentario" class="form-control"></textarea>
                </div>

                <!-- Imagen -->
                <div class="col-md-12">
                    <label class="form-label fw-semibold">Imagen</label>
                    <input type="file" name="imagen" class="form-control" required>
                </div>

                <!-- Botón -->
                <div class="col-md-12 d-flex justify-content-end">
                    <button class="btn btn-primary px-4">Guardar</button>
                </div>

            </form>

            <a href="productos.php" class="btn btn-secondary mt-3">Volver</a>

        </div>
    </div>

</div>

</body>
</html>
