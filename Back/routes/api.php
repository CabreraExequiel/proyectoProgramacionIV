<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;  //Ultimo agregado
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CanchaController2;


// 1. Rutas Públicas (No requieren token)
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/canchas2', [ReservaController::class, 'getCanchas']); // Si es solo para listar
Route::apiResource('canchas2', CanchaController2::class)->only(['index', 'show']);
Route::get('/horarios', [ReservaController::class, 'getHorarios']);
Route::get('/disponibilidad-mes', [ReservaController::class, 'getDisponibilidadMes']);
Route::get('/disponibilidad', [ReservaController::class, 'getDisponibilidad']);

// 2. Grupo de Rutas Protegidas (Requieren un token válido)
Route::middleware('auth:api')->group(function () {
    
    // Rutas de Usuario Específico
    Route::get('/reservas', [ReservaController::class, 'index']); 
    Route::get('/reservations', [ReservaController::class, 'getReservasActivasPorUsuario']); 
    Route::post('/reservas', [ReservaController::class, 'store']); 
      
    // Rutas de Administración /
    Route::apiResource('users', UserController::class)->except(['store']); 
    Route::apiResource('canchas2', CanchaController2::class)->except(['index', 'show']); 
    
    // Rutas de Reportes y Gestión de Reservas
    Route::get('/reservas/activas', [ReservaController::class, 'getReservasActivas']);
    Route::get('/reservas/pendientes', [ReservaController::class, 'getReservasPendientes']);
    Route::get('/reservas/metrics', [ReservaController::class, 'getMetrics']);
    Route::get('/reservas/ingresos', [ReservaController::class, 'getIngresosMensuales']);
    Route::put('/reservas/{id}/estado', [ReservaController::class, 'actualizarEstado']);
    Route::put('/reservas/{reserva}', [ReservaController::class, 'update']);
    Route::delete('/reservas/{reserva}', [ReservaController::class, 'destroy']);
    Route::get('/usuarios-registrados', [UserController::class, 'getUsuariosRegistrados']);

    
});


