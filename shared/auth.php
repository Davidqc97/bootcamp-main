<?php
/**
 * Middleware de Autenticación JWT
 * Proporciona funciones para codificar, decodificar y verificar tokens JWT
 */

// Configuración
$JWT_SECRET = getenv('JWT_SECRET') ?: 'MiSecretoSuperSeguro_ChangeMe_123';
$JWT_ALGORITHM = 'HS256';
$JWT_EXP_HOURS = intval(getenv('JWT_EXP_HOURS') ?: '24');

/**
 * Codifica datos en base64url
 */
function base64url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

/**
 * Decodifica datos en base64url
 */
function base64url_decode($data) {
    $padding = 4 - (strlen($data) % 4);
    if ($padding !== 4) {
        $data .= str_repeat('=', $padding);
    }
    return base64_decode(strtr($data, '-_', '+/'));
}

/**
 * Genera un JWT
 * @param array $payload Datos del token
 * @param string $secret Clave secreta
 * @return string Token JWT
 */
function jwt_encode($payload, $secret) {
    global $JWT_ALGORITHM;
    
    $header = [
        'alg' => $JWT_ALGORITHM,
        'typ' => 'JWT'
    ];
    
    $segments = [];
    $segments[] = base64url_encode(json_encode($header));
    $segments[] = base64url_encode(json_encode($payload));
    
    $signing_input = implode('.', $segments);
    $signature = hash_hmac('sha256', $signing_input, $secret, true);
    $segments[] = base64url_encode($signature);
    
    return implode('.', $segments);
}

/**
 * Decodifica y valida un JWT
 * @param string $jwt Token a validar
 * @param string $secret Clave secreta
 * @return array [válido (bool), payload (array|string)]
 */
function jwt_decode($jwt, $secret) {
    $parts = explode('.', $jwt);
    
    if (count($parts) !== 3) {
        return [false, 'Formato inválido'];
    }
    
    list($h64, $p64, $s64) = $parts;
    
    $payload = json_decode(base64url_decode($p64), true);
    
    if ($payload === null) {
        return [false, 'Payload inválido'];
    }
    
    $sig = base64url_decode($s64);
    $calc = hash_hmac('sha256', "$h64.$p64", $secret, true);
    
    if (!hash_equals($calc, $sig)) {
        return [false, 'Firma inválida'];
    }
    
    if (isset($payload['exp']) && time() >= $payload['exp']) {
        return [false, 'Token expirado'];
    }
    
    return [true, $payload];
}

/**
 * Verifica si el usuario está autenticado
 * @return array|false [usuario_id, usuario, rol] o false
 */
function check_auth() {
    global $JWT_SECRET;
    
    if (!isset($_COOKIE['auth_token'])) {
        return false;
    }
    
    $token = $_COOKIE['auth_token'];
    list($valid, $payload) = jwt_decode($token, $JWT_SECRET);
    
    if (!$valid || !is_array($payload)) {
        return false;
    }
    
    return [
        'id' => $payload['sub'] ?? null,
        'usuario' => $payload['usuario'] ?? null,
        'rol' => $payload['rol'] ?? 'usuario'
    ];
}

/**
 * Requiere autenticación, redirige al login si no está autenticado
 * @param string $redirect_url URL a redirigir si no está autenticado
 * @return array Datos del usuario autenticado
 */
function require_auth($redirect_url = null) {
    $auth = check_auth();
    
    if (!$auth) {
        $redirect = $redirect_url ?: 'http://localhost:8082/login.php';
        header("Location: $redirect");
        exit;
    }
    
    return $auth;
}

/**
 * Requiere un rol específico
 * @param string $required_role Rol requerido
 * @param string $redirect_url URL a redirigir
 * @return array Datos del usuario
 */
function require_role($required_role, $redirect_url = null) {
    $auth = require_auth($redirect_url);
    
    if ($auth['rol'] !== $required_role) {
        header('HTTP/1.0 403 Forbidden');
        echo 'Acceso denegado: rol insuficiente';
        exit;
    }
    
    return $auth;
}

/**
 * Crea un token JWT para un usuario
 * @param int $user_id ID del usuario
 * @param string $usuario Nombre de usuario
 * @param string $rol Rol del usuario
 * @return string Token JWT
 */
function create_token($user_id, $usuario, $rol = 'usuario') {
    global $JWT_SECRET, $JWT_EXP_HOURS;
    
    $now = time();
    $payload = [
        'iss' => 'auth-service',
        'sub' => $user_id,
        'usuario' => $usuario,
        'rol' => $rol,
        'iat' => $now,
        'exp' => $now + ($JWT_EXP_HOURS * 3600)
    ];
    
    return jwt_encode($payload, $JWT_SECRET);
}

/**
 * Establece la cookie de autenticación
 * @param string $token Token JWT
 */
function set_auth_cookie($token) {
    $payload = json_decode(base64url_decode(explode('.', $token)[1]), true);
    
    setcookie(
        'auth_token',
        $token,
        [
            'expires' => $payload['exp'] ?? 0,
            'path' => '/',
            'httponly' => true,
            'samesite' => 'Lax'
        ]
    );
}

/**
 * Destruye la autenticación
 */
function logout() {
    setcookie('auth_token', '', time() - 3600, '/');
    session_destroy();
}

/**
 * Retorna respuesta JSON con error
 */
function json_error($message, $code = 400) {
    header('Content-Type: application/json');
    http_response_code($code);
    echo json_encode([
        'success' => false,
        'error' => $message
    ]);
    exit;
}

/**
 * Retorna respuesta JSON con éxito
 */
function json_success($data = [], $code = 200) {
    header('Content-Type: application/json');
    http_response_code($code);
    echo json_encode([
        'success' => true,
        'data' => $data
    ]);
    exit;
}
?>