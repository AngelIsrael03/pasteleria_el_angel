<?php include('../includes/conexion.php'); ?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Agregar Producto</title>
  <link rel="stylesheet" href="../css/admin_productos.css">
</head>

<body>
 <a href="index.php" class="btn-volver">← Volver al inicio</a>
  <div class="form-container">
    <h2 style="text-align:center; margin-top: 30px;">Agregar Producto</h2>

    <form action="guardar_producto.php" method="POST" enctype="multipart/form-data" style="max-width: 600px; margin: auto;">
      <input type="text" name="nombre" placeholder="Nombre del producto" required><br><br>

      <textarea name="descripcion" placeholder="Descripción" rows="3" required></textarea><br><br>

      <input type="text" name="categoria" placeholder="Categoría" required><br><br>

      <input type="number" step="0.01" name="precio" placeholder="Precio" required><br><br>

      <input type="number" step="0.01" name="precio_promocion" placeholder="Precio promocional (opcional)"><br><br>


      <input type="number" name="stock" placeholder="Stock disponible" required><br><br>

      <label>Tipo de producto:</label>
      <p>Secciones del producto:</p>
      <?php
      require_once '../includes/conexion.php';
      $resultado = $conexion->query("SELECT * FROM Secciones");
      while ($seccion = $resultado->fetch_assoc()) {
        echo '<label><input type="checkbox" name="secciones[]" value="' . $seccion['id_seccion'] . '"> ' . ucfirst($seccion['nombre']) . '</label><br>';
      }
      ?>

      <script>
        function mostrarVistaPrevia(event) {
          const reader = new FileReader();
          reader.onload = function() {
            const output = document.getElementById('preview-img');
            output.src = reader.result;
            output.style.display = 'block';
          };
          reader.readAsDataURL(event.target.files[0]);
        }
      </script>


      <label>Imagen del producto:</label><br>
      <input type="file" name="imagen" accept="image/*" required onchange="mostrarVistaPrevia(event)"><br><br>
      <img id="preview-img" src="#" alt="Vista previa" style="display:none; max-width: 250px; border: 2px solid #ccc; border-radius: 10px; margin-top: 10px;">
      <br><br>


      <button type="submit">Guardar producto</button>
      <br><br>

    </form>
    <div>

</body>

</html>