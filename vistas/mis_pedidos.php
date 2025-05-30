<?php
session_start();
include '../includes/conexion.php';

$logueado = isset($_SESSION['id_usuario']) && $_SESSION['tipo'] === 'cliente';

if ($logueado) {
    $id_usuario = $_SESSION['id_usuario'];

    $sql = "SELECT * FROM pedidos WHERE id_usuario = ? ORDER BY fecha_recogida ASC, hora_recogida ASC";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();

    $pedidos = [];
    while ($fila = $resultado->fetch_assoc()) {
        $pedidos[] = $fila;
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Mis pedidos</title>
    <link rel="stylesheet" href="../css/mis_pedidos.css">
</head>

<body>
    <a href="../index.php" class="btn-volver">← Volver al inicio</a>

    <h1>Mis pedidos</h1>

    <?php if (!$logueado): ?>
        <p style="text-align: center; font-size: 18px; color: #bf4079;">
            Por favor inicia sesión para ver tus pedidos.
        </p>
        <div class="centrar-boton">
            <a href="login.php" class="btn-volver">Iniciar sesión</a>
        </div>

    <?php elseif (empty($pedidos)): ?>
        <p style="text-align:center;">No tienes pedidos registrados aún.</p>

        <?php else:
        $estados = ['pendiente', 'completado', 'cancelado'];
        foreach ($estados as $estado):
            $pedidos_estado = array_filter($pedidos, fn($p) => $p['estado'] === $estado);
            if (empty($pedidos_estado)) continue;
        ?>
            <h2 style="color:#bf4079; border-bottom:2px dashed #f9d5e5; text-align:center; margin-top:30px;"><?= ucfirst($estado) ?></h2>

            <?php foreach ($pedidos_estado as $pedido): ?>
                <div style="background-color:#ffe4ed; border-radius:10px; margin:20px auto; padding:20px; max-width:900px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                    <p><strong>Fecha del pedido:</strong> <?= $pedido['fecha_pedido'] ?></p>
                    <p><strong>Fecha de recolección:</strong> <?= $pedido['fecha_recogida'] ?> a las <?= $pedido['hora_recogida'] ?></p>
                    <p><strong>Método de pago:</strong> <?= ucfirst($pedido['metodo_pago']) ?></p>
                    <p><strong>Estado:</strong> <span class="estado-<?= strtolower($estado) ?>"><?= ucfirst($estado) ?></span></p>
                    <p><strong>Observaciones:</strong> <?= $pedido['observaciones'] ?></p>

                    <?php
                    $es_personalizado = false;
                    $check = $conexion->prepare("SELECT COUNT(*) FROM detalle_personalizado WHERE id_pedido = ?");
                    $check->bind_param("i", $pedido['id_pedido']);
                    $check->execute();
                    $check->bind_result($es_personalizado);
                    $check->fetch();
                    $check->close();
                    ?>

                    <div style="background:#fff; border-radius:8px; padding:15px; margin-top:10px;">
                        <?php if ($es_personalizado): ?>
                            <h4>Detalle del pastel personalizado:</h4>
                            <?php
                            $det = $conexion->prepare("SELECT * FROM detalle_personalizado WHERE id_pedido = ?");
                            $det->bind_param("i", $pedido['id_pedido']);
                            $det->execute();
                            $res = $det->get_result();
                            while ($d = $res->fetch_assoc()):
                            ?>
                                <p><strong>Tamaño:</strong> <?= $d['tamaño'] ?></p>
                                <p><strong>Sabor:</strong> <?= $d['sabor'] ?></p>
                                <p><strong>Relleno:</strong> <?= $d['relleno'] ?></p>
                                <p><strong>Cobertura:</strong> <?= $d['cobertura'] ?></p>
                                <p><strong>Forma:</strong> <?= $d['forma'] ?></p>
                                <p><strong>Tema:</strong> <?= $d['tema'] ?></p>
                                <p><strong>Dedicatoria:</strong> <?= $d['dedicatoria'] ?></p>
                                <p><strong>Precio:</strong> $<?= number_format($d['precio'], 2) ?></p>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <h4>Productos del pedido:</h4>
                            <table style="width:100%; background:#fff7fa; border-radius:6px;">
                                <thead>
                                    <tr>
                                        <th style="text-align:left;">Producto</th>
                                        <th>Cantidad</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $det = $conexion->prepare("SELECT d.*, p.nombre FROM detalle_pedido d JOIN productos p ON d.id_producto = p.id_producto WHERE d.id_pedido = ?");
                                    $det->bind_param("i", $pedido['id_pedido']);
                                    $det->execute();
                                    $res = $det->get_result();
                                    $total = 0;
                                    while ($d = $res->fetch_assoc()):
                                        $total += $d['subtotal'];
                                    ?>
                                        <tr>
                                            <td><?= $d['nombre'] ?></td>
                                            <td><?= $d['cantidad'] ?></td>
                                            <td>$<?= number_format($d['subtotal'], 2) ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                    <tr>
                                        <td colspan="2" style="text-align:right;"><strong>Total del pedido:</strong></td>
                                        <td><strong>$<?= number_format($total, 2) ?></strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    <?php endif; ?>

</body>

</html>