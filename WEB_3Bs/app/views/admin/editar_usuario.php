<?php
require_once "../../controllers/UsuariosController.php";

$controller = new UsuariosController();

if (!isset($_GET["id"])) {
    header("Location: administrar_usuarios.php");
    exit;
}

$id = $_GET["id"];
$usuario = $controller->getById($id);

if (!$usuario) {
    die("Usuario no encontrado");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $controller->update($id, $_POST);
    header("Location: administrar_usuarios.php?msg=updated");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../../public/css/styles.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container my-5">
    <div class="col-lg-6 mx-auto">

        <div class="card shadow-sm border-0">
            <div class="card-body p-4">

                <h3 class="fw-bold text-center mb-4" style="color: var(--color-secundario);">
                    <i class="bi bi-pencil-square"></i> Editar Usuario
                </h3>

                <form method="POST">

                    <!-- Nombre -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombre</label>
                        <input type="text" name="nombre" class="form-control" required value="<?= $usuario['nombre'] ?>">
                    </div>

                    <!-- Dirección -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Dirección</label>
                        <input type="text" name="direccion" class="form-control" value="<?= $usuario['direccion'] ?>">
                    </div>

                    <!-- Teléfono -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Teléfono</label>
                        <input type="text" name="telefono" class="form-control" value="<?= $usuario['telefono'] ?>">
                    </div>

                    <!-- Correo -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Correo</label>
                        <input type="email" name="correo" class="form-control" required value="<?= $usuario['correo'] ?>">
                    </div>

                    <!-- Contraseña -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nueva Contraseña (opcional)</label>
                        <input type="password" name="contraseña" class="form-control">
                        <small class="text-muted">Déjala vacía si no quieres cambiarla.</small>
                    </div>

                    <!-- Rol -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Rol</label>
                        <select name="rol" class="form-select" required>
                            <option value="cliente" <?= $usuario['rol'] === 'cliente' ? 'selected' : '' ?>>Cliente</option>
                            <option value="dueño" <?= $usuario['rol'] === 'dueño' ? 'selected' : '' ?>>Dueño</option>
                            <option value="admin" <?= $usuario['rol'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="administrar_usuarios.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Volver
                        </a>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Guardar Cambios
                        </button>
                    </div>

                </form>

            </div>
        </div>

    </div>
</div>

</body>
</html>
