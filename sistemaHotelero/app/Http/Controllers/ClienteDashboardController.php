<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClienteDashboardController extends Controller
{
    public function mostrarVistaDashboard()
    {
        return view('cliente.dashboard_cliente');
    }

    public function mostrarVistaReservas()
    {
        return view('cliente.reservas');
    }
}
