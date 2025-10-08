<?php
include 'db.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = htmlspecialchars($_POST['nombre']);
    $correo = htmlspecialchars($_POST['correo']);
    $mensaje = htmlspecialchars($_POST['mensaje']);

    if (!empty($nombre) && !empty($correo) && !empty($mensaje)) {
        try {
            $sql = "INSERT INTO mensajes (nombre, correo, mensaje) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$nombre, $correo, $mensaje]);
            $message = "<div class='alert success'>✅ Mensaje guardado correctamente.</div>";
        } catch (PDOException $e) {
            $message = "<div class='alert error'>❌ Error al guardar el mensaje: " . $e->getMessage() . "</div>";
        }
    } else {
        $message = "<div class='alert warning'>⚠️ Por favor, completa todos los campos.</div>";
    }
}

$stmt = $conn->query("SELECT * FROM mensajes ORDER BY fecha DESC");
$mensajes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang='es'>
<head>
<meta charset='UTF-8'>
<title>Arquitectura Cloud - Contenedores Docker</title>
<link rel='stylesheet' href='style.css'>
<script>
function validarFormulario() {
    const nombre = document.getElementById('nombre').value.trim();
    const correo = document.getElementById('correo').value.trim();
    const mensaje = document.getElementById('mensaje').value.trim();
    if (!nombre || !correo || !mensaje) {
        alert("Por favor completa todos los campos.");
        return false;
    }
    return true;
}
</script>
</head>
<body>
<header>
    <h1>🌐 Taller de Arquitectura Cloud</h1>
    <h2>Contenedores con Docker y PHP + MySQL</h2>
</header>

<main>
    <section class='form-section'>
        <h3>📝 Enviar mensaje</h3>
        <?= $message ?>
        <form method='POST' onsubmit='return validarFormulario();'>
            <label for='nombre'>Nombre:</label>
            <input type='text' name='nombre' id='nombre' placeholder='Tu nombre completo'>

            <label for='correo'>Correo:</label>
            <input type='email' name='correo' id='correo' placeholder='ejemplo@correo.com'>

            <label for='mensaje'>Mensaje:</label>
            <textarea name='mensaje' id='mensaje' rows='4' placeholder='Escribe tu mensaje...'></textarea>

            <button type='submit'>Guardar mensaje</button>
        </form>
    </section>

    <section class='table-section'>
        <h3>📋 Mensajes registrados</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Mensaje</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($mensajes) > 0): ?>
                    <?php foreach ($mensajes as $fila): ?>
                        <tr>
                            <td><?= $fila['id'] ?></td>
                            <td><?= htmlspecialchars($fila['nombre']) ?></td>
                            <td><?= htmlspecialchars($fila['correo']) ?></td>
                            <td><?= htmlspecialchars($fila['mensaje']) ?></td>
                            <td><?= $fila['fecha'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan='5' style='text-align:center;'>Sin registros aún.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </section>
</main>

<footer>
    <p>© 2025 Taller Docker — Docente: Juan Carlos López Henao</p>
</footer>
</body>
</html>
