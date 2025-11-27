<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Iniciar Sesión - Tienda 3Bs</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="../../../public/css/styles.css" rel="stylesheet">
</head>

<body class="bg-light">

<!-- NAVBAR -->
  <nav class="navbar navbar-expand-lg">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center" href="../../../index.html">
        <img src="../../../public/img/logo.png" alt="Logo 3Bs" width="40" height="40" class="me-2">
        <h1 class="text-uppercase fw-bold" style="color: var(--color-secundario);">Tienda 3Bs</h1>
      </a>

      <button class="navbar-toggler bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarMenu">
        <ul class="navbar-nav ms-auto align-items-lg-center">
          <li class="nav-item"><a class="nav-link active" href="../../../index.html">Inicio</a></li>
      
          <li class="nav-item"><a class="nav-link" href="../informacion.html">Información</a></li>

          <li class="nav-item">
            <a href="../auth/login.php" class="nav-session">
              <i class="bi bi-person-circle"></i>
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
  <div class="card shadow-lg border-0" style="width: 420px;">
    <div class="card-body p-4">

      <h2 class="text-center fw-bold mb-3" style="color: var(--color-primario);">
        Iniciar Sesión
      </h2>
      <p class="text-muted text-center mb-4">Accede a tu cuenta</p>

      <!-- FORMULARIO -->
      <form action="../../controllers/AuthController.php" method="POST">

        <input type="hidden" name="action" value="login">

        <div class="mb-3">
          <label class="form-label">Correo</label>
          <input type="email" name="correo" class="form-control" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Contraseña</label>
          <input type="password" name="password" class="form-control" required>
        </div>

        <button class="btn btn-dark w-100 py-2" type="submit">
          <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
        </button>
      </form>

      <p class="text-center mt-3">
        ¿No tienes cuenta?
        <a href="register.php">Regístrate</a>
      </p>

    </div>
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
