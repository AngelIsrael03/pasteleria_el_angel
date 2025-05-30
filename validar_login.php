<?php
session_start();
include('includes/conexion.php'); // o '../includes/conexion.php' según tu estructura

$correo = $_POST['correo'];
$contrasena = $_POST['contrasena'];

// Consulta segura
$sql = "SELECT * FROM Usuarios WHERE correo = '$correo' AND contraseña = '$contrasena'";
$res = mysqli_query($conexion, $sql);

if (mysqli_num_rows($res) == 1) {
    $usuario = mysqli_fetch_assoc($res);

    // Guardar datos en sesión
    $_SESSION['id_usuario'] = $usuario['id_usuario'];
    $_SESSION['nombre'] = $usuario['nombre'];
    $_SESSION['tipo'] = $usuario['tipo_usuario']; // CLAVE: esto debe existir

    // Redirección según el tipo de usuario
    if ($usuario['tipo_usuario'] === 'administrador') {
        header("Location: admin/index.php");
    } else {
        // Cliente → sincronizar carrito antes de redirigir
        include('includes/sincronizar_carrito.php');
        header("Location: index.php");
    }
    exit;
} else {
    echo "Correo o contraseña incorrectos.";
}
