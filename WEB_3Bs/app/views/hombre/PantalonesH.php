<?php
// ---- CONEXIÓN A LA BD ----
include_once "../../helpers/Conexion.php";

$db = new Conexion();
$conexion = $db->conectar();

// Consulta: obtener productos de la categoría Pantalones (Hombre)
$query = "SELECT id, nombre, descripcion, precio, imagen 
          FROM productos 
          WHERE categoria = 'Pantalones' AND genero = 'Hombre'";
$resultado = $conexion->query($query);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tienda de Ropa 3Bs - Pantalones Hombre</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Estilos personalizados -->
    <link href="../../../public/css/styles.css" rel="stylesheet">
</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="../indexSesion.html">
                <img src="../../../public/img/logo.png" alt="Logo 3Bs" width="40" height="40" class="me-2">
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
                        <a class="nav-link dropdown-toggle active" href="#" data-bs-toggle="dropdown">Hombre</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="CamisaH.php">Camisas</a></li>
                            <li><a class="dropdown-item active" href="PantalonesH.php">Pantalones</a></li>
                        </ul>
                    </li>
            
                    <li class="nav-item"><a class="nav-link" href="../carrito/carrito.php">Carrito</a></li>
                    <li class="nav-item"><a class="nav-link" href="../pedidos.php">Pedidos</a></li>
                    <li class="nav-item"><a class="nav-link" href="../informacionSesion.html">Información</a></li>

                    <li class="nav-item">
                        <a href="../perfil.php" class="nav-session" title="Perfil de Usuario">
                            <i class="bi bi-person-circle"></i>
                        </a>
                    </li>

                </ul>
            </div>
        </div>
    </nav>

    <!-- Título -->
    <header class="py-5 text-center bg-light shadow-sm">
        <h1 class="fw-bold text-dark">Pantalones para Hombre</h1>
        <p class="text-muted">Descubre pantalones de excelente calidad y precio accesible</p>
    </header>

    <!-- Catálogo -->
    <div class="container my-5">
        <div class="row">

            <?php
            $productos = $resultado->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($productos)) {
                foreach ($productos as $row) {
                    echo '
                    <div class="col-md-4 col-lg-3 mb-4">
                        <div class="card h-100 text-center shadow-sm">

                            <img src="../../../public/img/' . $row['imagen'] . '" 
                                 class="card-img-top" 
                                 alt="' . $row['nombre'] . '">

                            <div class="card-body">
                                <h5 class="card-title">' . $row['nombre'] . '</h5>

                                <p class="card-text text-muted">
                                    ' . $row['descripcion'] . '
                                </p>

                                <p class="fw-bold text-primary">$' . $row['precio'] . '</p>

                                <!-- BOTÓN AGREGAR AL CARRITO -->
                                <a href="../carrito/agregar_carrito.php?id=' . $row['id'] . '" 
                                   class="btn btn-outline-dark btn-sm">
                                   <i class="bi bi-cart-plus"></i> Comprar
                                </a>
                            </div>

                        </div>
                    </div>
                    ';
                }
            } else {
                echo "<p class='text-center text-muted'>No hay productos disponibles.</p>";
            }
            ?>

        </div>
    </div>

    <!-- FOOTER -->
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

<php?>
