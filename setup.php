<?php
// Archivo: setup.php
// Este script automatiza la configuración de la base de datos.

// Mensajes para el usuario
$mensaje = '';
$clase_alerta = '';

// Incluir la conexión, pero manejar el error si no se puede conectar
@include 'conexion.php';

if (!isset($conexion) || $conexion->connect_error) {
    $mensaje = "<strong>Error Crítico:</strong> No se pudo conectar a la base de datos. Por favor, verifica que las credenciales en <code>conexion.php</code> son correctas antes de continuar.";
    $clase_alerta = "danger";
} else {
    // Ruta al archivo SQL
    $sql_file = 'crear_db.sql';

    if (!file_exists($sql_file)) {
        $mensaje = "<strong>Error:</strong> El archivo <code>crear_db.sql</code> no se encuentra en el servidor. Asegúrate de haberlo subido.";
        $clase_alerta = "danger";
    } else {
        // Leer el contenido del archivo SQL
        $sql_commands = file_get_contents($sql_file);

        // Ejecutar las consultas múltiples
        if ($conexion->multi_query($sql_commands)) {
            // Vaciar los resultados de las consultas anteriores
            while ($conexion->next_result()) {
                if ($result = $conexion->store_result()) {
                    $result->free();
                }
            }
            $mensaje = "<strong>¡Éxito!</strong> La base de datos ha sido configurada correctamente. Todas las tablas fueron creadas y pobladas.";
            $clase_alerta = "success";
        } else {
            $mensaje = "<strong>Error al ejecutar el script SQL:</strong> " . $conexion->error;
            $clase_alerta = "danger";
        }
    }
    $conexion->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración Automática de la Base de Datos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header text-center">
                <h3>Asistente de Configuración</h3>
            </div>
            <div class="card-body">
                <div class="alert alert-<?php echo $clase_alerta; ?>" role="alert">
                    <?php echo $mensaje; ?>
                </div>

                <?php if ($clase_alerta === 'success'): ?>
                    <div class="alert alert-danger text-center">
                        <h4><i class="bi bi-exclamation-triangle-fill"></i> ¡ACCIÓN DE SEGURIDAD REQUERIDA!</h4>
                        <p>Por favor, <strong>BORRA</strong> los archivos <code>setup.php</code> y <code>crear_db.sql</code> de tu servidor inmediatamente para prevenir riesgos de seguridad.</p>
                    </div>
                    <div class="text-center mt-4">
                        <a href="index.php" class="btn btn-primary">Ir a la Página Principal</a>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</body>
</html>
