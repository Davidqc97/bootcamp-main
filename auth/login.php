<?php
require_once '/var/www/shared/db.php';
require_once '/var/www/shared/auth.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    // Validar campos
    if (empty($usuario) || empty($password)) {
        $error = 'Por favor completa usuario y contraseña.';
    } else if (strlen($usuario) < 3 || strlen($password) < 6) {
        $error = 'Credenciales inválidas.';
    } else {
        try {
            $stmt = $conn->prepare('SELECT id, usuario, email, password_hash, rol, estado FROM usuarios WHERE usuario = ? LIMIT 1');
            $stmt->execute([$usuario]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && $user['estado'] === 'activo' && password_verify($password, $user['password_hash'])) {
                // Generar token
                $token = create_token($user['id'], $user['usuario'], $user['rol']);
                set_auth_cookie($token);
                
                // Actualizar último login
                $stmt = $conn->prepare('UPDATE usuarios SET ultimo_login = NOW() WHERE id = ?');
                $stmt->execute([$user['id']]);
                
                // Redirigir al app
                header('Location: http://localhost:8080/');
                exit;
            } else {
                $error = 'Usuario o contraseña incorrectos.';
            }
        } catch (PDOException $e) {
            $error = 'Error al procesar login. Intenta más tarde.';
            error_log($e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar Sesión - Campiclouders</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="auth-container">
        <h2>🔐 Iniciar Sesión</h2>
        
        <?php if ($error): ?>
            <div class="alert error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="POST" class="form">
            <div class="form-group">
                <label for="usuario">Usuario</label>
                <input 
                    type="text" 
                    id="usuario" 
                    name="usuario" 
                    placeholder="Tu usuario"
                    required
                    autocomplete="username"
                    minlength="3"
                    maxlength="50"
                >
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    placeholder="Tu contraseña"
                    required
                    autocomplete="current-password"
                    minlength="6"
                >
            </div>
            
            <button type="submit" class="btn-login">Entrar</button>
        </form>
        
        <div class="links">
            <p>¿No tienes cuenta? <a href="register.php">Regístrate aquí</a></p>
        </div>
    </div>
</body>
</html>