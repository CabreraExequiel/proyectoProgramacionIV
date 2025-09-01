<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\CanchaController;


Route::get('/', function () {
    return view('welcome');
});


Route::get('/clientes-form', function () {
    return view('clientes');
});

Route::resource('reservas', ReservaController::class);
Route::resource('canchas', CanchaController::class);

