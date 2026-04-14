USE sistemahotelero; 

DELIMITER // 

-- 1. PROCEDIMIENTO: sp_registrar_reserva (DINÁMICO SEGÚN FECHA)
DROP PROCEDURE IF EXISTS sp_registrar_reserva // 
CREATE PROCEDURE sp_registrar_reserva(
    IN p_id_cliente INT, 
    IN p_id_habitacion INT, 
    IN p_fecha_inicio DATE, 
    IN p_fecha_fin DATE, 
    IN p_monto_pago DECIMAL(10,2), 
    IN p_metodo_pago VARCHAR(50)
) 
BEGIN 
    DECLARE v_id_reserva INT; 
    DECLARE v_costo_calculado DECIMAL(10,2); 
    DECLARE v_estado_inicial VARCHAR(20) DEFAULT 'activa';

    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
    BEGIN 
        GET DIAGNOSTICS CONDITION 1 @text = MESSAGE_TEXT; 
        ROLLBACK; 
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = @text; 
    END; 

    -- DETERMINAR ESTADO SEGÚN FECHA
    IF p_fecha_inicio > CURDATE() THEN
        SET v_estado_inicial = 'pendiente';
    END IF;

    START TRANSACTION; 
        INSERT INTO reservas (id_cliente, fecha_registro, estado) 
        VALUES (p_id_cliente, CURRENT_TIMESTAMP, v_estado_inicial); 
        
        SET v_id_reserva = LAST_INSERT_ID(); 
        
        INSERT INTO detalle_reservas (id_reserva, id_habitacion, fecha_inicio, fecha_fin) 
        VALUES (v_id_reserva, p_id_habitacion, p_fecha_inicio, p_fecha_fin); 
        
        SET v_costo_calculado = fn_costo_estancia(v_id_reserva); 
        
        INSERT INTO pagos (id_reserva, monto, metodo_pago, fecha_pago) 
        VALUES (v_id_reserva, v_costo_calculado, p_metodo_pago, CURRENT_TIMESTAMP); 
    COMMIT; 
END // 


-- 2. PROCEDIMIENTO: sp_consultar_disponibilidad (CORREGIDO PARA IGNORAR CANCELADAS/FINALIZADAS Y CONSIDERAR PENDIENTES)
DROP PROCEDURE IF EXISTS sp_consultar_disponibilidad //
CREATE PROCEDURE sp_consultar_disponibilidad(
    IN p_fecha_inicio DATE,
    IN p_fecha_fin DATE
)
BEGIN
    SELECT h.*, th.nombre_tipo, th.precio_base
    FROM habitaciones h
    JOIN tipos_habitacion th ON h.id_tipo = th.id_tipo
    WHERE h.estado != 'mantenimiento'
    AND h.id_habitacion NOT IN (
        SELECT dr.id_habitacion 
        FROM detalle_reservas dr
        JOIN reservas r ON dr.id_reserva = r.id_reserva
        WHERE r.estado IN ('activa', 'pendiente')  -- <-- Bloquea habitaciones ya apartadas
        AND (p_fecha_inicio < dr.fecha_fin AND p_fecha_fin > dr.fecha_inicio)
    );
END //


-- 3. TRIGGER: tr_liberar_habitacion (PARA LIBERAR AL FINALIZAR/CANCELAR)
DROP TRIGGER IF EXISTS tr_liberar_habitacion //
CREATE TRIGGER tr_liberar_habitacion
AFTER UPDATE ON reservas
FOR EACH ROW
BEGIN
    IF (NEW.estado = 'finalizada' OR NEW.estado = 'cancelada') THEN
        UPDATE habitaciones 
        SET estado = 'disponible'
        WHERE id_habitacion IN (
            SELECT id_habitacion 
            FROM detalle_reservas 
            WHERE id_reserva = NEW.id_reserva
        );
    END IF;
END //


-- 4. TRIGGER: tr_evitar_traslapes (CONSIDERAR RESERVAS PENDIENTES)
DROP TRIGGER IF EXISTS tr_evitar_traslapes //
CREATE TRIGGER tr_evitar_traslapes
BEFORE INSERT ON detalle_reservas
FOR EACH ROW
BEGIN
    DECLARE v_conflicto INT;
    
    SELECT COUNT(*) INTO v_conflicto
    FROM detalle_reservas dr
    JOIN reservas r ON dr.id_reserva = r.id_reserva
    WHERE dr.id_habitacion = NEW.id_habitacion
    AND r.estado IN ('activa', 'pendiente')  -- <-- No permite traslapes con reservas apartadas
    AND (NEW.fecha_inicio < dr.fecha_fin AND NEW.fecha_fin > dr.fecha_inicio);
    
    IF v_conflicto > 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: La habitación ya se encuentra ocupada o reservada en el periodo seleccionado.';
    END IF;
END //

-- 5. TRIGGER: tr_evitar_sobreocupacion (CORREGIDO: SOLO PARA RESERVAS ACTIVAS)
DROP TRIGGER IF EXISTS tr_evitar_sobreocupacion //
CREATE TRIGGER tr_evitar_sobreocupacion
AFTER INSERT ON detalle_reservas
FOR EACH ROW
BEGIN
    DECLARE v_estado_reserva VARCHAR(20);
    
    SELECT estado INTO v_estado_reserva 
    FROM reservas 
    WHERE id_reserva = NEW.id_reserva;

    -- Solo marca como ocupada si la reserva inicia hoy (estado activa)
    IF v_estado_reserva = 'activa' THEN
        UPDATE habitaciones SET estado = 'ocupada' WHERE id_habitacion = NEW.id_habitacion;
    END IF;
END //

DELIMITER ;
