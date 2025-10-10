<?php
// Incluir autenticación compartida
require_once '/var/www/shared/db.php';
require_once '/var/www/shared/auth.php';

// Requerir autenticación
$auth = require_auth('http://localhost:8082/login.php');

// Inicializar variables
$message = "";
$mensajes = [];

// Manejo del formulario POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre      = trim($_POST['nombre'] ?? '');
    $celular     = trim($_POST['celular'] ?? '');
    $categoria   = trim($_POST['categoria'] ?? '');
    $correo      = trim($_POST['correo'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $estado      = trim($_POST['estado'] ?? '');
    
    // Validación
    $errores = [];
    if (empty($nombre)) $errores[] = "El nombre es obligatorio.";
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) $errores[] = "Correo inválido.";
    if (empty($categoria)) $errores[] = "Selecciona la categoría.";
    if (empty($estado)) $errores[] = "Selecciona el estado.";
    if (empty($descripcion)) $errores[] = "La descripción es obligatoria.";
    if (!empty($celular) && !preg_match('/^\d{10}$/', $celular)) {
        $errores[] = "Celular inválido (10 dígitos).";
    }
    
    if (empty($errores)) {
        try {
            $sql = "INSERT INTO mensajes (usuario_id, nombre, celular, categoria, correo, descripcion, estado)
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$auth['id'], $nombre, $celular, $categoria, $correo, $descripcion, $estado]);
            $message = "<div class='alert success'>✅ Registro guardado correctamente.</div>";
        } catch (PDOException $e) {
            $message = "<div class='alert error'>❌ Error al guardar: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    } else {
        $message = "<div class='alert warning'>⚠️ " . htmlspecialchars($errores[0]) . "</div>";
    }
}

