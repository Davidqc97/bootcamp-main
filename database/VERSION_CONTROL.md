# 📊 **Control de Versiones de Base de Datos**

## Versión Actual: **v1.0.0**

### **Historial de Versiones**

| Versión | Fecha | Descripción | Archivos |
|---------|-------|-------------|----------|
| v1.0.0  | 2025-10-07 | Versión inicial con tabla mensajes | `01_schema.sql` |
| v1.1.0  | 2025-10-07 | Agregados índices de optimización | `02_indexes.sql` |
| v1.2.0  | 2025-10-07 | Datos de prueba y testing | `03_test_data.sql` |
| v1.3.0  | 2025-10-07 | Procedimientos almacenados | `04_procedures.sql` |
| v1.4.0  | 2025-10-07 | Vistas para reporting | `05_views.sql` |
| v1.5.0  | 2025-10-07 | Sistema de auditoría con triggers | `06_triggers.sql` |

### **Estructura de Archivos SQL**
```
initdb/
├── 01_schema.sql      # Estructura principal
├── 02_indexes.sql     # Índices de optimización
├── 03_test_data.sql   # Datos de prueba
├── 04_procedures.sql  # Procedimientos almacenados
├── 05_views.sql       # Vistas para reporting
├── 06_triggers.sql    # Sistema de auditoría
└── migrations/        # Futuras migraciones
    ├── v1.6.0_add_status_column.sql
    └── v1.7.0_add_categories_table.sql
```

### **Proceso de Migración**
1. **Backup** de la base de datos actual
2. **Ejecutar** scripts en orden numérico
3. **Validar** cambios aplicados
4. **Actualizar** versión en documentación

### **Comandos de Versionado**
```bash
# Crear backup antes de migración
docker exec -it container_db mysqldump -u root -p appdb > backup_v1.0.0.sql

# Aplicar nueva versión
docker exec -it container_db mysql -u root -p appdb < migration_v1.1.0.sql

# Verificar versión aplicada
docker exec -it container_db mysql -u root -p -e "SELECT VERSION() as mysql_version, NOW() as applied_date;"
```