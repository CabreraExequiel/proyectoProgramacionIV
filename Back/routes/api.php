<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;  //Ultimo agregado
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CanchaController2;



Route::apiResource('reservas', ReservaController::class);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']); //Ultimo agregado
Route::apiResource('canchas2',CanchaController2::class);
