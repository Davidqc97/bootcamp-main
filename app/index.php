<?php

// Verificación de autenticación
require_once 'jwt.php';
$secret = getenv('JWT_SECRET') ?: 'MiSecretoSuperSeguro_ChangeMe_123';

if (!isset($_COOKIE['auth_token'])) {
    header('Location: http://localhost:8082/login.php');
    exit;
}

$token = $_COOKIE['auth_token'];
list($valid, $payload) = jwt_decode($token, $secret);

if (!$valid) {
    header('Location: http://localhost:8082/login.php');
    exit;
}

// Bloque PHP inicial
include 'db.php';
$message = "";

//Manejo del POST  - Lee/limpia - Valida
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre      = trim($_POST['nombre'] ?? '');
    $celular     = trim($_POST['celular'] ?? '');
    $categoria   = trim($_POST['categoria'] ?? '');
    $correo      = trim($_POST['correo'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $estado      = trim($_POST['estado'] ?? '');

    //errores
    $errores = [];
    if ($nombre === '') $errores[] = "El nombre es obligatorio.";
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) $errores[] = "Correo inválido.";
    if ($categoria === '') $errores[] = "Selecciona la categoría.";
    if ($estado === '') $errores[] = "Selecciona el estado.";
    if ($descripcion === '') $errores[] = "La descripción es obligatoria.";
    if ($celular !== '' && !preg_match('/^(\+?\d{1,3}\s?)?(\d{10}|\d{3}\s?\d{3}\s?\d{4})$/', $celular)) {
        $errores[] = "Celular inválido.";
    }

   //alerta según éxito o error.
    if (empty($errores)) {
        try {
            $sql = "INSERT INTO mensajes (nombre, celular, categoria, correo, descripcion, estado)
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$nombre, $celular, $categoria, $correo, $descripcion, $estado]);
            $message = "<div class='alert success'>✅ Registro guardado correctamente.</div>";
        } catch (PDOException $e) {
            $message = "<div class='alert error'>❌ Error al guardar: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    } else {
        $message = "<div class='alert warning'>⚠️ " . htmlspecialchars($errores[0]) . "</div>";
    }
}

// Consulta para listar
//Trae todos los registros ordenados por fecha
try {
    $stmt = $conn->query("SELECT * FROM mensajes ORDER BY creado_en DESC");
    $mensajes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $mensajes = [];
    $message .= "<div class='alert error'>❌ Error listando registros: " . htmlspecialchars($e->getMessage()) . "</div>";
}

// Simulación sin base de datos
// $message = "";
// $mensajes = [];

// // Si se envía el formulario
// if ($_SERVER["REQUEST_METHOD"] == "POST") {
//     $nombre      = trim($_POST['nombre'] ?? '');
//     $celular     = trim($_POST['celular'] ?? '');
//     $categoria   = trim($_POST['categoria'] ?? '');
//     $correo      = trim($_POST['correo'] ?? '');
//     $descripcion = trim($_POST['descripcion'] ?? '');
//     $estado      = trim($_POST['estado'] ?? '');

//     $errores = [];
//     if ($nombre === '') $errores[] = "El nombre es obligatorio.";
//     if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) $errores[] = "Correo inválido.";
//     if ($categoria === '') $errores[] = "Selecciona la categoría.";
//     if ($estado === '') $errores[] = "Selecciona el estado.";
//     if ($descripcion === '') $errores[] = "La descripción es obligatoria.";

//     if (empty($errores)) {
//         // Simulamos que se guarda correctamente
//         $message = "<div class='alert success'>✅ Registro simulado guardado correctamente.</div>";
//     } else {
//         $message = "<div class='alert warning'>⚠️ " . htmlspecialchars($errores[0]) . "</div>";
//     }
// }

// // Datos simulados (para mostrar la tabla)
// $mensajes = [
//     [
//         'nombre' => 'Ana Pérez',
//         'celular' => '+57 3001112233',
//         'categoria' => 'Estudiante',
//         'correo' => 'ana@example.com',
//         'descripcion' => 'Consulta sobre talleres.',
//         'estado' => 'Nuevo',
//         'creado_en' => '2025-10-08 10:00:00'
//     ],
//     [
//         'nombre' => 'Carlos Gómez',
//         'celular' => '+57 3125556677',
//         'categoria' => 'Profesor',
//         'correo' => 'carlos@example.com',
//         'descripcion' => 'Solicitud de material.',
//         'estado' => 'En proceso',
//         'creado_en' => '2025-10-07 15:30:00'
//     ]
// ];

?>
<!DOCTYPE html>
<html lang="es">
<head>

<meta charset="UTF-8">
<title>Campiclouders</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="style.css">

<script>
// --- Contador de descripción ---
function actualizarContadorDesc(el) {
  const count = document.getElementById('desc-count');
  if (count) count.textContent = el.value.length;
}

