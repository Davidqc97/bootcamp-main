<?php
// Archivo: procesar.php (Reescrito para base de datos normalizada)

require 'conexion.php';

$mensaje = "";
$clase_alerta = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Iniciar transacción
    $conexion->begin_transaction();

    try {
        // 1. Insertar en la tabla principal `usuarios`
        $sql_usuario = "INSERT INTO usuarios (sexo, idioma, nivel, edad, altura_cm, peso_kg, training_age_anos, dias_disp_sem, tiempo_por_sesion_min) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_usuario = $conexion->prepare($sql_usuario);
        
        // Limpiar y asignar variables
        $sexo = $_POST['sexo'] ?? '';
        $idioma = $_POST['idioma'] ?? '';
        $nivel = $_POST['nivel'] ?? '';
        $edad = (int)($_POST['edad'] ?? 0);
        $altura_cm = (int)($_POST['altura_cm'] ?? 0);
        $peso_kg = (float)($_POST['peso_kg'] ?? 0.0);
        $training_age_anos = (int)($_POST['training_age_anos'] ?? 0);
        $dias_disp_sem = (int)($_POST['dias_disp_sem'] ?? 0);
        $tiempo_por_sesion_min = (int)($_POST['tiempo_por_sesion_min'] ?? 0);

        $stmt_usuario->bind_param("sssiidiis", $sexo, $idioma, $nivel, $edad, $altura_cm, $peso_kg, $training_age_anos, $dias_disp_sem, $tiempo_por_sesion_min);
        $stmt_usuario->execute();

        // Obtener el ID del usuario recién insertado
        $id_usuario = $conexion->insert_id;
        $stmt_usuario->close();

        // 2. Insertar en las tablas pivote (multi-hot)
        // Función auxiliar para insertar en tablas pivote
        function insertar_pivote($conexion, $id_usuario, $post_array, $tabla_pivote, $columna_id) {
            if (!empty($post_array) && is_array($post_array)) {
                $sql = "INSERT INTO {$tabla_pivote} (id_usuario, {$columna_id}) VALUES (?, ?)";
                $stmt = $conexion->prepare($sql);
                foreach ($post_array as $id_seleccion) {
                    $id_seleccion_int = (int)$id_seleccion;
                    $stmt->bind_param("ii", $id_usuario, $id_seleccion_int);
                    $stmt->execute();
                }
                $stmt->close();
            }
        }

        insertar_pivote($conexion, $id_usuario, $_POST['objetivos'] ?? [], 'usuario_objetivos', 'id_objetivo');
        insertar_pivote($conexion, $id_usuario, $_POST['equipo_disponible'] ?? [], 'usuario_equipos', 'id_equipo');
        insertar_pivote($conexion, $id_usuario, $_POST['lesiones'] ?? [], 'usuario_lesiones', 'id_lesion');

        // Si todo fue bien, confirmar la transacción
        $conexion->commit();
        $mensaje = "¡Registro guardado exitosamente con la nueva estructura!";
        $clase_alerta = "success";

    } catch (mysqli_sql_exception $exception) {
        // Si algo falló, revertir la transacción
        $conexion->rollback();
        $mensaje = "Error al guardar el registro: " . $exception->getMessage();
        $clase_alerta = "danger";
    }

} else {
    $mensaje = "Acceso no permitido. Por favor, envía el formulario.";
    $clase_alerta = "warning";
}

$conexion->close();
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
            <h4 class="alert-heading"><?php echo htmlspecialchars($mensaje); ?></h4>
        </div>
        <a href="formulario.php" class="btn btn-primary">Volver al Formulario</a>
        <a href="listar.php" class="btn btn-secondary">Ver Lista de Usuarios</a>
    </div>
</body>
</html>