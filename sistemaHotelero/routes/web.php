<?php

use Illuminate\Support\Facades\Route;

use App\Models\Cliente;
use App\Models\Habitacion;
use App\Models\Reserva;

Route::get('/admin-dashboard', function () {
    $clientesCount = Cliente::count();
    $habitacionesDisponibles = Habitacion::where('estado', 'disponible')->count();
    $reservasActivas = Reserva::where('estado', 'activa')->count();
    $ultimasReservas = Reserva::with('cliente')->orderBy('id_reserva', 'desc')->take(5)->get();

    return view('welcome', compact('clientesCount', 'habitacionesDisponibles', 'reservasActivas', 'ultimasReservas'));
});

Route::get('/login', function () {
    return view('login');
});
