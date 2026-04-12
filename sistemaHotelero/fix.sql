USE sistemahotelero; 

DELIMITER // 

DROP PROCEDURE IF EXISTS sp_registrar_reserva // 

/*
 * PROCEDIMIENTO: sp_registrar_reserva
 * 
 * PROBLEMA RESUELTO: 
 * Anteriormente, todas las reservaciones se creaban con estado 'activa', independientemente 
 * de la fecha de inicio. Esto causaba que reservaciones a futuro (ej. dentro de un mes) 
 * aparecieran como huéspedes actuales, afectando la precisión del inventario de ocupación 
 * y complicando la labor del recepcionista al no distinguir llegadas futuras de estancias actuales.
 * 
 * JUSTIFICACIÓN TÉCNICA:
 * Se implementa una validación lógica que compara la fecha de inicio (p_fecha_inicio) con 
 * la fecha actual (CURDATE). Esto permite que el sistema de forma autónoma asigne el 
 * estado 'pendiente' a reservaciones futuras, integrándose perfectamente con el flujo 
 * de Check-in del hotel.
 */
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

    -- Gestión de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
    BEGIN 
        GET DIAGNOSTICS CONDITION 1 @sqlstate = RETURNED_SQLSTATE, @errno = MYSQL_ERRNO, @text = MESSAGE_TEXT; 
        ROLLBACK; 
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = @text; 
    END; 

    -- DETERMINACIÓN LÓGICA DEL ESTADO
    -- Si la fecha de inicio es después de hoy, la reserva queda en espera (pendiente).
    IF p_fecha_inicio > CURDATE() THEN
        SET v_estado_inicial = 'pendiente';
    END IF;

    START TRANSACTION; 
        -- Inserción con estado dinámico
        INSERT INTO reservas (id_cliente, fecha_registro, estado) 
        VALUES (p_id_cliente, CURRENT_TIMESTAMP, v_estado_inicial); 
        
        SET v_id_reserva = LAST_INSERT_ID(); 
        
        -- Registro del detalle de estancia
        INSERT INTO detalle_reservas (id_reserva, id_habitacion, fecha_inicio, fecha_fin) 
        VALUES (v_id_reserva, p_id_habitacion, p_fecha_inicio, p_fecha_fin); 
        
        -- Cálculo automático del costo mediante función almacenada
        SET v_costo_calculado = fn_costo_estancia(v_id_reserva); 
        
        -- Registro del pago inicial vinculado
        INSERT INTO pagos (id_reserva, monto, metodo_pago, fecha_pago) 
        VALUES (v_id_reserva, v_costo_calculado, p_metodo_pago, CURRENT_TIMESTAMP); 
    COMMIT; 
END // 

DELIMITER ;
