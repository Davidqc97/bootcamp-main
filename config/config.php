<?php
// === Configuración de entorno (InfinityFree) ===
define('DB_HOST', 'sql303.infinityfree.com');
define('DB_PORT', '3306'); // no lo usa mysqli directamente, pero lo dejamos anotado
define('DB_NAME', 'if0_40011650_training_plans');
define('DB_USER', 'if0_40011650');
define('DB_PASS', 'CU2GIZ9tqlRFC');

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