<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\CanchaController;
use App\Http\Controllers\AuthController;  //Ultimo agregado


Route::apiResource('canchas', CanchaController::class);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']); //Ultimo agregado
