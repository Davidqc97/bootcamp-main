<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plataforma de Fitness</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f2f5;
        }
        .hero {
            background: url('https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?q=80&w=2070&auto=format&fit=crop') no-repeat center center;
            background-size: cover;
            color: white;
            padding: 100px 0;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.7);
        }
        .hero h1 {
            font-size: 3.5rem;
            font-weight: bold;
        }
        .card {
            border: none;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

    <!-- Sección Principal (Hero) -->
    <div class="hero text-center">
        <div class="container">
            <h1>Bienvenido a Tu Plataforma de Fitness</h1>
            <p class="lead">El lugar central para registrar, seguir y analizar tu progreso en el entrenamiento.</p>
        </div>
    </div>

    <!-- Contenido Principal -->
    <div class="container mt-5">
        <div class="row text-center">
            <div class="col-md-8 mx-auto">
                <h2>Gestiona tus Datos de Entrenamiento</h2>
                <p class="lead text-muted">Nuestra plataforma te permite llevar un registro detallado de tus datos antropométricos, disponibilidad, objetivos y mucho más. Comienza hoy mismo a construir tu historial de atleta.</p>
            </div>
        </div>

        <!-- Tarjetas de Acción -->
        <div class="row mt-5">
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center d-flex flex-column justify-content-center">
                        <h5 class="card-title">Nuevos Atletas</h5>
                        <p class="card-text">Añade un nuevo perfil de atleta a la base de datos utilizando nuestro formulario de registro detallado.</p>
                        <a href="formulario.html" class="btn btn-primary mt-auto">Registrar Nuevo Usuario</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center d-flex flex-column justify-content-center">
                        <h5 class="card-title">Ver Registros</h5>
                        <p class="card-text">Consulta la lista completa de todos los atletas registrados en el sistema y sus datos principales.</p>
                        <a href="listar.php" class="btn btn-secondary mt-auto">Ver Usuarios Registrados</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="text-center text-muted py-4 mt-5">
        <p>&copy; 2025 Plataforma de Fitness</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
