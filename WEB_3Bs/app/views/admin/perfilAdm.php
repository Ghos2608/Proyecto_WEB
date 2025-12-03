<?php
session_start();

// Si no hay sesión, enviarlo al inicio
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// Valores desde la sesión
$nombre      = $_SESSION['usuario_nombre'];
$correo      = $_SESSION['usuario_correo'];
$telefono    = $_SESSION['usuario_telefono'] ?? "No registrado";
$direccion   = $_SESSION['usuario_direccion'] ?? "No registrada";
$rol         = $_SESSION['usuario_rol'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Mi Perfil - Tienda 3Bs</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="../../../public/css/styles.css" rel="stylesheet">
</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="indexSesionAdm.html">
                <img src="../../../public/img/logo.png" alt="Logo 3Bs" width="40" height="40" class="me-2">
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

  <!-- ENCABEZADO -->
  <header class="py-5 text-center bg-light shadow-sm">
    <h1 class="fw-bold" style="color: var(--color-primario);">Mi Perfil</h1>
    <p class="text-muted">Información general de tu cuenta</p>
  </header>

  <!-- CONTENIDO -->
  <main class="container my-5">
    <div class="row justify-content-center">
      <div class="col-md-8 col-lg-6">
        <div class="card shadow-sm border-0">
          <div class="card-body text-center">

            <!-- FOTO -->
            <img src="https://cdn-icons-png.flaticon.com/512/149/149071.png"
                 alt="Foto de perfil"
                 class="rounded-circle mb-3"
                 width="220" height="120">

            <!-- NOMBRE Y CORREO -->
            <h3 class="fw-bold mb-0" style="color: var(--color-primario);">
                <?= htmlspecialchars($nombre) ?>
            </h3>
            <p class="text-muted"><?= htmlspecialchars($correo) ?></p>

            <hr>

            <!-- INFORMACIÓN GENERAL -->
            <div class="text-start px-3">
              <p><i class="bi bi-person-circle me-2"></i>
                 <strong>Nombre completo:</strong> <?= htmlspecialchars($nombre) ?></p>

              <p><i class="bi bi-envelope me-2"></i>
                 <strong>Correo:</strong> <?= htmlspecialchars($correo) ?></p>

              <p><i class="bi bi-phone me-2"></i>
                 <strong>Teléfono:</strong> <?= htmlspecialchars($telefono) ?></p>

              <p><i class="bi bi-geo-alt me-2"></i>
                 <strong>Dirección:</strong> <?= htmlspecialchars($direccion) ?></p>

              <p><i class="bi bi-person-badge me-2"></i>
                 <strong>Rol:</strong> <?= htmlspecialchars($rol) ?></p>
            </div>

            <hr>

            <!-- BOTONES -->
            <div class="d-flex flex-column gap-2 px-3">
              <a href="#" class="btn btn-dark w-100">
                <i class="bi bi-pencil-square"></i> Editar perfil
              </a>

              <a href="../../controllers/logout.php" class="btn btn-outline-danger w-100">
                <i class="bi bi-box-arrow-right"></i> Cerrar sesión
              </a>
            </div>

          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- FOOTER -->
  <footer>
    <div class="container">
      <p>&copy; 2025 Tienda 3Bs. Todos los derechos reservados.</p>
      <p>Dirección: Calle Principal #123, Oxkutzcab, Yucatán | Tel: (999) 123-4567 | Email: contacto@3bs.com</p>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
