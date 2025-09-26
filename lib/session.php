<?php
// Secure session start
if (session_status() === PHP_SESSION_NONE) {
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}
function session_regenerate_safe() {
    if (!isset($_SESSION['__regen'])) {
        $_SESSION['__regen'] = time();
        session_regenerate_id(true);
    } elseif (time() - $_SESSION['__regen'] > 300) { // 5 min
        $_SESSION['__regen'] = time();
        session_regenerate_id(true);
    }
}
?>