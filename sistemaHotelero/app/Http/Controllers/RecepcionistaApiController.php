<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Habitacion;
use App\Models\Reserva;
use App\Models\DetalleReserva;
use Carbon\Carbon;
use \Illuminate\Support\Facades\DB;
class RecepcionistaApiController extends Controller
{
    public function obtenerDatosDashboard()
    {
        $hoy = Carbon::today()->format('Y-m-d');

        return response()->json([
            'stats' => [
                'totales' => Habitacion::count(),
                'disponibles' => Habitacion::where('estado', 'disponible')->count(),
                'ocupadas' => Habitacion::where('estado', 'ocupada')->count(),
                'mantenimiento' => Habitacion::where('estado', 'mantenimiento')->count(),
            ],
            'reservas_stats' => [
                'pendientes' => Reserva::where('estado', 'pendiente')->count(),
                'activas' => Reserva::where('estado', 'activa')->count(),
                'finalizadas' => Reserva::where('estado', 'finalizada')->count(),
                'canceladas' => Reserva::where('estado', 'cancelada')->count(),
            ]
        ]);
    }

    public function registrarCheckIn($id)
    {
        try {
            DB::beginTransaction();

            $reserva = Reserva::findOrFail($id);
            $reserva->update(['estado' => 'activa']);

            // Obtener habitaciones de la reserva para marcarlas como ocupadas
            $reserva->habitaciones()->update(['estado' => 'ocupada']);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Check-in realizado correctamente.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
