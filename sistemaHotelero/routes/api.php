<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminHabitacionController;
use App\Http\Controllers\AdminClienteController;
use App\Http\Controllers\EmpleadoApiController;
use App\Http\Controllers\ReservaApiController;
use App\Http\Controllers\RecepcionistaApiController;
use App\Http\Controllers\ServicioConsumoController;
use App\Http\Controllers\AdminFacturacionController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/registro', [AuthController::class, 'registro']);

Route::post('/password/recuperar',[AuthController::class,'olvideMicontrasena']);
Route::post('/password/restablecer',[AuthController::class,'cambiarContrasena']);
Route::post('/logout', [AuthController::class, 'logout']);


// Rutas de Recepcionista (API)
Route::get('/recepcionista/dashboard-data', [RecepcionistaApiController::class, 'getDashboardData']);
Route::post('/recepcionista/reservas/{id}/check-in', [RecepcionistaApiController::class, 'checkIn']);
Route::get('/recepcionista/reservas-activas', [ServicioConsumoController::class, 'getActiveReservations']);
Route::get('/recepcionista/servicios', [ServicioConsumoController::class, 'getServices']);
Route::post('/recepcionista/consumos', [ServicioConsumoController::class, 'store']);


Route::get('/admin/reservas', [ReservaApiController::class, 'index']);
Route::get('/admin/reservas/form-data', [ReservaApiController::class, 'formData']);
Route::get('/admin/reservas/calcular-costo', [ReservaApiController::class, 'calcularCosto']);
Route::get('/admin/reservas/disponibilidad', [ReservaApiController::class, 'apiDisponibilidad']);
Route::post('/admin/reservas', [ReservaApiController::class, 'store']);
Route::patch('/admin/reservas/{id}/cancelar', [ReservaApiController::class, 'cancelar']);
Route::patch('/admin/reservas/{id}/finalizar', [ReservaApiController::class, 'finalizar']);

Route::get('/admin/habitaciones', [AdminHabitacionController::class, 'apiIndex']);
Route::get('/admin/habitaciones/form-data', [AdminHabitacionController::class, 'formData']);
Route::post('/admin/habitaciones', [AdminHabitacionController::class, 'apiStore']);
Route::put('/admin/habitaciones/{id}', [AdminHabitacionController::class, 'apiUpdate']);
Route::delete('/admin/habitaciones/{id}', [AdminHabitacionController::class, 'apiDestroy']);

Route::get('/admin/clientes', [AdminClienteController::class, 'apiIndex']);
Route::get('/admin/facturacion', [AdminFacturacionController::class, 'apiIndex']);
Route::get('/admin/facturacion/reportes', [AdminFacturacionController::class, 'apiReportes']);

Route::get('/admin/empleados', [EmpleadoApiController::class, 'index']);
Route::get('/admin/roles', [EmpleadoApiController::class, 'roles']);
Route::post('/admin/empleados', [EmpleadoApiController::class, 'store']);
Route::put('/admin/empleados/{id}', [EmpleadoApiController::class, 'update']);
Route::delete('/admin/empleados/{id}', [EmpleadoApiController::class, 'destroy']);

// Rutas de Cliente
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/cliente/dashboard-summary', [App\Http\Controllers\ClienteApiController::class, 'getDashboardSummary']);
    Route::get('/cliente/reservas', [App\Http\Controllers\ClienteApiController::class, 'getReservas']);
    
    // Rutas para que el cliente haga su propia reserva
    Route::get('/cliente/reservas/disponibilidad', [ReservaApiController::class, 'apiDisponibilidad']);
    Route::get('/cliente/reservas/calcular-costo', [ReservaApiController::class, 'calcularCosto']);
    Route::post('/cliente/reservas', [App\Http\Controllers\ClienteApiController::class, 'storePropiaReserva']);
});
