-- =================================
-- MIGRACIÓN v1.6.0
-- Agregar columna de estado a mensajes
-- =================================

-- Agregar columna status
ALTER TABLE mensajes 
ADD COLUMN status ENUM('activo', 'archivado', 'eliminado') DEFAULT 'activo' AFTER mensaje;

-- Crear índice para la nueva columna
CREATE INDEX idx_mensajes_status ON mensajes(status);

-- Actualizar vista de mensajes recientes para incluir status
DROP VIEW IF EXISTS v_mensajes_recientes;
CREATE VIEW v_mensajes_recientes AS
SELECT 
    id,
    nombre,
    correo,
    LEFT(mensaje, 100) as mensaje_preview,
    status,
    fecha,
    DATEDIFF(NOW(), fecha) as dias_desde_envio
FROM mensajes 
WHERE fecha >= DATE_SUB(NOW(), INTERVAL 30 DAY)
AND status = 'activo'
ORDER BY fecha DESC;

-- Registrar migración
INSERT INTO auditoria_mensajes (mensaje_id, accion, usuario, datos_nuevos)
VALUES (0, 'MIGRATION', USER(), 
    JSON_OBJECT('version', 'v1.6.0', 'description', 'Added status column', 'date', NOW())
);