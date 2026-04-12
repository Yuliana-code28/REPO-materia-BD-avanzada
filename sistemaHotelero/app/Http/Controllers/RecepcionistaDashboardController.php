<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Habitacion;
use App\Models\Reserva;
use App\Models\DetalleReserva;
use Carbon\Carbon;

class RecepcionistaDashboardController extends Controller
{
    public function index()
    {
        $hoy = Carbon::today()->format('Y-m-d');

        // Estadísticas de habitaciones
        $habitacionesTotales = Habitacion::count();
        $disponibles = Habitacion::where('estado', 'disponible')->count();
        $ocupadas = Habitacion::where('estado', 'ocupada')->count();
        $mantenimiento = Habitacion::where('estado', 'mantenimiento')->count();

        // Entradas (Check-ins) del día
        $entradasHoy = DetalleReserva::where('fecha_inicio', $hoy)
            ->with(['reserva.cliente', 'habitacion'])
            ->get();

        // Salidas (Check-outs) del día
        $salidasHoy = DetalleReserva::where('fecha_fin', $hoy)
            ->with(['reserva.cliente', 'habitacion'])
            ->get();

        return view('recepcionista.dashboard_recepcionista', compact(
            'habitacionesTotales',
            'disponibles',
            'ocupadas',
            'mantenimiento',
            'entradasHoy',
            'salidasHoy'
        ));
    }
}
