CREATE TRIGGER tr_liberar_habitacion
AFTER UPDATE ON reservas
FOR EACH ROW
BEGIN
    -- Si el estado cambia a finalizada o cancelada
    IF (NEW.estado = 'finalizada' OR NEW.estado = 'cancelada') THEN
        -- Actualizar la habitación vinculada a disponible
        UPDATE habitaciones 
        SET estado = 'disponible'
        WHERE id_habitacion IN (
            SELECT id_habitacion 
            FROM detalle_reservas 
            WHERE id_reserva = NEW.id_reserva
        );
    END IF;
END;
