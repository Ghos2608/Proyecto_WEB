<?php
require_once "../../controllers/PedidosController.php";

$controller = new PedidosController();

$filtros = [
    "cliente" => $_GET['cliente'] ?? "",
    "estado" => $_GET['estado'] ?? "",
    "fecha_inicio" => $_GET['fecha_inicio'] ?? "",
    "fecha_fin" => $_GET['fecha_fin'] ?? ""
];

$data = $controller->getAll($filtros);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pedidos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
    <h2 class="fw-bold mb-4 text-center">Administración de Pedidos</h2>

    <a href="crear_pedido.php" class="btn btn-primary mb-3">Nuevo Pedido</a>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="cliente" class="form-control" placeholder="Cliente" value="<?= htmlspecialchars($filtros['cliente']) ?>">
                </div>
                <div class="col-md-2">
                    <select name="estado" class="form-select">
                        <option value="">Estado</option>
                        <option value="Pendiente" <?= $filtros['estado']=="Pendiente" ? "selected" : "" ?>>Pendiente</option>
                        <option value="Entregado" <?= $filtros['estado']=="Entregado" ? "selected" : "" ?>>Entregado</option>
                        <option value="Cancelado" <?= $filtros['estado']=="Cancelado" ? "selected" : "" ?>>Cancelado</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="fecha_inicio" class="form-control" value="<?= $filtros['fecha_inicio'] ?>">
                </div>
                <div class="col-md-2">
                    <input type="date" name="fecha_fin" class="form-control" value="<?= $filtros['fecha_fin'] ?>">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-success w-100">Filtrar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div style="overflow-x:auto; max-height:450px; overflow-y:auto;">
                <table class="table table-striped text-center">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($data)): ?>
                            <tr><td colspan="6">No hay pedidos.</td></tr>
                        <?php endif; ?>

                        <?php foreach ($data as $row): ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><?= $row['fecha_pedido'] ?></td>
                                <td><?= htmlspecialchars($row['cliente']) ?></td>
                                <td>$<?= number_format($row['costo_total'], 2) ?></td>
                                <td>
                                    <span class="badge bg-<?=
                                        $row['estado']=="Pendiente" ? "warning" :
                                        ($row['estado']=="Entregado" ? "success" : "danger")
                                    ?>">
                                        <?= $row['estado'] ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="detalle_pedido.php?id=<?= $row['id'] ?>" class="btn btn-info btn-sm">Ver</a>
                                    <a href="editar_pedido.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
                                    <a href="../../controllers/PedidosController.php?action=delete&id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar pedido?')">Eliminar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between">
        <a href="administrar.html" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Volver</a>
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
