<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::post('/login', [AuthController::class, 'login']);

Route::post('/password/recuperar',[AuthController::class,'olvideMicontrasena']);
Route::post('/password/restablecer',[AuthController::class,'cambiarContrasena']);