<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\CanchaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\AuthController;

Route::apiResource('categorias', CategoriaController::class);
Route::get('/clientes', [ClienteController::class, 'index']);
Route::post('/clientes', [ClienteController::class, 'store']);
Route::put('/clientes/{id}', [ClienteController::class, 'update']);
Route::delete('/clientes/{id}', [ClienteController::class, 'destroy']);
Route::apiResource('canchas', CanchaController::class);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register-provisional', [AuthController::class, 'registerProvisional']);

