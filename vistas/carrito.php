<?php
session_start();
include '../includes/conexion.php';

if (!isset($_SESSION['carrito'])) {
  $_SESSION['carrito'] = [];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Carrito</title>
  <link rel="stylesheet" href="../css/carrito.css"> <!-- Opcional -->
</head>
<body>
  <h2>🛒 Mi Carrito</h2>

  <?php if (isset($_SESSION['mensaje_carrito'])): ?>
    <p style="color: green;"><?= $_SESSION['mensaje_carrito'] ?></p>
    <?php unset($_SESSION['mensaje_carrito']); ?>
  <?php endif; ?>

  <?php if (empty($_SESSION['carrito'])): ?>
    <div class="carrito-vacio">
  <i class="fas fa-shopping-cart"></i>
  <p>Tu carrito está vacío.</p>
  <p style="text-align:center;">
  <a href="../index.php" class="btn-volver">← Volver al inicio</a>
</p>
</div>

  <?php else: ?>
    <table>
      <tr>
        <th>Producto</th>
        <th>Precio</th>
        <th>Cantidad</th>
        <th>Total</th>
        <th>Acción</th>
      </tr>

      <?php
$total = 0;
foreach ($_SESSION['carrito'] as $id => $item):
  $subtotal = $item['precio'] * $item['cantidad'];
  $total += $subtotal;
?>
<tr>
  <td><?= htmlspecialchars($item['nombre']) ?></td>
  <td>$<?= number_format($item['precio'], 2) ?></td>
  <td>
    <form action="actualizar_carrito.php" method="POST" style="display:inline;">
      <input type="hidden" name="id_producto" value="<?= $id ?>">
      <input type="number" name="cantidad" value="<?= $item['cantidad'] ?>" min="1" max="99" style="width: 60px; padding: 4px;">
      <button type="submit">Actualizar</button>
    </form>
  </td>
  <td>$<?= number_format($subtotal, 2) ?></td>
  <td>
    <form action="eliminar_del_carrito.php" method="POST">
      <input type="hidden" name="id_producto" value="<?= $id ?>">
      <button type="submit">Eliminar</button>
    </form>
  </td>
</tr>
<?php endforeach; ?>

    </table>

    <!-- <h3>Total: $<?= number_format($total, 2) ?></h3> -->
    <div class="resumen-carrito">
  <h3>Total: $<?= number_format($total, 2) ?></h3>
  <a href="finalizar_compra.php" class="btn-finalizar">Finalizar compra</a>
  <br><br>
  <a href="../index.php" class="btn-volver">← Volver al inicio</a>
</div>

  <?php endif; ?>
</body>
</html>
