<?php
require_once "../../controllers/UsuariosController.php";

$usuarios = new UsuariosController();
$data = $usuarios->getAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administrar Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container my-5">

    <h2 class="fw-bold mb-4 text-center">Administración de Usuarios</h2>

    <a href="crear_usuario.php" class="btn btn-primary mb-3">
        <i class="bi bi-person-plus"></i> Nuevo Usuario
    </a>

    <div class="card shadow-sm">
        <div class="card-body">

            <table class="table table-striped text-center">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Teléfono</th>
                        <th>Rol</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                <?php foreach ($data as $u): ?>
                    <tr>
                        <td><?= $u['id']; ?></td>
                        <td><?= $u['nombre']; ?></td>
                        <td><?= $u['correo']; ?></td>
                        <td><?= $u['telefono']; ?></td>
                        <td><?= ucfirst($u['rol']); ?></td>

                        <td>
                            <a href="editar_usuario.php?id=<?= $u['id']; ?>" class="btn btn-warning btn-sm">Editar</a>

                            <a href="eliminar_usuario.php?id=<?= $u['id']; ?>" 
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('¿Eliminar usuario?');">
                               Eliminar
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>

            </table>

        </div>
    </div>
</div>

</body>
</html>
