<?php
session_start();
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'administrador') {
  header("Location: ../index.php");
  exit;
}

include '../includes/conexion.php';
function obtenerProductosPorSeccion($conexion, $nombre_seccion)
{
  $stmt = $conexion->prepare("
        SELECT p.* 
        FROM Productos p
        INNER JOIN Producto_Seccion ps ON p.id_producto = ps.id_producto
        INNER JOIN Secciones s ON ps.id_seccion = s.id_seccion
        WHERE s.nombre = ? AND p.estado = 'activo' AND p.stock > 0
    ");
  $stmt->bind_param("s", $nombre_seccion);
  $stmt->execute();
  $resultado = $stmt->get_result();
  return $resultado->fetch_all(MYSQLI_ASSOC);
}

$sql_alertas = "SELECT COUNT(*) AS total FROM alertas_stock WHERE estado = 'pendiente'";
$res_alertas = $conexion->query($sql_alertas)->fetch_assoc();
$alertas_pendientes = $res_alertas['total'] ?? 0;
?>



<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel Admin - Pastelería El Ángel</title>
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/admin.css">
  <script src="../js/carousel.js" defer></script>
  <script src="../js/admin.js" defer></script>
  <link rel="icon" type="image/png" href="../img/logo.jpeg">
</head>

<?php if ($alertas_pendientes > 0): ?>
  <div id="modal-alerta">
    <div class="modal-contenido">
      <h3>⚠️ ¡Alerta de Stock Bajo!</h3>
      <p>Tienes <?= $alertas_pendientes ?> insumo(s) con stock por debajo del mínimo.</p>
      <button onclick="atenderAlerta()">Ver alertas</button>
      <button onclick="posponerAlerta()">Posponer 5 minuto</button>
    </div>
  </div>


  <script>
    function mostrarModal() {
      document.getElementById("modal-alerta").style.display = "block";
    }

    function ocultarModal() {
      document.getElementById("modal-alerta").style.display = "none";
    }

    function atenderAlerta() {
      // Te manda directo a editar insumos
      window.location.href = 'ver_alertas.php';
    }

    function posponerAlerta() {
      ocultarModal();
      const proxima = Date.now() + (10 * 1000); // ⏱ 10 segundos
     // const proxima = Date.now() + (5 * 60 * 1000); // 5 minutos
      localStorage.setItem('proximaAlerta', proxima);
    }

    function verificarAlerta() {
      const proxima = localStorage.getItem('proximaAlerta');

      // Mostrar solo si ya pasó el tiempo pospuesto
      if (!proxima || Date.now() >= parseInt(proxima)) {
        mostrarModal();
        localStorage.removeItem('proximaAlerta'); // Se vuelve a activar luego
      }
    }

    window.addEventListener('DOMContentLoaded', () => {
      verificarAlerta();

      setInterval(() => {
        const modalVisible = document.getElementById("modal-alerta").style.display === "block";
        const proxima = localStorage.getItem('proximaAlerta');

        if (!modalVisible && (!proxima || Date.now() >= parseInt(proxima))) {
          mostrarModal();
          localStorage.removeItem('proximaAlerta');
        }
      }, 1000); // Verifica cada segundo
    });
  </script>


<?php endif; ?>


<body>

  <!-- BOTÓN MENÚ ADMIN -->
  <button class="menu-toggle" id="menuToggle">☰</button>
  <div class="admin-menu" id="adminMenu">
    <!-- <a href="index.php">Inicio Admin</a> -->
    <a href="agregar_producto.php">Agregar Producto</a>
    <a href="ver_productos.php">Gestionar Productos</a>
    <a href="secciones_productos.php">Secciones de productos</a>
    <a href="ver_pedidos.php">Ver pedidos</a>
    <a href="ver_ventas.php">Ver ventas</a>
    <a href="agregar_insumo.php">Agregar Insumo</a>
    <a href="ver_insumos.php">Ver Insumos</a>
    <a href="seleccionar_producto_insumos.php">Asignar Insumos</a>
    <a href="ver_alertas.php">Alertas de Stock</a>
    <a href="../includes/logout.php">Cerrar sesión</a>
  </div>

  <!-- NAVBAR COMPARTIDA -->
  <header class="navbar">
    <img src="../img/logo.jpeg" alt="Logo El Ángel" class="logo">

    <div class="nav-container">
      <nav>
        <ul class="nav-links">
          <li><a href="#menu">PRODUCTOS</a></li>
          <li><a href="#sucursal">SUCURSAL</a></li>
          <li><a href="#promociones">PROMOCIONES</a></li>
          <li><a href="#contacto">CONTACTO</a></li>
        </ul>
      </nav>
    </div>

    <div class="nicono-navbar">
      <?php if (isset($_SESSION['nombre'])): ?>
        <div class="usuario-nombre">
          <span><?= htmlspecialchars($_SESSION['nombre']) ?></span>
          <a href="perfil_admin.php" title="Mi cuenta">
            <img src="../img/usuario.png" alt="Perfil" class="icono-navbar">
          </a>
        </div>
      <?php else: ?>
        <a href="../vistas/login.php" title="Iniciar sesión">
          <img src="../img/usuario.png" alt="Iniciar sesión" class="icono-navbar">
        </a>
      <?php endif; ?>

      <!-- <a href="#" title="Buscar"> -->
      <!-- <img src="../img/lupa.png" alt="Buscar" class="icono-navbar"> -->
      <!-- </a> -->
    </div>

    <div class="boton-sesion">
      <a href="../includes/logout.php" class="btn-sesion">Cerrar sesión</a>
    </div>
  </header>

  <!-- CARRUSEL -->
  <section class="banner">
    <div class="carousel">
      <img src="../img/banner1.jpg" alt="Banner 1" class="slide">
      <img src="../img/banner2.jpg" alt="Banner 2" class="slide">
      <img src="../img/banner3.jpg" alt="Banner 3" class="slide">
      <button id="prev">&#10094;</button>
      <button id="next">&#10095;</button>
    </div>
  </section>

  <!-- PROMOCIONES -->
  <section class="promociones" id="promociones">
    <h2 class="titulo-promociones">PROMOCIONES ESPECIALES</h2>
    <div class="promo-container">
      <?php
      $productos_menu = obtenerProductosPorSeccion($conexion, 'promociones');
      foreach ($productos_menu as $row) {

      ?>
        <div class="promo-card">
          <img src="../ver_imagen.php?id=<?= $row['id_producto'] ?>" alt="<?= htmlspecialchars($row['nombre']) ?>" class="promo-img">
          <div class="promo-descuento">PROMO</div>
          <p class="promo-nombre"><?= htmlspecialchars($row['nombre']) ?></p>
          <p class="promo-detalle"><?= htmlspecialchars($row['descripcion']) ?></p>

          <?php if (!empty($row['precio_promocion'])): ?>
            <p>
              <span style="color: gray; text-decoration: line-through;">
                $<?= number_format($row['precio'], 2) ?>
              </span>
              <span style="color: #e53935; font-weight: bold;">
                $<?= number_format($row['precio_promocion'], 2) ?>
              </span>
            </p>
          <?php else: ?>
            <p class="precio">$<?= number_format($row['precio'], 2) ?></p>
          <?php endif; ?>

        </div>

      <?php } ?>
    </div>
  </section>

  <!-- MENÚ DE PRODUCTOS -->
  <section class="menu" id="menu">
    <h2 class="titulo-menu">MENÚ COMPLETO</h2>
    <div class="productos-container">
      <?php
      $productos_menu = obtenerProductosPorSeccion($conexion, 'menú');
      foreach ($productos_menu as $row) {

      ?>
        <div class="producto-card">
          <img src="../ver_imagen.php?id=<?= $row['id_producto'] ?>" alt="<?= htmlspecialchars($row['nombre']) ?>" class="producto-img">
          <?php if (!empty($row['precio_promocion'])): ?>
            <div class="precio">
              <span style="color: gray; text-decoration: line-through;">
                $<?= number_format($row['precio'], 2) ?>
              </span>
              <span style="color:rgb(255, 255, 255); font-weight: bold;">
                $<?= number_format($row['precio_promocion'], 2) ?>
              </span>
            </div>
          <?php else: ?>
            <div class="precio">$<?= number_format($row['precio'], 2) ?></div>
          <?php endif; ?>

          <p><strong><?= htmlspecialchars($row['nombre']) ?></strong></p>
          <p class="descripcion"><?= htmlspecialchars($row['descripcion']) ?></p>

          <!-- <button class="btn-comprar">COMPRAR</button> -->

        </div>
      <?php } ?>
    </div>
  </section>

  <!-- MÁS VENDIDO -->
  <section class="mas-vendido">
    <h2 class="titulo-vendido">LO MÁS VENDIDO DEL MES</h2>
    <div class="vendido-container">
      <?php
      $productos_menu = obtenerProductosPorSeccion($conexion, 'más vendidos');
      foreach ($productos_menu as $row) {

      ?>
        <div class="vendido-card">
          <div class="badge">⭐ Más vendido</div>
          <img src="../ver_imagen.php?id=<?= $row['id_producto'] ?>" alt="<?= htmlspecialchars($row['nombre']) ?>" class="vendido-img">
          <h3 class="vendido-nombre"><?= htmlspecialchars($row['nombre']) ?></h3>
          <p class="vendido-detalle"><?= htmlspecialchars($row['descripcion']) ?></p>
          <?php if (!empty($row['precio_promocion'])): ?>
            <div class="precio">
              <span style="color: gray; text-decoration: line-through;">
                $<?= number_format($row['precio'], 2) ?>
              </span>
              <span style="color:rgb(255, 255, 255); font-weight: bold;">
                $<?= number_format($row['precio_promocion'], 2) ?>
              </span>
            </div>
          <?php else: ?>
            <div class="precio">$<?= number_format($row['precio'], 2) ?></div>
          <?php endif; ?>
          <!-- <button class="btn-vendido">ORDENAR</button> -->
        </div>
      <?php } ?>
    </div>
  </section>

  <?php include('../includes/footer_admin.php'); ?>
</body>

</html>