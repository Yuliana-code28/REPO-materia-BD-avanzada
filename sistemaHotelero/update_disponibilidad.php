<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    DB::unprepared("DROP PROCEDURE IF EXISTS sp_consultar_disponibilidad;");
    
    $procedure = "
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
            SELECT id_habitacion 
            FROM detalle_reservas 
            WHERE (p_fecha_inicio < fecha_fin AND p_fecha_fin > fecha_inicio)
        );
    END";

    DB::unprepared($procedure);
    echo "¡Procedimiento de Disponibilidad actualizado exitosamente!\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
