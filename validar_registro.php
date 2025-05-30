<?php
// Inicia la sesión para poder usar variables de sesión
session_start();

// Incluye el archivo de conexión a la base de datos
include('includes/conexion.php'); // Asegúrate de que la ruta sea correcta

// Verifica que el formulario haya sido enviado por método POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recupera y limpia los datos del formulario
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $contrasena = trim($_POST['contrasena']);
    $confirmar = trim($_POST['confirmar']);

    // Validación: campos vacíos
    if (empty($nombre) || empty($correo) || empty($contrasena) || empty($confirmar)) {
        $_SESSION['error_registro'] = "Todos los campos son obligatorios.";
        header("Location: vistas/registro.php"); // Redirige al formulario
        exit();
    }

    // Validación: contraseñas deben coincidir
    if ($contrasena !== $confirmar) {
        $_SESSION['error_registro'] = "Las contraseñas no coinciden.";
        header("Location: vistas/registro.php");
        exit();
    }

    // Validación: verificar si el correo ya existe
    $sql = "SELECT id_usuario FROM usuarios WHERE correo = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $_SESSION['error_registro'] = "El correo ya está registrado.";
        header("Location: vistas/registro.php");
        exit();
    }

    // Inserta el nuevo usuario como tipo 'cliente'
    $sql_insert = "INSERT INTO usuarios (nombre, correo, contraseña, tipo_usuario) VALUES (?, ?, ?, 'cliente')";
    $stmt_insert = $conexion->prepare($sql_insert);
    $stmt_insert->bind_param("sss", $nombre, $correo, $contrasena);

    // Si se guarda correctamente
    if ($stmt_insert->execute()) {
        $_SESSION['registro_exitoso'] = "Cuenta creada exitosamente. Ya puedes iniciar sesión.";
        header("Location: vistas/login.php");
        exit();
    } else {
        // Si hubo un error al insertar
        $_SESSION['error_registro'] = "Error al crear cuenta.";
        header("Location: vistas/registro.php");
        exit();
    }
}
