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
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../../../public/css/styles.css" rel="stylesheet">
</head>
<body class="bg-light">

<!-- NAVBAR -->
  <nav class="navbar navbar-expand-lg">
      <div class="container">
          <a class="navbar-brand d-flex align-items-center" href="indexSesionAdm.html">
              <img src="../../../public/Img/logo.png" alt="Logo 3Bs" width="40" height="40" class="me-2">
              <h1 class="text-uppercase fw-bold" style="color: var(--color-secundario);">Tienda 3Bs</h1>
          </a>

          <button class="navbar-toggler bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
              <span class="navbar-toggler-icon"></span>
          </button>

          <div class="collapse navbar-collapse" id="navbarMenu">
              <ul class="navbar-nav ms-auto align-items-lg-center">
                  <li class="nav-item"><a class="nav-link" href="indexSesionAdm.html">Inicio</a></li>
                  <li class="nav-item"><a class="nav-link" href="administrar.html">Administrar</a></li>
                  <li class="nav-item">
                      <a href="perfilAdm.php" class="nav-session" title="Perfil de Usuario">
                          <i class="bi bi-person-circle"></i>
                      </a>
                  </li>
              </ul>
          </div>
      </div>
  </nav>

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
        <div class="d-flex justify-content-between">
            <a href="administrar.html" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>
</div>
<!-- FOOTER -->
  <footer>
    <div class="container text-center py-3">
      <p>&copy; 2025 Tienda 3Bs. Todos los derechos reservados.</p>
      <p>Dirección: Calle Principal #123, Oxkutzcab, Yucatán | Tel: (999) 123-4567 | Email: contacto@3bs.com</p>
    </div>
  </footer>
</body>
</html>
