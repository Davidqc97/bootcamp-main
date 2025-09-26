<?php
// Archivo: procesar.php (PDO)
declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../lib/auth.php';
require_auth();
$pdo = db();
$user = auth_user();

$mensaje = "";
$clase_alerta = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Sanitizar y mapear campos
        $sexo = trim($_POST['sexo'] ?? '');
        $idioma = trim($_POST['idioma'] ?? '');
        $nivel = trim($_POST['nivel'] ?? '');
        $edad = (int)($_POST['edad'] ?? 0);
        $altura_cm = (int)($_POST['altura_cm'] ?? 0);
        $peso_kg = (float)($_POST['peso_kg'] ?? 0);
        $training_age_anos = (int)($_POST['training_age_anos'] ?? 0);
        $dias_disp_sem = (int)($_POST['dias_disp_sem'] ?? 0);
        $tiempo_por_sesion_min = (int)($_POST['tiempo_por_sesion_min'] ?? 0);

        $pdo->beginTransaction();

        // Insert principal
        $st = $pdo->prepare("INSERT INTO usuarios
            (sexo, idioma, nivel, edad, altura_cm, peso_kg, training_age_anos, dias_disp_sem, tiempo_por_sesion_min)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $st->execute([$sexo, $idioma, $nivel, $edad, $altura_cm, $peso_kg, $training_age_anos, $dias_disp_sem, $tiempo_por_sesion_min]);
        $id_usuario = (int)$pdo->lastInsertId();

        // Helpers para tablas pivote
        $insertPivot = function (PDO $pdo, int $idUsuario, array $ids, string $tabla, string $col) {
            if (empty($ids)) return;
            $sql = "INSERT INTO {$tabla} (id_usuario, {$col}) VALUES (:u, :v)";
            $st = $pdo->prepare($sql);
            foreach ($ids as $v) {
                $v = (int)$v;
                $st->execute([':u' => $idUsuario, ':v' => $v]);
            }
        };

        $insertPivot($pdo, $id_usuario, $_POST['objetivos'] ?? [], 'usuario_objetivos', 'id_objetivo');
        $insertPivot($pdo, $id_usuario, $_POST['equipo_disponible'] ?? [], 'usuario_equipos', 'id_equipo');
        $insertPivot($pdo, $id_usuario, $_POST['lesiones'] ?? [], 'usuario_lesiones', 'id_lesion');

        $pdo->commit();
        $mensaje = "¡Registro guardado con éxito!";
        $clase_alerta = "success";
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        $mensaje = "Error al guardar: " . htmlspecialchars($e->getMessage());
        $clase_alerta = "danger";
    }
} else {
    $mensaje = "Método no permitido.";
    $clase_alerta = "warning";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado del Registro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="alert alert-<?php echo $clase_alerta; ?>" role="alert">
            <h4 class="alert-heading"><?php echo $mensaje; ?></h4>
        </div>
        <a href="formulario.php" class="btn btn-primary">Volver al Formulario</a>
        <a href="listar.php" class="btn btn-secondary">Ver Lista de Usuarios</a>
    </div>
</body>
</html>
