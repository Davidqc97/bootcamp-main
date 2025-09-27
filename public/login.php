<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../lib/session.php';
require_once __DIR__ . '/../lib/csrf.php';
require_once __DIR__ . '/../lib/auth.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validate();
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';
    if ($email === '' || $pass === '') {
        $error = 'Por favor, completa todos los campos.';
    } else {
        if (auth_login($email, $pass)) {
            header('Location: ' . APP_BASE_URL . 'protected/dashboard.php');
            exit;
        } else {
            $error = 'Credenciales inválidas.';
        }
    }
}
?>

<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login • <?php echo htmlspecialchars(APP_NAME); ?></title>
  <style>
    :root { font-family: system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, Cantarell, Noto Sans, Arial, "Apple Color Emoji", "Segoe UI Emoji"; }
    body { display:flex; align-items:center; justify-content:center; min-height:100vh; background:#f6f7fb; margin:0; }
    .card { width: 100%; max-width: 380px; background:#fff; border-radius:16px; box-shadow: 0 10px 30px rgba(0,0,0,.08); padding: 24px; }
    h1 { font-size: 1.25rem; margin:0 0 12px; }
    .muted { color:#6b7280; font-size:.9rem; margin-bottom:18px; }
    label { display:block; font-weight:600; margin:12px 0 6px; }
    input { width:100%; padding:10px 12px; border-radius:10px; border:1px solid #e5e7eb; outline:none; }
    input:focus { border-color:#3b82f6; box-shadow:0 0 0 3px rgba(59,130,246,.15); }
    .btn { margin-top:16px; width:100%; padding:10px 12px; border:none; border-radius:12px; cursor:pointer; font-weight:700; }
    .btn-primary { background:#3b82f6; color:white; }
    .error { background:#fee2e2; color:#991b1b; border:1px solid #fecaca; padding:10px; border-radius:10px; margin-bottom:12px; }
    .foot { margin-top:12px; font-size:.85rem; color:#6b7280; text-align:center; }
  </style>
</head>
<body>
  <div class="card">
    <h1>Bienvenida 👋</h1>
    <p class="muted">Ingresa con tu correo y contraseña para continuar.</p>
    <?php if ($error): ?><div class="error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    <form method="post" action="">
      <?php echo csrf_input(); ?>
      <label for="email">Correo</label>
      <input id="email" type="email" name="email" required autocomplete="email">
      <label for="password">Contraseña</label>
      <input id="password" type="password" name="password" required autocomplete="current-password">
      <button class="btn btn-primary" type="submit">Iniciar sesión</button>
    </form>
    <div class="foot">© <?php echo date('Y'); ?> <?php echo htmlspecialchars(APP_NAME); ?></div>
  </div>
</body>
</html>