<?php
session_start();

// Validar si hay usuario logeado
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION["usuario_id"];

// Validar si viene el ID del pedido
if (!isset($_GET["id"])) {
    echo "Pedido no especificado.";
    exit;
}

$id_pedido = intval($_GET["id"]);

// Conexión a la BD
$conexion = new mysqli("localhost", "root", "", "base_3bs");
if ($conexion->connect_error) {
    die("Error en la conexión: " . $conexion->connect_error);
}

// Consultar el pedido (ahora incluye el estado)
$sqlPedido = "
    SELECT id, fecha_pedido, costo_total, estado
    FROM pedidos
    WHERE id = $id_pedido AND id_usuario = $usuario_id
";

$resPedido = $conexion->query($sqlPedido);

if ($resPedido->num_rows == 0) {
    echo "No tienes permiso para ver este pedido.";
    exit;
}

$pedido = $resPedido->fetch_assoc();

// Obtener productos del pedido
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
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detalle del Pedido #<?= $pedido["id"] ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --color-primario: #8e24aa;
            --color-secundario: #ec407a;
            --color-fondo: #f8f9fa;
            --color-texto: #333;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
        }

        body {
            background: white;
            min-height: 100vh;
            font-family: 'Poppins', sans-serif;
            display: flex;
            flex-direction: column;
        }

        footer {
            background-color: var(--color-primario);
            color: white;
            text-align: center;
            padding: 20px 0;
            margin-top: auto;
        }

        footer p {
            margin: 0;
            font-size: 0.95rem;
        }

        .main-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }

        .back-button {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border: 2px solid var(--color-primario);
            color: var(--color-primario);
            padding: 0.6rem 1.5rem;
            border-radius: 50px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
            margin-bottom: 1.5rem;
        }

        .back-button:hover {
            background: var(--color-primario);
            color: white;
            transform: translateX(-5px);
        }

        .order-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            animation: slideUp 0.5s ease;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .order-header {
            background: linear-gradient(135deg, var(--color-primario) 0%, var(--color-secundario) 100%);
            color: white;
            padding: 2rem;
        }

        .order-header h3 {
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .order-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .order-date {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.95rem;
            opacity: 0.9;
        }

        .status-badge {
            padding: 0.5rem 1.2rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .status-pendiente {
            background: var(--warning-color);
            color: white;
        }

        .status-entregado {
            background: var(--success-color);
            color: white;
        }

        .status-cancelado {
            background: var(--danger-color);
            color: white;
        }

        .order-body {
            padding: 2rem;
        }

        .section-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--color-primario);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .product-item {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 1.2rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 1.5rem;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .product-item:hover {
            transform: translateX(5px);
            border-color: var(--color-primario);
            box-shadow: 0 5px 15px rgba(142, 36, 170, 0.2);
        }

        .product-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .product-details {
            flex: 1;
        }

        .product-name {
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--color-primario);
            margin-bottom: 0.5rem;
        }

        .product-info {
            display: flex;
            gap: 1.5rem;
            color: #6c757d;
            font-size: 0.95rem;
        }

        .product-info span {
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        .order-footer {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 1.5rem 2rem;
            border-top: 2px solid #dee2e6;
        }

        .total-amount {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 1.5rem;
        }

        .total-label {
            color: var(--color-primario);
            font-weight: 600;
        }

        .total-value {
            color: var(--color-secundario);
            font-weight: 800;
        }

        @media (max-width: 768px) {
            .product-item {
                flex-direction: column;
                text-align: center;
            }

            .product-image {
                width: 80px;
                height: 80px;
            }

            .product-info {
                flex-direction: column;
                gap: 0.5rem;
            }

            .order-info {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>

<body>

<div class="main-container">

    <a href="pedidos.php" class="back-button">
        <i class="fas fa-arrow-left"></i> Regresar a mis pedidos
    </a>

    <div class="order-card">
        <div class="order-header">
            <h3>
                <i class="fas fa-receipt"></i>
                Pedido #<?= $pedido["id"] ?>
            </h3>
            
            <div class="order-info">
                <div class="order-date">
                    <i class="fas fa-calendar-alt"></i>
                    <?= date('d/m/Y H:i', strtotime($pedido["fecha_pedido"])) ?>
                </div>

                <span class="status-badge status-<?= strtolower($pedido['estado']) ?>">
                    <i class="fas fa-circle"></i>
                    <?= $pedido["estado"] ?>
                </span>
            </div>
        </div>

        <div class="order-body">
            <h4 class="section-title">
                <i class="fas fa-box-open"></i>
                Productos del pedido
            </h4>

            <?php foreach ($items as $item): ?>
                <div class="product-item">
                    <img src="../../public/img/<?= $item["imagen"] ?>" 
                         alt="<?= $item["nombre"] ?>"
                         class="product-image">

                    <div class="product-details">
                        <div class="product-name"><?= $item["nombre"] ?></div>
                        <div class="product-info">
                            <span>
                                <i class="fas fa-cubes"></i>
                                Cantidad: <strong><?= $item["cantidad"] ?></strong>
                            </span>
                            <span>
                                <i class="fas fa-dollar-sign"></i>
                                Subtotal: <strong>$<?= number_format($item["precio_cantidad"], 2) ?></strong>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="order-footer">
            <div class="total-amount">
                <span class="total-label">
                    <i class="fas fa-money-bill-wave"></i>
                    Total pagado:
                </span>
                <span class="total-value">
                    $<?= number_format($pedido["costo_total"], 2) ?>
                </span>
            </div>
        </div>
    </div>
</div>

<footer>
    <div class="text-center py-3">
        <p>&copy; 2025 Tienda 3Bs. Todos los derechos reservados.</p>
        <p>Dirección: Calle Principal #123, Oxkutzcab, Yucatán | Tel: (999) 123-4567 | Email: contacto@3bs.com</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>