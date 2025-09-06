<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;  //Ultimo agregado
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CanchaController2;




Route::get('/horarios', [ReservaController::class, 'getHorarios']);
Route::get('/reservas', [ReservaController::class, 'index']);
Route::get('/reservations', [ReservaController::class, 'index']);
Route::get('/reservas/metrics', [ReservaController::class, 'getMetrics']);
Route::get('/reservas/activas', [ReservaController::class, 'getReservasActivas']);
Route::middleware('auth:api')->group(function () {
    Route::post('/reservas', [ReservaController::class, 'store']);
});
Route::get('/usuarios-registrados', [UserController::class, 'getUsuariosRegistrados']);
Route::get('/reservas-pendientes', [ReservaController::class, 'getReservasPendientes']);
Route::put('/reservas/{id}/estado', [ReservaController::class, 'actualizarEstado']);
Route::get('/reservas/ingresos', [ReservaController::class, 'getIngresosMensuales']);
Route::apiResource('reservas', ReservaController::class);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']); //Ultimo agregado
Route::apiResource('canchas2',CanchaController2::class);
