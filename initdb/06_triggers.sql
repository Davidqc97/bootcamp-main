-- =================================
-- TRIGGERS PARA AUDITORÍA
-- =================================

-- Tabla de auditoría
CREATE TABLE IF NOT EXISTS auditoria_mensajes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mensaje_id INT,
    accion ENUM('INSERT', 'UPDATE', 'DELETE'),
    usuario VARCHAR(50),
    fecha_accion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    datos_anteriores JSON,
    datos_nuevos JSON
);

DELIMITER //

-- Trigger para INSERT
CREATE TRIGGER tr_mensajes_insert 
AFTER INSERT ON mensajes
FOR EACH ROW
BEGIN
    INSERT INTO auditoria_mensajes (mensaje_id, accion, usuario, datos_nuevos)
    VALUES (NEW.id, 'INSERT', USER(), 
        JSON_OBJECT('id', NEW.id, 'nombre', NEW.nombre, 'correo', NEW.correo, 'mensaje', NEW.mensaje, 'fecha', NEW.fecha)
    );
END //

-- Trigger para UPDATE
CREATE TRIGGER tr_mensajes_update 
AFTER UPDATE ON mensajes
FOR EACH ROW
BEGIN
    INSERT INTO auditoria_mensajes (mensaje_id, accion, usuario, datos_anteriores, datos_nuevos)
    VALUES (NEW.id, 'UPDATE', USER(), 
        JSON_OBJECT('id', OLD.id, 'nombre', OLD.nombre, 'correo', OLD.correo, 'mensaje', OLD.mensaje, 'fecha', OLD.fecha),
        JSON_OBJECT('id', NEW.id, 'nombre', NEW.nombre, 'correo', NEW.correo, 'mensaje', NEW.mensaje, 'fecha', NEW.fecha)
    );
END //

-- Trigger para DELETE
CREATE TRIGGER tr_mensajes_delete 
BEFORE DELETE ON mensajes
FOR EACH ROW
BEGIN
    INSERT INTO auditoria_mensajes (mensaje_id, accion, usuario, datos_anteriores)
    VALUES (OLD.id, 'DELETE', USER(), 
        JSON_OBJECT('id', OLD.id, 'nombre', OLD.nombre, 'correo', OLD.correo, 'mensaje', OLD.mensaje, 'fecha', OLD.fecha)
    );
END //

DELIMITER ;