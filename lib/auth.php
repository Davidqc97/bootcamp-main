<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/session.php';
function auth_login(string $email, string $password): bool {
    $pdo = db();
    $stmt = $pdo->prepare("SELECT id, name, email, password_hash FROM users WHERE email = :email LIMIT 1");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();
    if (!$user) return false;
    if (!password_verify($password, $user['password_hash'])) return false;
    // Success
    session_regenerate_safe();
    $_SESSION['user'] = [
        'id' => $user['id'],
        'name' => $user['name'],
        'email' => $user['email'],
    ];
    return true;
}
function auth_logout() {
    require_once __DIR__ . '/session.php';
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}
function auth_user() {
    return $_SESSION['user'] ?? null;
}
function require_auth() {
    if (!auth_user()) {
        header('Location: ' . APP_BASE_URL . 'public/login.php');
        exit;
    }
}
?>