// Consulta para listar (solo mensajes del usuario actual)
try {
    $stmt = $conn->prepare("SELECT id, nombre, celular, categoria, correo, descripcion, estado, creado_en FROM mensajes WHERE usuario_id = ? ORDER BY creado_en DESC");
    $stmt->execute([$auth['id']]);
    $mensajes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $mensajes = [];
    $message .= "<div class='alert error'>❌ Error listando registros: " . htmlspecialchars($e->getMessage()) . "</div>";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel - Campiclouders</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .navbar {
            background: var(--surface-2);
            padding: 15px var(--space);
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        .navbar-brand {
            font-weight: 600;
            color: var(--accent);
        }
        .navbar-user {
            display: flex;
            align-items: center;
            gap: 15px;
            color: var(--muted);
            font-size: 0.9rem;
        }
        .navbar-user a {
            color: var(--accent);
            text-decoration: none;
            transition: opacity 0.3s;
        }
        .navbar-user a:hover {
            opacity: 0.8;
        }
        .btn-logout {
            background: #ef4444;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.85rem;
        }
        .btn-logout:hover {
            background: #dc2626;
        }
    </style>
</head>

<body>
    <!-- Encabezado -->
    <header>
        <img src="https://fulppi.s3.us-east-1.amazonaws.com/Logo+de+Campiclouders.png" alt="Campiclouders" class="logo">
    </header>

    <!-- Barra de navegación -->
    <nav class="navbar">
        <div class="navbar-brand">Panel de Control</div>
        <div class="navbar-user">
            <span>👤 <?= htmlspecialchars($auth['usuario']) ?></span>
            <a href="http://localhost:8082/logout.php" class="btn-logout">Cerrar sesión</a>
        </div>
    </nav>

    <main>
        <!-- Mensajes del servidor -->
        <?php if (!empty($message)) echo $message; ?>

        <!-- Formulario (crear nuevo registro) -->
        <section>
            <h2>📝 Nuevo Registro</h2>
            <form method="post" action="" onsubmit="return validarFormulario();" class="form">
                <div class="grid">
                    <label>Nombre
                        <input 
                            id="nombre" 
                            name="nombre" 
                            type="text" 
                            placeholder="Tu nombre"
                            required
                            pattern="^[A-Za-záéíóúäëïöüñÁÉÍÓÚÄËÏÖÜÑ ]{2,100}$"
                            maxlength="100"
                        >
                    </label>

                    <label>Celular
                        <input 
                            id="celular" 
                            name="celular" 
                            type="tel" 
                            placeholder="3001234567"
                            inputmode="numeric"
                            pattern="^\d{10}$"
                            maxlength="10"
                        >
                    </label>

                    <label>Correo
                        <input 
                            id="correo" 
                            name="correo" 
                            type="email" 
                            placeholder="tu@email.com"
                            required
                        >
                    </label>
                    
                    <label>Categoría
                        <select id="categoria" name="categoria" required>
                            <option value="">Selecciona...</option>
                            <option>Profesor</option>
                            <option>Estudiante</option>
                            <option>Otro</option>
                        </select>
                    </label>

                    <label>Estado
                        <select id="estado" name="estado" required>
                            <option value="">Selecciona...</option>
                            <option>Nuevo</option>
                            <option>En proceso</option>
                            <option>Cerrado</option>
                        </select>
                    </label>

                    <label>Prioridad
                        <select id="prioridad" name="prioridad">
                            <option value="media">Media</option>
                            <option value="baja">Baja</option>
                            <option value="alta">Alta</option>
                        </select>
                    </label>
                </div>

                <label>Descripción
                    <textarea 
                        id="descripcion" 
                        name="descripcion" 
                        rows="5"
                        placeholder="Escribe la descripción..."
                        required 
                        maxlength="1000"
                    ></textarea>
                    <small><span id="desc-count">0</span>/1000</small>
                </label>
                
                <div class="actions">
                    <button type="submit" class="btn-guardar">💾 Guardar</button>
                </div>
            </form>
        </section>

        <!-- Tabla (listar registros) --> 
        <section>
            <h2>📋 Mis Registros</h2>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Celular</th>
                            <th>Categoría</th>
                            <th>Correo</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th>Creado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($mensajes) === 0): ?>
                            <tr><td colspan="7" style="text-align: center; color: var(--muted);">Sin registros aún.</td></tr>
                        <?php else: foreach ($mensajes as $r): ?>
                            <tr>
                                <td><?= htmlspecialchars($r['nombre']) ?></td>
                                <td>
                                    <?php if (!empty($r['celular'])): ?>
                                        <div>
                                            <a href="tel:<?= htmlspecialchars($r['celular']) ?>"><?= htmlspecialchars($r['celular']) ?></a>
                                            <?php
                                                $soloDigitos = preg_replace('/\D/', '', $r['celular']);
                                                if ($soloDigitos) {
                                                    echo ' · <a target="_blank" href="https://wa.me/57' . $soloDigitos . '">WhatsApp</a>';
                                                }
                                            ?>
                                        </div>
                                    <?php else: ?>
                                        <span style="color: var(--muted);">—</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($r['categoria']) ?></td>
                                <td><a href="mailto:<?= htmlspecialchars($r['correo']) ?>"><?= htmlspecialchars($r['correo']) ?></a></td>
                                <td><?= htmlspecialchars(substr($r['descripcion'], 0, 50)) ?>...</td>
                                <td><?= htmlspecialchars($r['estado']) ?></td>
                                <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($r['creado_en']))) ?></td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <footer style="text-align: center; padding: 20px; background: var(--surface-2); margin-top: 40px; color: var(--muted);">
        © <script>document.write(new Date().getFullYear())</script> · Bootcamp G5 · Campiclouders
    </footer>

    <script>
        function actualizarContadorDesc(el) {
            const count = document.getElementById('desc-count');
            if (count) count.textContent = el.value.length;
        }

        function validarFormulario() {
            const nombre = document.getElementById('nombre').value.trim();
            const celular = document.getElementById('celular').value.trim();
            const categoria = document.getElementById('categoria').value;
            const correo = document.getElementById('correo').value.trim();
            const descripcion = document.getElementById('descripcion').value.trim();
            const estado = document.getElementById('estado').value;

            let errores = [];
            if (!nombre) errores.push("El nombre es obligatorio.");
            if (!correo) errores.push("El correo es obligatorio.");
            if (!categoria) errores.push("Selecciona una categoría.");
            if (!estado) errores.push("Selecciona un estado.");
            if (!descripcion) errores.push("La descripción es obligatoria.");
            if (celular && !/^\d{10}$/.test(celular)) errores.push("Celular inválido (10 dígitos).");

            if (errores.length) {
                alert("Revisa el formulario:\n• " + errores.join("\n• "));
                return false;
            }
            return true;
        }

        document.addEventListener('DOMContentLoaded', () => {
            const desc = document.getElementById('descripcion');
            if (desc) {
                actualizarContadorDesc(desc);
                desc.addEventListener('input', () => actualizarContadorDesc(desc));
            }
        });
    </script>
</body>
</html>