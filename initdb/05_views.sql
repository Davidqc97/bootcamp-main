-- =================================
-- VISTAS PARA REPORTING
-- =================================

-- Vista para mensajes recientes (últimos 30 días)
CREATE VIEW v_mensajes_recientes AS
SELECT 
    id,
    nombre,
    correo,
    LEFT(mensaje, 100) as mensaje_preview,
    fecha,
    DATEDIFF(NOW(), fecha) as dias_desde_envio
FROM mensajes 
WHERE fecha >= DATE_SUB(NOW(), INTERVAL 30 DAY)
ORDER BY fecha DESC;

-- Vista para estadísticas por día
CREATE VIEW v_estadisticas_diarias AS
SELECT 
    DATE(fecha) as fecha_dia,
    COUNT(*) as total_mensajes,
    COUNT(DISTINCT correo) as usuarios_unicos,
    AVG(CHAR_LENGTH(mensaje)) as longitud_promedio
FROM mensajes
GROUP BY DATE(fecha)
ORDER BY fecha_dia DESC;

-- Vista para usuarios más activos
CREATE VIEW v_usuarios_activos AS
SELECT 
    correo,
    nombre,
    COUNT(*) as total_mensajes,
    MAX(fecha) as ultimo_mensaje,
    MIN(fecha) as primer_mensaje
FROM mensajes
GROUP BY correo, nombre
HAVING COUNT(*) > 1
ORDER BY total_mensajes DESC;