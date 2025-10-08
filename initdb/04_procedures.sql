-- =================================
-- PROCEDIMIENTOS ALMACENADOS
-- =================================

DELIMITER //

-- Procedimiento para insertar mensajes con validación
CREATE PROCEDURE sp_insertar_mensaje(
    IN p_nombre VARCHAR(100),
    IN p_correo VARCHAR(120),
    IN p_mensaje TEXT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;
    
    START TRANSACTION;
    
    -- Validaciones básicas
    IF p_nombre IS NULL OR TRIM(p_nombre) = '' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El nombre no puede estar vacío';
    END IF;
    
    IF p_correo IS NULL OR TRIM(p_correo) = '' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El correo no puede estar vacío';
    END IF;
    
    IF p_mensaje IS NULL OR TRIM(p_mensaje) = '' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El mensaje no puede estar vacío';
    END IF;
    
    -- Insertar el mensaje
    INSERT INTO mensajes (nombre, correo, mensaje) 
    VALUES (TRIM(p_nombre), TRIM(p_correo), TRIM(p_mensaje));
    
    COMMIT;
END //

-- Procedimiento para obtener estadísticas
CREATE PROCEDURE sp_estadisticas_mensajes()
BEGIN
    SELECT 
        COUNT(*) as total_mensajes,
        COUNT(DISTINCT correo) as usuarios_unicos,
        MIN(fecha) as primer_mensaje,
        MAX(fecha) as ultimo_mensaje,
        AVG(CHAR_LENGTH(mensaje)) as longitud_promedio_mensaje
    FROM mensajes;
END //

-- Función para limpiar mensajes antiguos
CREATE PROCEDURE sp_limpiar_mensajes_antiguos(IN dias_antiguedad INT)
BEGIN
    DECLARE mensajes_eliminados INT DEFAULT 0;
    
    DELETE FROM mensajes 
    WHERE fecha < DATE_SUB(NOW(), INTERVAL dias_antiguedad DAY);
    
    SET mensajes_eliminados = ROW_COUNT();
    
    SELECT CONCAT('Se eliminaron ', mensajes_eliminados, ' mensajes antiguos') as resultado;
END //

DELIMITER ;