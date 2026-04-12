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

    return view('admin.welcome', compact('clientesCount', 'habitacionesDisponibles', 'reservasActivas', 'ultimasReservas'));
})->name('admin.dashboard');

Route::get('/admin/reservas', [App\Http\Controllers\AdminReservaController::class, 'index'])->name('admin.reservas');

Route::get('/admin/habitaciones', [App\Http\Controllers\AdminHabitacionController::class, 'index'])->name('admin.habitaciones');
Route::post('/admin/habitaciones', [App\Http\Controllers\AdminHabitacionController::class, 'store'])->name('admin.habitaciones.store');
Route::put('/admin/habitaciones/{id}', [App\Http\Controllers\AdminHabitacionController::class, 'update'])->name('admin.habitaciones.update');
Route::delete('/admin/habitaciones/{id}', [App\Http\Controllers\AdminHabitacionController::class, 'destroy'])->name('admin.habitaciones.destroy');

Route::get('/admin/clientes', [App\Http\Controllers\AdminClienteController::class, 'index'])->name('admin.clientes');
Route::get('/admin/empleados', [App\Http\Controllers\AdminEmpleadoController::class, 'index'])->name('admin.empleados');
Route::get('/admin/facturacion', [App\Http\Controllers\AdminFacturacionController::class, 'index'])->name('admin.facturacion');

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', function () {
    return view('login');
});

Route::get('/recepcionista-dashboard', function () {
    return view('recepcionista.dashboard_recepcionista');
});

Route::get('/cliente-dashboard', function () {
    return view('cliente.dashboard_cliente');
});

Route::get('/contrasena-recuperar',function(){
   return view('recuperarContrasena');
});

Route::get('/cambiar-contrasena', function(){
   return view('cambiarContrasena');
});