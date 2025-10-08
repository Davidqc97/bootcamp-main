-- =================================
-- DATOS DE PRUEBA PARA TESTING
-- =================================

INSERT INTO mensajes (nombre, correo, mensaje, fecha) VALUES
('Juan Pérez', 'juan.perez@example.com', 'Primer mensaje de prueba del sistema', '2025-01-01 10:00:00'),
('María García', 'maria.garcia@example.com', 'Segundo mensaje para validar funcionalidad', '2025-01-02 14:30:00'),
('Carlos López', 'carlos.lopez@example.com', 'Mensaje de prueba para índices', '2025-01-03 09:15:00'),
('Ana Rodríguez', 'ana.rodriguez@example.com', 'Testing de la aplicación web', '2025-01-04 16:45:00'),
('Luis Martínez', 'luis.martinez@example.com', 'Validación de rendimiento de consultas', '2025-01-05 11:20:00');

-- Verificar datos insertados
SELECT COUNT(*) as total_mensajes FROM mensajes;
SELECT * FROM mensajes ORDER BY fecha DESC LIMIT 5;