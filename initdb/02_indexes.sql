-- =================================
-- ÍNDICES PARA OPTIMIZACIÓN
-- =================================

-- Índice para búsquedas por correo
CREATE INDEX idx_mensajes_correo ON mensajes(correo);

-- Índice para ordenamiento por fecha
CREATE INDEX idx_mensajes_fecha ON mensajes(fecha DESC);

-- Índice compuesto para búsquedas frecuentes
CREATE INDEX idx_mensajes_nombre_fecha ON mensajes(nombre, fecha DESC);

-- Verificar índices creados
SHOW INDEX FROM mensajes;