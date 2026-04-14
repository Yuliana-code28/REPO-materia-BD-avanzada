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

Route::post('/login', [AuthController::class, 'autenticarUsuario']);
Route::post('/registro', [AuthController::class, 'registrarCliente']);

Route::post('/password/recuperar',[AuthController::class,'enviarEnlaceRecuperacion']);
Route::post('/password/restablecer',[AuthController::class,'restablecerContrasena']);
Route::post('/logout', [AuthController::class, 'cerrarSesion']);


// Rutas de Recepcionista (API)
Route::get('/recepcionista/dashboard-data', [RecepcionistaApiController::class, 'obtenerDatosDashboard']);
Route::post('/recepcionista/reservas/{id}/check-in', [RecepcionistaApiController::class, 'registrarCheckIn']);
Route::get('/recepcionista/reservas-activas', [ServicioConsumoController::class, 'obtenerReservasActivas']);
Route::get('/recepcionista/servicios', [ServicioConsumoController::class, 'listarServiciosDisponibles']);
Route::post('/recepcionista/consumos', [ServicioConsumoController::class, 'registrarConsumoServicio']);


Route::get('/admin/reservas', [ReservaApiController::class, 'listarReservas']);
Route::get('/admin/reservas/form-data', [ReservaApiController::class, 'obtenerDatosFormularioReserva']);
Route::get('/admin/reservas/calcular-costo', [ReservaApiController::class, 'calcularCostoEstancia']);
Route::get('/admin/reservas/disponibilidad', [ReservaApiController::class, 'consultarDisponibilidadHabitaciones']);
Route::post('/admin/reservas', [ReservaApiController::class, 'registrarNuevaReserva']);
Route::patch('/admin/reservas/{id}/cancelar', [ReservaApiController::class, 'cancelarReserva']);
Route::patch('/admin/reservas/{id}/finalizar', [ReservaApiController::class, 'finalizarReserva']);

Route::get('/admin/habitaciones', [AdminHabitacionController::class, 'listarHabitacionesAPI']);
Route::get('/admin/habitaciones/form-data', [AdminHabitacionController::class, 'obtenerTiposHabitacion']);
Route::post('/admin/habitaciones', [AdminHabitacionController::class, 'crearHabitacionAPI']);
Route::put('/admin/habitaciones/{id}', [AdminHabitacionController::class, 'actualizarHabitacionAPI']);
Route::delete('/admin/habitaciones/{id}', [AdminHabitacionController::class, 'eliminarHabitacionAPI']);

Route::get('/admin/clientes', [AdminClienteController::class, 'listarClientesAPI']);
Route::get('/admin/facturacion', [AdminFacturacionController::class, 'listarFacturacionAPI']);
Route::get('/admin/facturacion/reportes', [AdminFacturacionController::class, 'obtenerReportesEstadisticosAPI']);

Route::get('/admin/empleados', [EmpleadoApiController::class, 'listarEmpleados']);
Route::get('/admin/roles', [EmpleadoApiController::class, 'listarRoles']);
Route::post('/admin/empleados', [EmpleadoApiController::class, 'crearEmpleado']);
Route::put('/admin/empleados/{id}', [EmpleadoApiController::class, 'actualizarEmpleado']);
Route::delete('/admin/empleados/{id}', [EmpleadoApiController::class, 'eliminarEmpleado']);

// Rutas de Cliente
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/cliente/dashboard-summary', [App\Http\Controllers\ClienteApiController::class, 'obtenerResumenDashboard']);
    Route::get('/cliente/reservas', [App\Http\Controllers\ClienteApiController::class, 'listarReservas']);
    
    // Rutas para que el cliente haga su propia reserva
    Route::get('/cliente/reservas/disponibilidad', [ReservaApiController::class, 'consultarDisponibilidadHabitaciones']);
    Route::get('/cliente/reservas/calcular-costo', [ReservaApiController::class, 'calcularCostoEstancia']);
    Route::post('/cliente/reservas', [App\Http\Controllers\ClienteApiController::class, 'crearReservaPropia']);
});
