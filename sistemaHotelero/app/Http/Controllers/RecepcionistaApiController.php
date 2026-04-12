<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Habitacion;
use App\Models\Reserva;
use App\Models\DetalleReserva;
use Carbon\Carbon;

class RecepcionistaApiController extends Controller
{
    public function getDashboardData()
    {
        $hoy = Carbon::today()->format('Y-m-d');

        return response()->json([
            'stats' => [
                'totales' => Habitacion::count(),
                'disponibles' => Habitacion::where('estado', 'disponible')->count(),
                'ocupadas' => Habitacion::where('estado', 'ocupada')->count(),
                'mantenimiento' => Habitacion::where('estado', 'mantenimiento')->count(),
            ]
        ]);
    }

    public function checkIn($id)
    {
        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $reserva = Reserva::findOrFail($id);
            $reserva->update(['estado' => 'activa']);

            // Obtener habitaciones de la reserva para marcarlas como ocupadas
            $reserva->habitaciones()->update(['estado' => 'ocupada']);

            \Illuminate\Support\Facades\DB::commit();

            return response()->json(['success' => true, 'message' => 'Check-in realizado correctamente.']);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
