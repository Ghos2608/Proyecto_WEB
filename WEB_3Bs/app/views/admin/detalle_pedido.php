<?php
require_once "../../controllers/PedidosController.php";

$controller = new PedidosController();

if (!isset($_GET['id'])) {
    header("Location: listar_pedidos.php");
    exit;
}

$id = $_GET['id'];
$pedido = $controller->getById($id);
$detalles = $controller->getDetalles($id);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle Pedido #<?= $id ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../../public/css/styles.css" rel="stylesheet">
</head>
<body class="bg-light">
  <!-- NAVBAR -->
  <nav class="navbar navbar-expand-lg">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center" href="indexSesionAdm.html">
        <img src="../../../public/Img/logo.png" width="40" class="me-2">
        <h1 class="text-uppercase fw-bold" style="color: var(--color-secundario);">Tienda 3Bs</h1>
      </a>
    </div>
  </nav>

<div class="container my-5">
    <h2 class="fw-bold mb-4 text-center">Detalle Pedido #<?= $pedido['id'] ?></h2>

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <p><strong>Cliente:</strong> <?= htmlspecialchars($pedido['cliente']) ?></p>
            <p><strong>Fecha:</strong> <?= $pedido['fecha_pedido'] ?></p>
            <p><strong>Estado:</strong> <?= $pedido['estado'] ?></p>
            <p><strong>Total:</strong> $<?= number_format($pedido['costo_total'],2) ?></p>
            <?php if (!empty($pedido['direccion'])): ?>
                <p><strong>Dirección:</strong> <?= htmlspecialchars($pedido['direccion']) ?></p>
            <?php endif; ?>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <h5>Productos</h5>
            <div class="table-responsive">
                <table class="table table-bordered text-center">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio total línea</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($detalles as $d): ?>
                            <tr>
                                <td><?= htmlspecialchars($d['producto']) ?></td>
                                <td><?= $d['cantidad'] ?></td>
                                <td>$<?= number_format($d['precio_cantidad'],2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <a href="listar_pedidos.php" class="btn btn-secondary mt-3">Volver</a>
        </div>
    </div>
</div>

</body>
</html>
