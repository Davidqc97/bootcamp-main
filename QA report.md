🧪 QA Report – Docker Web Auth Architecture

Proyecto: Bootcamp – Taller Docker Web con Autenticación
Autor QA: José David Escalante
Fecha: 2025-10-10
Estado: ✅ Validado con observaciones menores

🔍 Objetivo

Verificar la correcta modularización del proyecto Docker con servicios separados para:

Aplicación principal (app)

Servicio de autenticación (auth)

Base de datos (db)

Administración (phpmyadmin)

y confirmar que se resolvieron los errores de la versión anterior:

❌ ../app/db.php no encontrado
❌ Rutas inconsistentes
❌ Contenedores aislados
❌ Redirección incorrecta

🧩 Hallazgos y Correcciones Verificadas
Categoría	Antes (❌)	Ahora (✅)	Resultado
Conexión BD	include '../app/db.php' → ruta inválida	Se reemplazó por shared/db.php montado como volumen en ambos contenedores	✅ Correcto
Variables y rutas	URLs relativas y dependientes del contenedor	Se implementaron variables de entorno (BASE_URL_APP, BASE_URL_AUTH) en .env y constantes en config.php	✅ Correcto
Aislamiento de contenedores	Cada servicio con su propio root sin compartir recursos	Se agregaron volúmenes compartidos (./shared:/var/www/shared) y red común con hostname db	✅ Correcto
Redirección	header('Location: /login.php') y header('Location: /') incorrectas	Se corrigió a URLs absolutas (http://localhost:8082/login.php, http://localhost:8080/)	✅ Correcto
JWT Cookie	Token generado sin expiración variable	Ahora usa JWT_EXP_HOURS en .env para definir validez del token	✅ Correcto
Persistencia de datos	MySQL sin volumen persistente	Volumen db_data creado y montado correctamente	✅ Correcto
⚙️ Archivos Clave Verificados

docker-compose.yml – Estructura modular de servicios y dependencias.

Dockerfile – Instalación limpia con soporte mysqli y pdo_mysql.

.env – Variables de entorno centralizadas.

jwt.php, login.php, register.php, logout.php – Flujo de autenticación funcional.

01_schema.sql – Base de datos inicial correcta.

README.md – Documentación funcional con instrucciones claras.

🧠 Observaciones

Mejora futura: centralizar constantes de JWT y base URL en un archivo config.php dentro del volumen shared/.

Seguridad: considerar mover la clave JWT_SECRET fuera del repositorio público (usar .env.local o GitHub Secrets).

UX: el flujo de login y registro podría mostrar mensajes más claros (éxito, expiración, error de conexión).

CI/CD: posible mejora automatizando el docker-compose up --build en GitHub Actions para QA automatizado.

🏁 Conclusión

El refactor modular cumple los objetivos:

✅ Servicios independientes pero conectados correctamente.

✅ Redirecciones consistentes entre módulos.

✅ Persistencia y seguridad mejoradas.

Estado Final: 🟢 Aprobado para merge en rama principal (main o release/v2.0)