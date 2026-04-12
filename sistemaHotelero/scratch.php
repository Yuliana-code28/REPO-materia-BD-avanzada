<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Reserva;
use App\Models\DetalleReserva;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

$hoy = Carbon::today()->format('Y-m-d');
echo "HOY: " . $hoy . "\n";

// Cambiar una reserva a "pendiente" para que inicie hoy
$res = Reserva::where('estado', '!=', 'pendiente')->orderBy('id_reserva', 'desc')->first();
if ($res) {
    $res->update(['estado' => 'pendiente']);
    DetalleReserva::where('id_reserva', $res->id_reserva)->update([
        'fecha_inicio' => $hoy,
        'fecha_fin' => Carbon::today()->addDays(2)->format('Y-m-d')
    ]);
    echo "Actualizada reserva ID " . $res->id_reserva . " para Check-in hoy.\n";
}

// Cambiar una reserva activa para que termine hoy
$res2 = Reserva::where('estado', 'activa')->where('id_reserva', '!=', $res->id_reserva ?? 0)->first();
if ($res2) {
    DetalleReserva::where('id_reserva', $res2->id_reserva)->update([
        'fecha_fin' => $hoy
    ]);
    echo "Actualizada reserva ID " . $res2->id_reserva . " para Check-out hoy.\n";
}
