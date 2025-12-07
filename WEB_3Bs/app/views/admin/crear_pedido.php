<?php
require_once "../../controllers/PedidosController.php";

$controller = new PedidosController();
$productos = $controller->getProducts();
$clientes = $controller->getUsers();
$mensaje = "";

// Procesar POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $data = [
        "fecha_pedido" => $_POST['fecha_pedido'] ?? date('Y-m-d'),
        "id_usuario" => $_POST['id_usuario'],
        "estado" => $_POST['estado'] ?? 'Pendiente'
    ];

    // detalles arrays
    $detalles = [];
    if (!empty($_POST['producto_id']) && is_array($_POST['producto_id'])) {
        for ($i=0; $i < count($_POST['producto_id']); $i++) {
            $detalles[] = [
                'id_producto' => $_POST['producto_id'][$i],
                'cantidad' => intval($_POST['cantidad'][$i]),
                'precio_unitario' => floatval($_POST['precio_unitario'][$i])
            ];
        }
    }

    if (empty($detalles)) {
        $mensaje = "Agrega al menos un producto al pedido.";
    } else {
        if ($controller->insert($data, $detalles)) {
            header("Location: listar_pedidos.php?msg=creado");
            exit;
        } else {
            $mensaje = "Error al crear el pedido.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Pedido</title>
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
    <h2 class="fw-bold mb-4 text-center">Crear Pedido</h2>

    <?php if ($mensaje): ?>
        <div class="alert alert-danger"><?= $mensaje ?></div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">

            <form method="POST" id="pedidoForm">
                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Fecha</label>
                        <input type="date" name="fecha_pedido" class="form-control" value="<?= date('Y-m-d') ?>">
                    </div>

                    <div class="col-md-5">
                        <label class="form-label">Cliente</label>
                        <select name="id_usuario" class="form-select" required>
                            <option value="">Selecciona cliente</option>
                            <?php foreach ($clientes as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Estado</label>
                        <select name="estado" class="form-select">
                            <option value="Pendiente">Pendiente</option>
                            <option value="Entregado">Entregado</option>
                            <option value="Cancelado">Cancelado</option>
                        </select>
                    </div>
                </div>

                <!-- Agregar productos -->
                <div class="mb-3">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-6">
                            <label class="form-label">Producto</label>
                            <select id="selectProducto" class="form-select">
                                <option value="">Selecciona producto</option>
                                <?php foreach ($productos as $p): ?>
                                    <option data-precio="<?= $p['precio'] ?>" value="<?= $p['id'] ?>">
                                        <?= htmlspecialchars($p['nombre']) ?> — $<?= number_format($p['precio'],2) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Cantidad</label>
                            <input id="inputCantidad" type="number" min="1" value="1" class="form-control">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Precio unit.</label>
                            <input id="inputPrecio" type="number" step="0.01" class="form-control" readonly>
                        </div>

                        <div class="col-md-2 d-grid">
                            <button id="btnAgregar" type="button" class="btn btn-success">Agregar</button>
                        </div>
                    </div>
                </div>

                <!-- Tabla de detalles -->
                <div class="table-responsive mb-3">
                    <table class="table table-bordered" id="tablaDetalles">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio unit.</th>
                                <th>Subtotal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end mb-3">
                    <h4>Total: $<span id="total">0.00</span></h4>
                </div>

                <div class="d-flex justify-content-end">
                    <button class="btn btn-primary">Guardar Pedido</button>
                </div>
            </form>

            <a href="listar_pedidos.php" class="btn btn-secondary mt-3">Volver</a>

        </div>
    </div>
</div>

<script>
    // JS para manejar tabla dinámica
    const productos = <?php echo json_encode($productos); ?>;
    const selectProducto = document.getElementById('selectProducto');
    const inputPrecio = document.getElementById('inputPrecio');
    const inputCantidad = document.getElementById('inputCantidad');
    const btnAgregar = document.getElementById('btnAgregar');
    const tablaBody = document.querySelector('#tablaDetalles tbody');
    const totalSpan = document.getElementById('total');

    selectProducto.addEventListener('change', () => {
        const opt = selectProducto.selectedOptions[0];
        inputPrecio.value = opt ? opt.dataset.precio : '';
    });

    function actualizarTotal() {
        let total = 0;
        document.querySelectorAll('#tablaDetalles tbody tr').forEach(tr => {
            total += parseFloat(tr.querySelector('.subtotal').textContent);
        });
        totalSpan.textContent = total.toFixed(2);
    }

    btnAgregar.addEventListener('click', () => {
        const prodId = selectProducto.value;
        if (!prodId) return alert('Selecciona un producto');
        const prodName = selectProducto.selectedOptions[0].textContent;
        const cantidad = parseInt(inputCantidad.value) || 1;
        const precio = parseFloat(inputPrecio.value) || 0;
        const subtotal = +(cantidad * precio).toFixed(2);

        // crear fila con inputs ocultos para POST
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>
                ${prodName}
                <input type="hidden" name="producto_id[]" value="${prodId}">
            </td>
            <td>
                ${cantidad}
                <input type="hidden" name="cantidad[]" value="${cantidad}">
            </td>
            <td>
                ${precio.toFixed(2)}
                <input type="hidden" name="precio_unitario[]" value="${precio.toFixed(2)}">
            </td>
            <td class="subtotal">${subtotal.toFixed(2)}</td>
            <td><button type="button" class="btn btn-sm btn-danger btn-eliminar">Quitar</button></td>
        `;
        tablaBody.appendChild(tr);
        actualizarTotal();

        // eliminar
        tr.querySelector('.btn-eliminar').addEventListener('click', () => {
            tr.remove();
            actualizarTotal();
        });
    });

    // calcular total al enviar (ya calculado en servidor también)
    document.getElementById('pedidoForm').addEventListener('submit', (e) => {
        if (!document.querySelectorAll('#tablaDetalles tbody tr').length) {
            e.preventDefault();
            alert('Agrega al menos un producto.');
        }
    });

</script>
</body>
</html>
