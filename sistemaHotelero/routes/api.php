<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::post('/login', [AuthController::class, 'login']);

Route::post('/password/recuperar',[AuthController::class,'olvideMicontrasena']);
Route::post('/password/restablecer',[AuthController::class,'cambiarContrasena']);
Route::post('/logout', [AuthController::class, 'logout']);


Route::get('/admin/reservas', [App\Http\Controllers\ReservaApiController::class, 'index']);
Route::get('/admin/reservas/form-data', [App\Http\Controllers\ReservaApiController::class, 'formData']);
Route::get('/admin/reservas/calcular-costo', [App\Http\Controllers\ReservaApiController::class, 'calcularCosto']);
Route::get('/admin/reservas/disponibilidad', [App\Http\Controllers\ReservaApiController::class, 'apiDisponibilidad']);
Route::post('/admin/reservas', [App\Http\Controllers\ReservaApiController::class, 'store']);
Route::patch('/admin/reservas/{id}/cancelar', [App\Http\Controllers\ReservaApiController::class, 'cancelar']);
Route::patch('/admin/reservas/{id}/finalizar', [App\Http\Controllers\ReservaApiController::class, 'finalizar']);

Route::get('/admin/habitaciones', [App\Http\Controllers\AdminHabitacionController::class, 'apiIndex']);
Route::get('/admin/habitaciones/form-data', [App\Http\Controllers\AdminHabitacionController::class, 'formData']);
Route::post('/admin/habitaciones', [App\Http\Controllers\AdminHabitacionController::class, 'apiStore']);
Route::put('/admin/habitaciones/{id}', [App\Http\Controllers\AdminHabitacionController::class, 'apiUpdate']);
Route::delete('/admin/habitaciones/{id}', [App\Http\Controllers\AdminHabitacionController::class, 'apiDestroy']);

Route::get('/admin/clientes', [App\Http\Controllers\AdminClienteController::class, 'apiIndex']);
Route::get('/admin/facturacion', [App\Http\Controllers\AdminFacturacionController::class, 'apiIndex']);
Route::get('/admin/facturacion/reportes', [App\Http\Controllers\AdminFacturacionController::class, 'apiReportes']);

Route::get('/admin/empleados', [App\Http\Controllers\EmpleadoApiController::class, 'index']);
Route::get('/admin/roles', [App\Http\Controllers\EmpleadoApiController::class, 'roles']);
Route::post('/admin/empleados', [App\Http\Controllers\EmpleadoApiController::class, 'store']);
Route::put('/admin/empleados/{id}', [App\Http\Controllers\EmpleadoApiController::class, 'update']);
Route::delete('/admin/empleados/{id}', [App\Http\Controllers\EmpleadoApiController::class, 'destroy']);
