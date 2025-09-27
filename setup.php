<?php
// Archivo: setup.php
// Asistente de instalación (InfinityFree / MySQL)

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once __DIR__ . '/config/db.php';

function import_sql(PDO $pdo, string $file): array {
    $res = ['file' => basename($file), 'ok' => true, 'executed' => 0, 'errors' => []];
    if (!is_file($file)) {
        $res['ok'] = false;
        $res['errors'][] = "No se encontró el archivo: " . htmlspecialchars($file);
        return $res;
    }
    $sql = file_get_contents($file);
    if ($sql === false) {
        $res['ok'] = false;
        $res['errors'][] = "No se pudo leer el archivo: " . htmlspecialchars($file);
        return $res;
    }

    // Remover comentarios /* */ y líneas que inician con -- o #
    $sql = preg_replace('~/\*.*?\*/~s', '', $sql);
    $lines = preg_split('~\R~', $sql);
    $clean = [];
    foreach ($lines as $line) {
        $trim = ltrim($line);
        if ($trim === '' || str_starts_with($trim, '--') || str_starts_with($trim, '#')) continue;
        $clean[] = $line;
    }
    $sql = implode("\n", $clean);

    // Dividir por ';' en fin de sentencia
    $statements = preg_split('~;(?=\s*(?:\R|$))~', $sql);
    foreach ($statements as $stmt) {
        $stmt = trim($stmt);
        if ($stmt === '') continue;
        try {
            $pdo->exec($stmt);
            $res['executed']++;
        } catch (Throwable $e) {
            $res['ok'] = false;
            $res['errors'][] = $e->getMessage();
        }
    }
    return $res;
}

$pdo = db();
$pdo->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");

$results = [];
$hadError = false;

// 1) Crear tabla de usuarios del login
$results[] = import_sql($pdo, __DIR__ . '/sql/01_create_users.sql');

// 2) Resto del esquema de negocio / catálogos
$results[] = import_sql($pdo, __DIR__ . '/crear_db.sql');

foreach ($results as $r) {
    if (!$r['ok']) $hadError = true;
}

// 3) Crear usuario admin si no existe
$adminEmail = 'admin@example.com';
try {
    $exists = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE email = " . $pdo->quote($adminEmail))->fetchColumn();
    if ($exists === 0) {
        $hash = password_hash('Admin123*', PASSWORD_DEFAULT);
        $st = $pdo->prepare("INSERT INTO users (name, email, password_hash) VALUES (:n, :e, :h)");
        $st->execute([':n' => 'Administrador', ':e' => $adminEmail, ':h' => $hash]);
        $adminCreated = true;
    } else {
        $adminCreated = false;
    }
} catch (Throwable $e) {
    $hadError = true;
    $adminCreated = false;
    $results[] = ['file' => 'create_admin', 'ok' => false, 'executed' => 0, 'errors' => [$e->getMessage()]];
}

$ok = !$hadError;

?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Asistente de instalación</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
          <h4 class="mb-0">Asistente de instalación</h4>
        </div>
        <div class="card-body">
          <?php if ($ok): ?>
            <div class="alert alert-success">
              <strong>¡Listo!</strong> La base de datos se configuró correctamente.
            </div>
          <?php else: ?>
            <div class="alert alert-warning">
              <strong>Se encontraron algunos problemas.</strong> Revisa los detalles debajo.
            </div>
          <?php endif; ?>

          <h5>Resultados de importación</h5>
          <ul class="list-group mb-4">
            <?php foreach ($results as $r): ?>
              <li class="list-group-item">
                <div class="d-flex justify-content-between">
                  <span><code><?php echo htmlspecialchars($r['file']); ?></code></span>
                  <span class="badge bg-<?php echo $r['ok'] ? 'success' : 'danger'; ?>">
                    <?php echo $r['ok'] ? 'OK' : 'Errores'; ?>
                  </span>
                </div>
                <small class="text-muted">Sentencias ejecutadas: <?php echo (int)$r['executed']; ?></small>
                <?php if (!empty($r['errors'])): ?>
                  <div class="mt-2">
                    <?php foreach ($r['errors'] as $e): ?>
                      <div class="text-danger small"><?php echo htmlspecialchars($e); ?></div>
                    <?php endforeach; ?>
                  </div>
                <?php endif; ?>
              </li>
            <?php endforeach; ?>
          </ul>

          <h5>Usuario administrador</h5>
          <p>
            <?php if ($adminCreated): ?>
              Se creó el usuario <code><?php echo htmlspecialchars($adminEmail); ?></code> con contraseña <code>Admin123*</code>.
            <?php else: ?>
              Ya existía el usuario <code><?php echo htmlspecialchars($adminEmail); ?></code> o no fue necesario crearlo.
            <?php endif; ?>
          </p>

          <div class="alert alert-info">
            Por seguridad, <strong>borra este archivo <code>setup.php</code></strong> cuando termines.
          </div>

          <div class="d-flex gap-2">
            <a class="btn btn-primary" href="<?php echo APP_BASE_URL; ?>public/login.php">Ir al login</a>
            <a class="btn btn-outline-secondary" href="<?php echo APP_BASE_URL; ?>">Ir al inicio</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
