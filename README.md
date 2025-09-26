# 🌐 Proyecto Web en InfinityFree – [Fitness y Bienestar]
### Grupo 5

## 👥 Integrantes y Roles
- [Nombre completo] – Líder / Coordinador
- [Nombre completo] – Desarrollador Backend
- [Nombre completo] – Desarrollador Frontend / UI
- [Nombre completo] – Administrador de Base de Datos (DBA)
- [Nombre completo] – DevOps / Deployment
- [Nombre completo] – QA / Tester
- [Nombre completo] – Documentador / Presentador

---

## 📖 Descripción del Proyecto
**Plataforma de Evaluación y Rutinas Personalizadas.**  
Aplicación web que **recopila datos del usuario** (sexo, idioma, nivel, edad, altura, peso, disponibilidad, etc.) para **generar una rutina de entrenamiento personalizada**, con **login** y autorización por roles (**admin** / **usuario**).  
Stack: **PHP 8.x + MySQL** y despliegue en **InfinityFree**.

---

## 🚀 Instrucciones de Uso (Producción)
1. **Subir** los archivos del repositorio a la carpeta `htdocs` o `public_html` de tu hosting (InfinityFree).  
2. **Configurar conexión** en `conexion.php`:
   - Host: `sqlXXX.epizy.com`
   - Usuario: `[usuario asignado]`
   - Contraseña: `[contraseña asignada]`
   - Base de datos: `[db asignada]`
3. **Importar BD** con **phpMyAdmin** usando `crear_db.sql` (o `dump.sql` si ya tienen datos).
4. **Ingresar** al sitio desde la URL pública:  
   **[]**

---

## 🧭 Navegación principal (rutas)
- `GET /index.php` → Home / acceso.  
- `GET /formulario.php` → Formulario de perfil del usuario.  
- `POST /procesar.php` → Guardado/actualización del perfil en BD (prepared statements).  
- `GET /listar.php` → Listado de perfiles (**solo rol `admin`**).  

---

## 🗂️ Estructura del Proyecto

/
├─ crear_db.sql              
├─ Headers_Dataset-Personas.NUM.v2.csv 
├─ conexion.php                
├─ index.php                   
├─ formulario.php              
├─ procesar.php                
├─ listar.php                  
├─ setup.php                   
└─  dump.sql

---

## 🧱 Modelo de Datos (tabla principal)
La tabla **`usuarios`** centraliza el perfil que utilizará la lógica de recomendación de rutina.

| Campo                    | Tipo         | Req | Descripción                                      |
|--------------------------|--------------|-----|--------------------------------------------------|
| `user_idx` (PK, AI)      | INT          | ✔   | Identificador interno                            |
| `sexo`                   | VARCHAR(50)  | ✔   | Masculino/Femenino/Otro                          |
| `idioma`                 | VARCHAR(50)  | ✔   | es/en/...                                        |
| `nivel`                  | VARCHAR(50)  | ✔   | novato/intermedio/avanzado                       |
| `edad`                   | INT          | –   | Años                                             |
| `altura_cm`              | INT          | –   | Centímetros                                      |
| `peso_kg`                | DECIMAL(5,2) | –   | Kilogramos                                       |
| `training_age_anos`      | INT          | –   | Años acumulados entrenando                       |
| `dias_disp_sem`          | INT          | –   | Días disponibles por semana                      |
| `tiempo_por_sesion_min`  | INT          | –   | Minutos por sesión                               |
| `fecha_registro`         | TIMESTAMP    | ✔   | `CURRENT_TIMESTAMP`                              |


---

## ✨ Funcionalidades Clave
- **Login + roles** (`admin`, `usuario`) con sesiones seguras.
- **Formulario** para crear/actualizar el perfil (`usuarios`).
- **Listado** y búsqueda (solo `admin`).
- **Backups** con `dump.sql` (phpMyAdmin).

---

## 🧰 Requisitos y Configuración (Desarrollo)
- **Requisitos**: PHP ≥ 8.0, MySQL 8.x, servidor local (XAMPP/LAMP/WAMP).
- **Variables** (local): usar `.env` (opcional) o editar `conexion.php`.

| Variable  | Ejemplo      | Descripción            |
|-----------|--------------|------------------------|
| DB_HOST   | localhost    | Host de MySQL          |
| DB_USER   | root         | Usuario MySQL          |
| DB_PASS   | ***          | Contraseña MySQL       |
| DB_NAME   | formulario_db| Base de datos del proyecto |

**Pasos (dev):**
1. Crear BD y **importar** `crear_db.sql`.  
2. Configurar credenciales en `conexion.php`.  
3. Probar `index.php`, `formulario.php`, `listar.php`.

---

## 🔐 Seguridad (mínimos recomendados)
- **Sesiones**: `session_start()` + `session_regenerate_id(true)` tras login.
- **Contraseñas**: `password_hash()` / `password_verify()` si gestionan usuarios propios.
- **SQL**: **prepared statements** en TODAS las consultas.
- **XSS/CSRF**: escapar salidas (`htmlspecialchars`) + token CSRF en formularios críticos.
- **CSV injection**: prefijar `'` cuando un campo empiece por `= + - @`.
- **Producción**: `display_errors=Off`, logs habilitados; **no** versionar credenciales ni `dump.sql`.

---

## 🧪 Evidencias de Despliegue
- URL del sitio: **[enlace]**  
- Captura de **phpMyAdmin** con ≥3 registros → `capturas/phpmyadmin.png`  
- Captura de **File Manager** con archivos subidos → `capturas/filemanager.png`  
- Captura del **sitio funcionando** → `capturas/sitio.png`

---

## 📦 Archivos Entregados
- `codigo.zip` — Código completo del proyecto  
- `dump.sql` — Export de base de datos  
- `qa-report.md` — Reporte de pruebas  
- `capturas/` — Evidencias gráficas

---

## 📝 Changelog (registro de cambios)
- [Nombre] — Implementó validaciones y **prepared statements**.  
- [Nombre] — Mejoró la interfaz y organizó assets (`static/`).  
- [Nombre] — Configuró la BD y generó `dump.sql`.  
- [Nombre] — Publicó en InfinityFree.  
- [Nombre] — Realizó QA y documentó resultados.  
- [Nombre] — Redactó README y preparó presentación.

> Sugerencia: Llevar un `CHANGELOG.md` con versiones y fechas.

---

## ☁️ Preguntas de Reflexión (Cloud)
1. **¿Qué es despliegue y cómo lo hicieron?**  
    > 

2. **¿Qué limitaciones encontraron en InfinityFree?**  
   > 

3. **Servicios equivalentes en AWS/Azure/GCP**  
   - **Archivos estáticos**:
   - **Base de datos**: 
   - **Hosting del sitio**: 

4. **Escalabilidad y alta disponibilidad**  
   > 

5. **Plan de migración (4–5 pasos)**  
   1) Mover estáticos a **S3/Blob/GCS** + **CDN**.  
   2) Migrar MySQL a **RDS/Cloud SQL** y configurar backups automáticos.  
   3) Desplegar app en **App Service/Beanstalk/App Engine** con **CI/CD** (GitHub Actions).  
   4) Añadir **WAF**, **Secret Manager** y variables de entorno seguras.  
   5) Habilitar **autoescalado** y observabilidad (métricas, logs, alertas).


