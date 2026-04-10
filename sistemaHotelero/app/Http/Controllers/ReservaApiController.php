<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReservaApiController extends Controller
{
    /**
     * Get all reservations from the vw_reservas view.
     */
    public function index(Request $request)
    {
        $query = DB::table('vw_reservas');

        if ($request->has('estado') && $request->estado != '') {
            $query->where('estado', $request->estado);
        }

        $reservas = $query->orderBy('id_reserva', 'desc')->get();

        return response()->json($reservas);
    }

    /**
     * Cancel a reservation by updating its status.
     */
    public function cancelar($id)
    {
        try {
            DB::beginTransaction();

            // 1. Actualizar estado de la reserva
            $affected = DB::table('reservas')
                ->where('id_reserva', $id)
                ->update(['estado' => 'cancelada']);

            if ($affected === 0) {
                throw new \Exception("No se encontró la reservación o ya está cancelada.");
            }

            // 2. Liberar la habitación vinculada
            // Buscamos el detalle para saber qué habitación liberar
            $detalle = DB::table('detalle_reservas')
                ->where('id_reserva', $id)
                ->first();

            if ($detalle) {
                DB::table('habitaciones')
                    ->where('id_habitacion', $detalle->id_habitacion)
                    ->update(['estado' => 'disponible']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Reservación cancelada correctamente y habitación liberada.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al cancelar: ' . $e->getMessage()
            ], 500);
        }
    }
}
