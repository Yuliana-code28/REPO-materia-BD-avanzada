<?php

use Illuminate\Support\Facades\Route;

use App\Models\Cliente;
use App\Models\Habitacion;
use App\Models\Reserva;

Route::get('/admin-dashboard', function () {
    $totales = Habitacion::count();
    $disponibles = Habitacion::where('estado', 'disponible')->count();
    $activas = Habitacion::where('estado', 'ocupada')->count();
    $mantenimiento = Habitacion::where('estado', 'mantenimiento')->count();
    $ultimasReservas = Reserva::with('cliente')->orderBy('id_reserva', 'desc')->take(5)->get();

    return view('admin.welcome', compact('totales', 'disponibles', 'activas', 'mantenimiento', 'ultimasReservas'));
})->name('admin.dashboard');

Route::get('/admin/reservas', [App\Http\Controllers\AdminReservaController::class, 'mostrarVistaReservas'])->name('admin.reservas');

Route::get('/admin/habitaciones', [App\Http\Controllers\AdminHabitacionController::class, 'mostrarVistaHabitaciones'])->name('admin.habitaciones');
Route::post('/admin/habitaciones', [App\Http\Controllers\AdminHabitacionController::class, 'guardarHabitacionWeb'])->name('admin.habitaciones.store');
Route::put('/admin/habitaciones/{id}', [App\Http\Controllers\AdminHabitacionController::class, 'actualizarHabitacionWeb'])->name('admin.habitaciones.update');
Route::delete('/admin/habitaciones/{id}', [App\Http\Controllers\AdminHabitacionController::class, 'eliminarHabitacionWeb'])->name('admin.habitaciones.destroy');

Route::get('/admin/clientes', [App\Http\Controllers\AdminClienteController::class, 'mostrarVistaClientes'])->name('admin.clientes');
Route::get('/admin/empleados', [App\Http\Controllers\AdminEmpleadoController::class, 'mostrarVistaEmpleados'])->name('admin.empleados');
Route::get('/admin/facturacion', [App\Http\Controllers\AdminFacturacionController::class, 'mostrarVistaFacturacion'])->name('admin.facturacion');

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', function () {
    return view('login');
});

// Dashboard y módulos de Recepcionista
Route::get('/recepcionista-dashboard', [App\Http\Controllers\RecepcionistaDashboardController::class, 'mostrarVistaDashboard'])->name('recepcionista.dashboard');

Route::prefix('recepcionista')->group(function () {
    Route::get('/reservas', function() { return view('recepcionista.reservas'); })->name('recepcionista.reservas');
    Route::get('/habitaciones', function() { return view('recepcionista.habitaciones'); })->name('recepcionista.habitaciones');
    Route::get('/servicios', [App\Http\Controllers\ServicioConsumoController::class, 'mostrarVistaServicios'])->name('recepcionista.servicios');
});

Route::get('/cliente-dashboard', [App\Http\Controllers\ClienteDashboardController::class, 'mostrarVistaDashboard'])->name('cliente.dashboard');
Route::get('/cliente/reservas', [App\Http\Controllers\ClienteDashboardController::class, 'mostrarVistaReservas'])->name('cliente.reservas');

Route::get('/registro', function () {
    return view('registro');
});

Route::get('/contrasena-recuperar',function(){
   return view('recuperarContrasena');
});

Route::get('/cambiar-contrasena', function(){
   return view('cambiarContrasena');
});