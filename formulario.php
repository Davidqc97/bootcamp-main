<?php
// Archivo: formulario.php
// Este script ahora es dinámico. Carga las opciones para los checkboxes desde la base de datos.

require 'conexion.php';

// Consultar catálogos para llenar los checkboxes
$objetivos_result = $conexion->query("SELECT * FROM catalogo_objetivos ORDER BY id");
$equipos_result = $conexion->query("SELECT * FROM catalogo_equipos ORDER BY id");
$lesiones_result = $conexion->query("SELECT * FROM catalogo_lesiones ORDER BY id");

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Registro de Usuario (Dinámico)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .container { max-width: 800px; }
        .card { margin-top: 2rem; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header text-center">
                <h3>Formulario de Registro</h3>
            </div>
            <div class="card-body">
                <form action="procesar.php" method="POST">

                    <!-- Datos Personales (one-hot) -->
                    <h5 class="mt-4">Datos Personales</h5>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label"><strong>Sexo</strong></label>
                            <div>
                                <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="sexo" id="sexo_m" value="Masculino" required><label class="form-check-label" for="sexo_m">Masculino</label></div>
                                <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="sexo" id="sexo_f" value="Femenino"><label class="form-check-label" for="sexo_f">Femenino</label></div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="idioma" class="form-label"><strong>Idioma</strong></label>
                            <select class="form-select" name="idioma" id="idioma" required><option value="Español">Español</option><option value="Inglés">Inglés</option><option value="Otro">Otro</option></select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="nivel" class="form-label"><strong>Nivel de Experiencia</strong></label>
                            <select class="form-select" name="nivel" id="nivel" required><option value="Principiante">Principiante</option><option value="Intermedio">Intermedio</option><option value="Avanzado">Avanzado</option></select>
                        </div>
                    </div>

                    <!-- Antropometría (int/float) -->
                    <h5 class="mt-4">Antropometría</h5>
                    <div class="row">
                        <div class="col-md-3 mb-3"><label for="edad" class="form-label">Edad</label><input type="number" class="form-control" id="edad" name="edad" placeholder="e.g., 25" required></div>
                        <div class="col-md-3 mb-3"><label for="altura_cm" class="form-label">Altura (cm)</label><input type="number" class="form-control" id="altura_cm" name="altura_cm" placeholder="e.g., 175" required></div>
                        <div class="col-md-3 mb-3"><label for="peso_kg" class="form-label">Peso (kg)</label><input type="number" step="0.1" class="form-control" id="peso_kg" name="peso_kg" placeholder="e.g., 70.5" required></div>
                        <div class="col-md-3 mb-3"><label for="training_age_anos" class="form-label">Años Entrenando</label><input type="number" class="form-control" id="training_age_anos" name="training_age_anos" placeholder="e.g., 3" required></div>
                    </div>

                    <!-- Disponibilidad (int) -->
                    <h5 class="mt-4">Disponibilidad</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label for="dias_disp_sem" class="form-label">Días por semana</label><input type="number" class="form-control" id="dias_disp_sem" name="dias_disp_sem" min="1" max="7" placeholder="e.g., 4" required></div>
                        <div class="col-md-6 mb-3"><label for="tiempo_por_sesion_min" class="form-label">Minutos por sesión</label><input type="number" class="form-control" id="tiempo_por_sesion_min" name="tiempo_por_sesion_min" step="5" placeholder="e.g., 60" required></div>
                    </div>

                    <!-- Selecciones Múltiples (multi-hot) -->
                    <div class="row mt-4">
                        <div class="col-md-4"><h6><strong>Objetivos</strong></h6>
                            <?php while($row = $objetivos_result->fetch_assoc()): ?>
                            <div class="form-check"><input class="form-check-input" type="checkbox" name="objetivos[]" value="<?php echo $row['id']; ?>" id="obj_<?php echo $row['id']; ?>"><label class="form-check-label" for="obj_<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['nombre']); ?></label></div>
                            <?php endwhile; ?>
                        </div>
                        <div class="col-md-4"><h6><strong>Equipo Disponible</strong></h6>
                            <?php while($row = $equipos_result->fetch_assoc()): ?>
                            <div class="form-check"><input class="form-check-input" type="checkbox" name="equipo_disponible[]" value="<?php echo $row['id']; ?>" id="eq_<?php echo $row['id']; ?>"><label class="form-check-label" for="eq_<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['nombre']); ?></label></div>
                            <?php endwhile; ?>
                        </div>
                        <div class="col-md-4"><h6><strong>Lesiones (Activas o Recientes)</strong></h6>
                            <?php while($row = $lesiones_result->fetch_assoc()): ?>
                            <div class="form-check"><input class="form-check-input" type="checkbox" name="lesiones[]" value="<?php echo $row['id']; ?>" id="les_<?php echo $row['id']; ?>"><label class="form-check-label" for="les_<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['nombre']); ?></label></div>
                            <?php endwhile; ?>
                        </div>
                    </div>

                    <div class="d-grid gap-2 mt-5"><button type="submit" class="btn btn-primary">Guardar Registro</button></div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conexion->close(); ?>
