<?php
require_once "../../controllers/PedidosController.php";

if (!isset($_GET['id'])) {
    die("ID de pedido no proporcionado.");
}

$id = intval($_GET['id']);

$controller = new PedidosController();
$result = $controller->delete($id);

if ($result) {
    echo "<script>
            alert('Pedido eliminado correctamente y el inventario ha sido restaurado.');
            window.location.href = 'pedidos.php';
          </script>";
} else {
    echo "<script>
            alert('Error al eliminar el pedido.');
            window.location.href = 'pedidos.php';
          </script>";
}
?>
