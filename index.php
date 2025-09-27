<?php
require_once __DIR__ . '/lib/auth.php';

if (auth_user()) {
  header('Location: ' . APP_BASE_URL . 'protected/dashboard.php');
  exit;
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Bienvenida</title>
  <style>
    :root { font-family: system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, Cantarell, Noto Sans, Arial; }
    body { margin:0; min-height:100vh; display:grid; place-items:center; background:#f6f7fb; }
    .card { background:#fff; padding:28px; border-radius:16px; box-shadow:0 10px 30px rgba(0,0,0,.08); text-align:center; max-width:520px; }
    h1 { margin:0 0 6px; font-size:1.5rem; }
    p { margin:0 0 16px; color:#6b7280; }
    a.btn { display:inline-block; padding:10px 14px; border-radius:12px; background:#3b82f6; color:#fff; text-decoration:none; font-weight:700; }
  </style>
</head>
<body>
  <div class="card">
    <h1>¡Hola!</h1>
    <p>Para continuar, inicia sesión.</p>
    <a class="btn" href="/public/login.php">Iniciar sesión</a>
  </div>
</body>
</html>
