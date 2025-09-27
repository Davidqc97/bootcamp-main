<?php
// === Configuración de entorno (InfinityFree) ===
define('DB_HOST', '');
define('DB_PORT', '');
define('DB_NAME', '');
define('DB_USER', '');
define('DB_PASS', '');

// Ajustes de la app
define('APP_NAME', 'Mi Proyecto - Login');
define('APP_BASE_URL', '/'); // ajusta si estás en subcarpeta (p.ej. '/miapp/')


// Helper para construir URLs consistentes
function url(string $path = ''): string {
    // Normaliza slashes
    $base = rtrim(APP_BASE_URL, '/').'/';
    $path = ltrim($path, '/');
    return $base.$path;
}

// Sesión (por si auth.php la necesita)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
