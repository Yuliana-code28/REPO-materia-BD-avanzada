<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    DB::unprepared("DROP PROCEDURE IF EXISTS sp_registrar_reserva;");

    $procedure = "
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

        DECLARE EXIT HANDLER FOR SQLEXCEPTION 
        BEGIN 
            GET DIAGNOSTICS CONDITION 1 @sqlstate = RETURNED_SQLSTATE, @errno = MYSQL_ERRNO, @text = MESSAGE_TEXT; 
            ROLLBACK; 
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = @text; 
        END; 

        START TRANSACTION; 
            INSERT INTO reservas (id_cliente, fecha_registro, estado) 
            VALUES (p_id_cliente, CURRENT_TIMESTAMP, 'activa'); 

            SET v_id_reserva = LAST_INSERT_ID(); 
            
            INSERT INTO detalle_reservas (id_reserva, id_habitacion, fecha_inicio, fecha_fin) 
            VALUES (v_id_reserva, p_id_habitacion, p_fecha_inicio, p_fecha_fin); 
            
            SET v_costo_calculado = fn_costo_estancia(v_id_reserva); 
            
            INSERT INTO pagos (id_reserva, monto, metodo_pago, fecha_pago) 
            VALUES (v_id_reserva, v_costo_calculado, p_metodo_pago, CURRENT_TIMESTAMP); 
        COMMIT; 
    END";

    DB::unprepared($procedure);
    echo "¡Procedimiento compilado exitosamente en MySQL!\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
