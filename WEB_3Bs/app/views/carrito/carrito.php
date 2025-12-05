<?php
session_start();

// Validar si el usuario está logeado
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION["usuario_id"];

// Si no existe el carrito, crearlo vacío
if (!isset($_SESSION["carrito"])) {
    $_SESSION["carrito"] = [];
}

// Conexión BD
$conexion = new mysqli("localhost", "root", "", "base_3bs");
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Obtener IDs del carrito
$ids = array_keys($_SESSION["carrito"]);
$productos = [];

if (!empty($ids)) {
    $ids_str = implode(",", $ids);
    $consulta = "SELECT * FROM productos WHERE id IN ($ids_str)";
    $resultado = $conexion->query($consulta);

    while ($row = $resultado->fetch_assoc()) {
        $productos[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Carrito - Tienda 3Bs</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <link href="../../../public/css/styles.css" rel="stylesheet">
</head>

<body>

    <!-- NAVBAR (copiado de pedidos.php) -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="../indexSesion.html">
                <img src="../../../public/img/logo.png" width="40" height="40" class="me-2">
                <h1 class="text-uppercase fw-bold" style="color: var(--color-secundario);">Tienda 3Bs</h1>
            </a>

            <button class="navbar-toggler bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarMenu">
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <li class="nav-item"><a class="nav-link" href="../indexSesion.html">Inicio</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Mujer</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="../mujer/casual.php">Ropa Casual</a></li>
                            <li><a class="dropdown-item" href="../mujer/deportiva.php">Ropa Deportiva</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Hombre</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="../hombre/CamisaH.php">Camisas</a></li>
                            <li><a class="dropdown-item" href="../hombre/PantalonesH.php">Pantalones</a></li>
                        </ul>
                    </li>
                     <li class="nav-item"><a class="nav-link active" href="../views/carrito/carrito.php">Carrito</a></li>
                    <li class="nav-item">
                    <li class="nav-item"><a class="nav-link" href="../pedidos.php">Pedidos</a></li>
                    <li class="nav-item">
                    <li class="nav-item"><a class="nav-link" href="../informacionSesion.html">Información</a></li>
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
        <h1 class="fw-bold text-dark">Mi Carrito</h1>
        <p class="text-muted">Revisa tus artículos antes de procesar tu compra</p>
    </header>

    <!-- Contenido -->
    <main class="container my-5">
        
        <?php if (empty($productos)): ?>
            <div class="text-center">
                <i class="bi bi-cart-x display-1 text-secondary"></i>
                <p class="mt-3 text-muted">Tu carrito está vacío.</p>
            </div>
        <?php else: ?>

            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Imagen</th>
                            <th>Producto</th>
                            <th>Precio</th>
                            <th>Cantidad</th>
                            <th>Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        $total = 0;
                        foreach ($productos as $p):
                            $id = $p["id"];
                            $cantidad = $_SESSION["carrito"][$id];
                            $subtotal = $cantidad * $p["precio"];
                            $total += $subtotal;
                        ?>
                            <tr>
                                <td><img src="../../../public/img/<?= $p["imagen"] ?>" width="70"></td>
                                <td><?= $p["nombre"] ?></td>
                                <td>$<?= $p["precio"] ?></td>

                                <td>
                                    <a href="sumar.php?id=<?= $id ?>" class="btn btn-sm btn-success">+</a>
                                    <span class="mx-2"><?= $cantidad ?></span>
                                    <a href="restar.php?id=<?= $id ?>" class="btn btn-sm btn-warning">−</a>
                                </td>

                                <td>$<?= number_format($subtotal, 2) ?></td>

                                <td>
                                    <a href="eliminar.php?id=<?= $id ?>" class="btn btn-danger btn-sm">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="text-end">
                <h3>Total: $<?= number_format($total, 2) ?></h3>

                <a href="realizar_pedido.php" class="btn btn-primary btn-lg mt-3">
                    <i class="bi bi-check-circle"></i> Realizar Pedido
                </a>
            </div>

        <?php endif; ?>
    </main>

    <!-- Footer -->
    <footer>
        <div class="text-center py-3">
            <p>&copy; 2025 Tienda 3Bs. Todos los derechos reservados.</p>
            <p>Dirección: Calle Principal #123, Oxkutzcab, Yucatán</p>
        </div>
    </footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
