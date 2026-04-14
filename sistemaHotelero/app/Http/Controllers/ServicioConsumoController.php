<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reserva;
use App\Models\Servicio;
use Illuminate\Support\Facades\DB;

class ServicioConsumoController extends Controller
{
    public function mostrarVistaServicios()
    {
        return view('recepcionista.servicios');
    }

    public function obtenerReservasActivas()
    {
        // Solo habitaciones ocupadas (activas)
        $reservas = Reserva::where('estado', 'activa')
            ->with(['cliente', 'habitaciones'])
            ->get();
        return response()->json($reservas);
    }

    public function listarServiciosDisponibles()
    {
        return response()->json(Servicio::all());
    }

    public function registrarConsumoServicio(Request $request)
    {
        $request->validate([
            'id_reserva' => 'required|exists:reservas,id_reserva',
            'id_servicio' => 'required|exists:servicios,id_servicio',
            'cantidad' => 'required|integer|min:1'
        ]);

        try {
            DB::table('consumos_servicios')->insert([
                'id_reserva' => $request->id_reserva,
                'id_servicio' => $request->id_servicio,
                'cantidad' => $request->cantidad,
                'fecha_consumo' => now()
            ]);

            return response()->json(['success' => true, 'message' => 'Servicio registrado correctamente.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al registrar: ' . $e->getMessage()], 500);
        }
    }
}
