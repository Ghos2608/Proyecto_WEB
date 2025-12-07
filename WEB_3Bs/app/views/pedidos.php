<?php
session_start();

// Validar si hay usuario logeado
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION["usuario_id"];

// Conexión a la BD
$conexion = new mysqli("localhost", "root", "", "base_3bs");
if ($conexion->connect_error) {
    die("Error en la conexión: " . $conexion->connect_error);
}

// 1. Obtener los pedidos del usuario
$sqlPedidos = "
    SELECT id, fecha_pedido, costo_total
    FROM pedidos
    WHERE id_usuario = $usuario_id
    ORDER BY fecha_pedido DESC
";

$resPedidos = $conexion->query($sqlPedidos);
$pedidos = [];

// 2. Recorrer los pedidos y obtener sus productos
while ($p = $resPedidos->fetch_assoc()) {

    $id_pedido = $p["id"];

    $sqlItems = "
        SELECT dp.cantidad, dp.precio_cantidad, pr.nombre, pr.imagen
        FROM detalles_pedidos dp
        INNER JOIN productos pr ON dp.id_producto = pr.id
        WHERE dp.id_pedido = $id_pedido
    ";

    $resItems = $conexion->query($sqlItems);

    $items = [];
    while ($i = $resItems->fetch_assoc()) {
        $items[] = $i;
    }

    $p["items"] = $items;
    $pedidos[] = $p;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mis Pedidos - Tienda 3Bs</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Estilos personalizados -->
    <link href="../../public/css/styles.css" rel="stylesheet">
</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="indexSesion.html">
                <img src="../../public/img/logo.png" alt="Logo 3Bs" width="40" height="40" class="me-2">
                <h1 class="text-uppercase fw-bold" style="color: var(--color-secundario);">Tienda 3Bs</h1>
            </a>

            <button class="navbar-toggler bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarMenu">
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <li class="nav-item"><a class="nav-link" href="indexSesion.html">Inicio</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Mujer</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="../views/mujer/casual.php">Ropa Casual</a></li>
                            <li><a class="dropdown-item" href="../views/mujer/deportiva.php">Ropa Deportiva</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Hombre</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="../views/hombre/CamisaH.php">Camisas</a></li>
                            <li><a class="dropdown-item" href="../views/hombre/PantalonesH.php">Pantalones</a></li>
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="../views/carrito/carrito.php">Carrito</a></li>
                    <li class="nav-item">
                    <li class="nav-item"><a class="nav-link active" href="pedidos.php">Pedidos</a></li>
                    <li class="nav-item">
                    <li class="nav-item"><a class="nav-link" href="informacionSesion.html">Información</a></li>
                    <li class="nav-item">
                        <a href="perfil.php" class="nav-session" title="Perfil de Usuario">
                            <i class="bi bi-person-circle"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Encabezado -->
    <header class="py-5 text-center bg-light shadow-sm">
        <h1 class="fw-bold text-dark">Mis Pedidos</h1>
        <p class="text-muted">Consulta el estado y detalles de tus compras</p>
    </header>

    <!-- Contenido principal -->
    <main class="container my-5">
        <div class="row gy-4">

            <?php if (empty($pedidos)): ?>

                <div class="col-12 text-center">
                    <i class="bi bi-box-seam display-1 text-secondary"></i>
                    <p class="text-muted mt-3">Aún no tienes pedidos realizados.</p>
                </div>

            <?php else: ?>

                <?php foreach ($pedidos as $pedido): ?>
                    <div class="col-12">
                        <div class="card shadow-sm pedido-card">

                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="card-title mb-0">Pedido #<?= $pedido["id"] ?></h5>
                                    <span class="badge bg-dark"><?= $pedido["fecha_pedido"] ?></span>
                                </div>

                                <p class="text-muted mb-1"><strong>Total:</strong> $<?= $pedido["costo_total"] ?></p>

                                <hr>
                                <h6 class="fw-bold">Artículos:</h6>

                                <ul class="list-unstyled mb-3">
                                    <?php foreach ($pedido["items"] as $item): ?>
                                        <li class="d-flex align-items-center mb-2">
                                            <img src="../../public/img/<?= $item["imagen"] ?>" 
                                                 width="60" height="60"
                                                 class="me-3" 
                                                 style="object-fit: cover; border-radius: 8px;">

                                            <div>
                                                <p class="mb-0 fw-semibold"><?= $item["nombre"] ?></p>
                                                <small class="text-muted">
                                                    Cantidad: <?= $item["cantidad"] ?> |
                                                    Precio: $<?= $item["precio_cantidad"] ?>
                                                </small>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>

                                <div class="text-end">
                                    <a href="detalle_pedido.php?id=<?= $pedido['id'] ?>" 
                                        class="btn btn-outline-dark btn-sm">
                                        <i class="bi bi-eye"></i> Ver detalles
                                    </a>
                                </div>
                            </div>

                        </div>
                    </div>
                <?php endforeach; ?>

            <?php endif; ?>

        </div>
    </main>

    <!-- Footer -->
    <footer>
        <div class="text-center py-3">
            <p>&copy; 2025 Tienda 3Bs. Todos los derechos reservados.</p>
            <p>Dirección: Calle Principal #123, Oxkutzcab, Yucatán | Tel: (999) 123-4567 | Email: contacto@3bs.com</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

