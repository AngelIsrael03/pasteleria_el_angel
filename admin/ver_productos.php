<?php
session_start();
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'administrador') {
  header("Location: ../index.php");
  exit;
}
include '../includes/conexion.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Ver Productos</title>
  <link rel="stylesheet" href="../css/admin_ver_productos.css">
</head>

<body>
  <a href="index.php" class="btn-volver">← Volver al inicio</a>
  <h2 class="titulo-admin">Gestión de Productos</h2>

  <div class="contenedor-productos-admin">
    <?php
    $sql = "SELECT * FROM Productos";
    $resultado = mysqli_query($conexion, $sql);

    while ($producto = mysqli_fetch_assoc($resultado)) {
    ?>
      <div class="tarjeta-admin">
        <img src="../ver_imagen.php?id=<?= $producto['id_producto'] ?>" alt="<?= htmlspecialchars($producto['nombre']) ?>" class="img-admin">
        <h3><?= htmlspecialchars($producto['nombre']) ?></h3>
        <p><?= htmlspecialchars($producto['descripcion']) ?></p>
        <p><strong>$<?= number_format($producto['precio'], 2) ?></strong></p>

        <!-- Mostrar precio promocional si existe -->
        <?php if (!empty($producto['precio_promocion'])): ?>
          <p>
            <span style="color: gray; text-decoration: line-through;">$<?= number_format($producto['precio'], 2) ?></span>
            <span style="color: #e53935; font-weight: bold;">$<?= number_format($producto['precio_promocion'], 2) ?></span>
          </p>
        <?php endif; ?>

        <!-- Formulario para actualizar precio promocional -->
        <form action="actualizar_promocion.php" method="POST" style="margin-top: 10px;" onsubmit="return confirmarCambioProducto();">
          <input type="hidden" name="id_producto" value="<?= $producto['id_producto'] ?>">
          <input type="number" step="0.01" name="precio_promocion" placeholder="Nuevo precio promocional" value="<?= $producto['precio_promocion'] ?>" required>
          <button type="submit" style="margin-top: 5px;">Actualizar promo</button>
        </form>

        <!-- Formulario para actualizar precio y stock -->
        <form action="actualizar_producto.php" method="POST" style="margin-top: 15px;" onsubmit="return confirmarCambioProducto();">
          <input type="hidden" name="id_producto" value="<?= $producto['id_producto'] ?>">

          <label for="precio">Precio normal:</label><br>
          <input type="number" step="0.01" name="precio" value="<?= $producto['precio'] ?>" required><br>

          <label for="stock">Stock:</label><br>
          <input type="number" name="stock" value="<?= $producto['stock'] ?>" required><br>

          <button type="submit" style="margin-top: 5px;">Actualizar producto</button>
        </form>

        <p><strong>Stock:</strong> <?= $producto['stock'] ?></p>
        <p><strong>Estado:</strong> <?= $producto['estado'] === 'activo' ? 'Disponible' : 'Inactivo' ?></p>


        <?php if ($producto['estado'] === 'activo'): ?>
          <form action="inactivar_producto.php" method="POST">
            <input type="hidden" name="id_producto" value="<?= $producto['id_producto'] ?>">
            <button class="btn-inactivar" onclick="return confirm('¿Estás seguro de inactivar este producto?')">Inactivar</button>
          </form>
        <?php else: ?>
          <form method="POST" action="activar_producto.php">
            <input type="hidden" name="id_producto" value="<?= $producto['id_producto'] ?>">
            <button class="btn-activar" onclick="return confirm('¿Deseas volver a activar este producto?')">Activar</button>
          </form>
        <?php endif; ?>


      </div>

    <?php } ?>
  </div>
  <br><br>
  <script>
    function confirmarCambioProducto() {
      return confirm("¿Estás seguro de que deseas aplicar estos cambios al producto?");
    }
  </script>

</body>

</html>