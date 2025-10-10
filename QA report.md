# 🧪 QA Report – Docker Web Auth Architecture

**Proyecto:** Bootcamp – Taller Docker Web con Autenticación  
**Autor QA:** José David Escalante  
**Fecha:** 2025-10-10  
**Estado:** ✅ Validado con observaciones menores  
**Etiquetas:** `qa` `bugfix` `docker` `auth-module`  

---

## 🎯 Objetivo

Validar la nueva arquitectura modular basada en contenedores Docker, verificando la correcta integración entre los servicios:

- `app` → aplicación principal  
- `auth` → autenticación de usuarios  
- `db` → base de datos MySQL  
- `phpmyadmin` → administración  

Y confirmar que se resolvieron los errores reportados en la versión anterior:

| Error detectado | Estado actual |
|------------------|----------------|
| ❌ `../app/db.php` no encontrado | ✅ `shared/db.php` montado correctamente |
| ❌ Rutas inconsistentes | ✅ Uso de variables en `.env` y URLs absolutas |
| ❌ Contenedores aislados | ✅ Volúmenes y red compartida |
| ❌ Redirección incorrecta | ✅ Flujo entre `auth → app` corregido |

---

## 🧩 Hallazgos y Verificación

| Categoría | Antes | Después | Resultado |
|------------|--------|----------|------------|
| **Conexión a BD** | Rutas relativas `../app/db.php` (fallaba si cambiaba el root) | Se centralizó conexión en `shared/db.php` y se montó como volumen en `app` y `auth` | ✅ |
| **Variables / Configuración** | Rutas y URLs hardcodeadas | Variables en `.env` (`BASE_URL_APP`, `BASE_URL_AUTH`) y `config.php` | ✅ |
| **Red entre contenedores** | Servicios aislados | Red compartida por Docker Compose (`hostname: db`) y volúmenes sincronizados | ✅ |
| **Redirecciones** | `/login.php` o `/` relativos | URLs absolutas (`http://localhost:8080`, `http://localhost:8082`) | ✅ |
| **JWT** | Sin expiración ni validación robusta | Token firmado con `JWT_SECRET` y expiración controlada por `JWT_EXP_HOURS` | ✅ |
| **Persistencia** | Datos de MySQL no persistentes | Volumen `db_data` montado correctamente | ✅ |

---

## ⚙️ Archivos revisados

- `docker-compose.yml` → servicios, dependencias y volúmenes  
- `Dockerfile` → entorno PHP 8.2 con soporte `pdo_mysql`  
- `.env` → centralización de configuración  
- `jwt.php`, `login.php`, `register.php`, `logout.php` → flujo JWT  
- `01_schema.sql` → estructura inicial de BD  
- `README.md` → documentación de despliegue y pruebas  

---

## 🧠 Observaciones y Recomendaciones

- 🔐 **Seguridad:** mover `JWT_SECRET` fuera del repo público (usar GitHub Secrets o `.env.local`).  
- ⚙️ **Mantenimiento:** crear un archivo `config.php` en `shared/` para centralizar constantes (`DB_HOST`, `BASE_URL_*`, `JWT_SECRET`).  
- 🧭 **UX:** mejorar mensajes de éxito/error en `login` y `register`.  
- 🤖 **CI/CD:** automatizar el `docker-compose up --build` con GitHub Actions para validaciones QA.  

---

## ✅ Conclusión

- Todos los errores críticos fueron resueltos.  
- La arquitectura es modular, reproducible y consistente entre contenedores.  
- El entorno QA confirma que el sistema funciona correctamente con login, registro y conexión a BD.  

**Estado final:** 🟢 **Aprobado para merge a rama principal (`main` o `release/v2.0`)**

---

### 📋 Checklist QA

- [x] Contenedores levantan sin errores (`docker-compose up`)  
- [x] Conexión exitosa a BD (`db` accesible)  
- [x] Login y registro funcionales  
- [x] Redirecciones correctas entre módulos  
- [x] Persistencia en volumen `db_data`  
- [x] JWT válido y con expiración configurada  

---

**Revisado por:**  
👤 *José David Escalante*  
🧩 *Administrador de infraestructura & QA técnico*
