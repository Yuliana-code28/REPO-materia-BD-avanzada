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

    

        return view('recepcionista.dashboard_recepcionista', compact(
            'habitacionesTotales',
            'disponibles',
            'ocupadas',
            'mantenimiento',
        ));
    }
}
