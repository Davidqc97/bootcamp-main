<?php
// Archivo: listar.php (Reescrito para DB Normalizada)
require 'conexion.php';

// Consulta con JOINs y GROUP_CONCAT para obtener toda la información
$sql = "
    SELECT 
        u.*,
        GROUP_CONCAT(DISTINCT co.nombre ORDER BY co.id SEPARATOR ', ') as objetivos,
        GROUP_CONCAT(DISTINCT ce.nombre ORDER BY ce.id SEPARATOR ', ') as equipos,
        GROUP_CONCAT(DISTINCT cl.nombre ORDER BY cl.id SEPARATOR ', ') as lesiones
    FROM 
        usuarios u
    LEFT JOIN 
        usuario_objetivos uo ON u.user_idx = uo.id_usuario
    LEFT JOIN 
        catalogo_objetivos co ON uo.id_objetivo = co.id
    LEFT JOIN 
        usuario_equipos ue ON u.user_idx = ue.id_usuario
    LEFT JOIN 
        catalogo_equipos ce ON ue.id_equipo = ce.id
    LEFT JOIN 
        usuario_lesiones ul ON u.user_idx = ul.id_usuario
    LEFT JOIN 
        catalogo_lesiones cl ON ul.id_lesion = cl.id
    GROUP BY
        u.user_idx
    ORDER BY 
        u.fecha_registro DESC;
";

$resultado = $conexion->query($sql);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Usuarios Registrados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style> body { background-color: #f8f9fa; } .table-responsive { margin-top: 2rem; } </style>
</head>
<body>
    <div class="container-fluid mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Usuarios Registrados (Estructura Correcta)</h3>
            <a href="index.php" class="btn btn-primary">Volver al Inicio</a>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th><th>Sexo</th><th>Nivel</th><th>Edad</th><th>Altura</th><th>Peso</th><th>Días/Sem</th><th>Objetivos</th><th>Equipo</th><th>Lesiones</th><th>Fecha Reg.</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($resultado && $resultado->num_rows > 0) {
                                while($fila = $resultado->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($fila["user_idx"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($fila["sexo"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($fila["nivel"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($fila["edad"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($fila["altura_cm"]) . " cm</td>";
                                    echo "<td>" . htmlspecialchars($fila["peso_kg"]) . " kg</td>";
                                    echo "<td>" . htmlspecialchars($fila["dias_disp_sem"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($fila["objetivos"] ?? 'N/A') . "</td>";
                                    echo "<td>" . htmlspecialchars($fila["equipos"] ?? 'N/A') . "</td>";
                                    echo "<td>" . htmlspecialchars($fila["lesiones"] ?? 'N/A') . "</td>";
                                    echo "<td>" . date('d/m/Y H:i', strtotime($fila["fecha_registro"])) . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='11' class='text-center'>No hay registros en la base de datos.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php $conexion->close(); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>