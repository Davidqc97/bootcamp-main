<?php
// Archivo: conexion.php
// Configuración para la conexión a la base de datos MySQL.
// Completa los siguientes campos con tus credenciales.

$servidor = "localhost";      // Tu servidor de base de datos (usualmente 'localhost')
$usuario = "root";            // Tu nombre de usuario de MySQL
$contrasena = ""; // La contraseña de tu usuario de MySQL
$base_de_datos = "formulario_db"; // El nombre de la base de datos que creaste

// Crear la conexión
$conexion = new mysqli($servidor, $usuario, $contrasena, $base_de_datos);

// Verificar si la conexión falló
if ($conexion->connect_error) {
    // Detener la ejecución y mostrar el error
    die("Error de conexión: " . $conexion->connect_error);
}

// Opcional: Establecer el juego de caracteres a UTF-8 para evitar problemas con tildes y caracteres especiales
$conexion->set_charset("utf8");

?>
