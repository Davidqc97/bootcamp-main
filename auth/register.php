<?php
require_once '/var/www/shared/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $nombre_completo = trim($_POST['nombre_completo'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $password_confirm = trim($_POST['password_confirm'] ?? '');
    
    // Validaciones
    $errores = [];
    
    if (empty($usuario)) {
        $errores[] = 'El usuario es obligatorio.';
    } else if (strlen($usuario) < 3 || strlen($usuario) > 50) {
        $errores[] = 'El usuario debe tener entre 3 y 50 caracteres.';
    } else if (!preg_match('/^[a-zA-Z0-9_.-]+$/', $usuario)) {
        $errores[] = 'El usuario solo puede contener letras, números, puntos, guiones y guiones bajos.';
    }
    
    if (empty($email)) {
        $errores[] = 'El email es obligatorio.';
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = 'El email no es válido.';
    }
    
    if (empty($nombre_completo)) {
        $errores[] = 'El nombre completo es obligatorio.';
    } else if (strlen($nombre_completo) < 3 || strlen($nombre_completo) > 100) {
        $errores[] = 'El nombre debe tener entre 3 y 100 caracteres.';
    }
    
    if (empty($password)) {
        $errores[] = 'La contraseña es obligatoria.';
    } else if (strlen($password) < 6) {
        $errores[] = 'La contraseña debe tener al menos 6 caracteres.';
    }
    
    if ($password !== $password_confirm) {
        $errores[] = 'Las contraseñas no coinciden.';
    }
    
    if (empty($errores)) {
        try {
            // Verificar si usuario o email ya existen
            $stmt = $conn->prepare('SELECT id FROM usuarios WHERE usuario = ? OR email = ? LIMIT 1');
            $stmt->execute([$usuario, $email]);
            
            if ($stmt->fetch()) {
                $errores[] = 'El usuario o email ya está registrado.';
            } else {
                // Crear nuevo usuario
                $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
                
                $stmt = $conn->prepare(
                    'INSERT INTO usuarios (usuario, email, password_hash, nombre_completo, rol, estado) 
                     VALUES (?, ?, ?, ?, ?, ?)'
                );
                
                $stmt->execute([
                    $usuario,
                    $email,
                    $hash,
                    $nombre_completo,
                    'usuario',
                    'activo'
                ]);
                
                $success = '✅ Registro exitoso. Ahora puedes iniciar sesión.';
            }
        } catch (PDOException $e) {
            $errores[] = 'Error al registrarse. Intenta más tarde.';
            error_log($e->getMessage());
        }
    }
    
    if (!empty($errores)) {
        $error = implode('<br>', $errores);
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrarse - Campiclouders</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="auth-container">
        <h2>📋 Crear Cuenta</h2>
        
        <?php if ($success): ?>
            <div class="alert success"><?= htmlspecialchars($success) ?></div>
            <div class="links">
                <p><a href="login.php">← Volver al login</a></p>
            </div>
        <?php else: ?>
            <?php if ($error): ?>
                <div class="alert error"><?= $error ?></div>
            <?php endif; ?>
            
            <form method="POST" class="form">
                <div class="form-group">
                    <label for="usuario">Usuario</label>
                    <input 
                        type="text" 
                        id="usuario" 
                        name="usuario" 
                        placeholder="Tu usuario"
                        value="<?= htmlspecialchars($_POST['usuario'] ?? '') ?>"
                        required
                        minlength="3"
                        maxlength="50"
                    >
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        placeholder="tu@email.com"
                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                        required
                    >
                </div>
                
                <div class="form-group">
                    <label for="nombre_completo">Nombre Completo</label>
                    <input 
                        type="text" 
                        id="nombre_completo" 
                        name="nombre_completo" 
                        placeholder="Tu nombre completo"
                        value="<?= htmlspecialchars($_POST['nombre_completo'] ?? '') ?>"
                        required
                        minlength="3"
                        maxlength="100"
                    >
                </div>
                
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="Mínimo 6 caracteres"
                        required
                        minlength="6"
                    >
                </div>
                
                <div class="form-group">
                    <label for="password_confirm">Confirmar Contraseña</label>
                    <input 
                        type="password" 
                        id="password_confirm" 
                        name="password_confirm" 
                        placeholder="Repite tu contraseña"
                        required
                        minlength="6"
                    >
                </div>
                
                <button type="submit" class="btn-login">Registrarse</button>
            </form>
            
            <div class="links">
                <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a></p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>