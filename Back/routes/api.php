<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\CanchaController;
use App\Http\Controllers\AuthController;  //Ultimo agregado
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;

Route::apiResource('canchas', CanchaController::class);
Route::apiResource('reservas', ReservaController::class);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']); //Ultimo agregado

Route::get('/dashboard-metrics', [DashboardController::class, 'metrics']);

Route::get('/canchas', [ReservaController::class, 'getCanchas']);
Route::get('/horarios', [ReservaController::class, 'getHorarios']);
Route::get('/reservas', [ReservaController::class, 'index']);
Route::post('/reservas', [ReservaController::class, 'store']);
// Route::get('/reservas-activas', [ReservaController::class, 'index']);