// --- Validación del formulario ---
function validarFormulario() {
  const nombre      = document.getElementById('nombre').value.trim();
  const celular     = document.getElementById('celular').value.trim();
  const categoria   = document.getElementById('categoria').value;
  const correo      = document.getElementById('correo').value.trim();
  const descripcion = document.getElementById('descripcion').value.trim();
  const estado      = document.getElementById('estado').value;

  const reNombre  = /^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ ]{2,60}$/;  // solo letras/espacios 2–60
  const reCorreo  = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;       // email básico
  const reCelular = /^\d{10}$/;                         // 10 dígitos

  const nombreOk  = reNombre.test(nombre);
  const emailOk   = reCorreo.test(correo);
  const telOk     = reCelular.test(celular);

  // Límite de descripción (toma del atributo maxlength si existe; si no, 300)
  const descMax   = document.getElementById('descripcion').maxLength > 0
                    ? document.getElementById('descripcion').maxLength
                    : 300;

  let errores = [];
  if (!nombreOk) errores.push("Nombre obligatorio (solo letras y espacios, 2–60).");
  if (!emailOk) errores.push("Correo inválido.");
  if (!categoria) errores.push("Selecciona la categoría.");
  if (!estado) errores.push("Selecciona el estado.");
  if (!descripcion) errores.push("La descripción es obligatoria.");
  if (descripcion.length > descMax) errores.push(`Descripción no debe superar ${descMax} caracteres.`);
  if (!telOk) errores.push("Celular inválido (exactamente 10 dígitos).");

  if (errores.length) {
    alert("Revisa el formulario:\n• " + errores.join("\n• "));
    return false;
  }
  return true;
}

// Inicializa contador al cargar y en cada input
document.addEventListener('DOMContentLoaded', () => {
  const desc = document.getElementById('descripcion');
  if (desc) {
    actualizarContadorDesc(desc);
    desc.addEventListener('input', () => actualizarContadorDesc(desc));
  }
});
</script>
</head>

<!-- Encabezado -->
<body>
  <header>
    <img src="https://fulppi.s3.us-east-1.amazonaws.com/Logo+de+Campiclouders.png" alt="" class="logo"> 
    <!-- <h1>Campiclouders</h1>
    <p>PHP + MySQL</p> -->
  </header>

  <main>
    <!-- Mensajes del servidor -->
    <?php if (!empty($message)) echo $message; ?>

    <!-- Formulario (crear nuevo registro) -->
    <section>
      <h2>Nuevo registro</h2>
      <form method="post" action="" onsubmit="return validarFormulario();" class="form">
        <div class="grid">
          <label>Nombre
            <input id="nombre" name="nombre" type="text" placeholder="Tu nombre"
                  required
                  inputmode="text"
                  pattern="^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ ]{2,60}$"
                  maxlength="60"
                  oninput="this.value=this.value.replace(/[^A-Za-zÁÉÍÓÚÜÑáéíóúüñ ]/g,'');">
          </label>

          <label>Celular
            <input id="celular" name="celular" type="tel" placeholder="3001234567"
                  required
                  inputmode="numeric"
                  pattern="^\d{10}$"
                  minlength="10" maxlength="10"
                  oninput="this.value=this.value.replace(/\D/g,'').slice(0,10);">
          </label>

          <label>Correo
            <input id="correo" name="correo" type="email" placeholder="tucorreo@ejemplo.com">
          </label>
          
          <label>Categoría
            <select id="categoria" name="categoria">
              <option value="">Selecciona…</option>
              <option>Profesor</option>
              <option>Estudiante</option>
            </select>
          </label>

          <label>Estado
            <select id="estado" name="estado">
              <option value="">Selecciona…</option>
              <option>Nuevo</option>
              <option>En proceso</option>
              <option>Cerrado</option>
            </select>
          </label>
        </div>

        <label>Descripción
          <textarea id="descripcion" name="descripcion" rows="5"
                    placeholder="Escribe la descripción…"
                    required maxlength="300"></textarea>
        </label>
        
        <div class="actions">
          <button type="submit" class="btn-guardar">Guardar</button>
        </div>
      </form>
    </section>

    <!-- Tabla (listar registros) --> 
    <section>
      <h2>Registros</h2>
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
              <tr><td colspan="7" class="muted">Sin registros aún.</td></tr>
            <?php else: foreach ($mensajes as $r): ?>
              <tr>
                <td><?= htmlspecialchars($r['nombre']) ?></td>
                <td>
                  <?php if (!empty($r['celular'])): ?>
                    <div>
                      <a href="tel:<?= htmlspecialchars($r['celular']) ?>"><?= htmlspecialchars($r['celular']) ?></a>
                      <?php
                        $soloDigitos = preg_replace('/\D/','',$r['celular']);
                        if ($soloDigitos) {
                          echo ' · <a target="_blank" href="https://wa.me/57'.$soloDigitos.'">WhatsApp</a>';
                        }
                      ?>
                    </div>
                  <?php else: ?>
                    <span class="muted">—</span>
                  <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($r['categoria']) ?></td>
                <td><a href="mailto:<?= htmlspecialchars($r['correo']) ?>"><?= htmlspecialchars($r['correo']) ?></a></td>
                <td><?= htmlspecialchars($r['descripcion']) ?></td>
                <td><?= htmlspecialchars($r['estado']) ?></td>
                <td><?= htmlspecialchars($r['creado_en'] ?? $r['fecha'] ?? '') ?></td>
              </tr>
            <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
    </section>
  </main>

  <footer>
    © <script>document.write(new Date().getFullYear())</script> · Bootcamp G5
  </footer>
</body>
</html>
