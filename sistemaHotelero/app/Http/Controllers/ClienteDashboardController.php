<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClienteDashboardController extends Controller
{
    public function index()
    {
        return view('cliente.dashboard_cliente');
    }

    public function reservas()
    {
        return view('cliente.reservas');
    }
}
