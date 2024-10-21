<?php
$server = "localhost";
$usuario = "root";
$contrasenia = "";
$base_de_datos = "biblioteca";

// Suprimimos los warnings para manejar los errores nosotros mismos
$conexion = @new mysqli($server, $usuario, $contrasenia, $base_de_datos);

// Verificamos la conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Establecemos el conjunto de caracteres a utf8
if (!$conexion->set_charset("utf8")) {
    die("Error cargando el conjunto de caracteres utf8: " . $conexion->error);
}

// Opcional: Configurar el modo de error de MySQL
$conexion->query("SET SESSION sql_mode = ''");

// Opcional: Configurar la zona horaria
$conexion->query("SET time_zone = '+00:00'");

// Si llegamos aquí, la conexión fue exitosa
// echo "Conexión exitosa a la base de datos.";
?>