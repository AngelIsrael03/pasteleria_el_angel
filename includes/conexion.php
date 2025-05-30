<?php
$host = "localhost";
$usuario = "root";
$contrasena = ""; // Por defecto está vacío en XAMPP
$base_datos = "pasteleria_el_angel";

// Crear conexión
$conexion = new mysqli($host, $usuario, $contrasena, $base_datos);

// Verificar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Mensaje de prueba (temporal)
//echo "<p style='color:green; text-align:center;'>✔ Conexión exitosa a la base de datos</p>";
?>