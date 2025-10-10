<?php
/**
 * Conexión a Base de Datos
 * Archivo compartido entre auth y app
 */
$host = getenv('DB_HOST') ?: 'db';
$dbname = getenv('DB_NAME') ?: 'appdb';
$username = getenv('DB_USER') ?: 'appuser';
$password = getenv('DB_PASSWORD') ?: 'app123';

try {
    $conn = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    error_log("Error de conexión BD: " . $e->getMessage());
    die("Error de conexión a la base de datos. Por favor intenta más tarde.");
}
?>