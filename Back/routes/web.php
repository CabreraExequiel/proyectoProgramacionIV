<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\CanchaController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('welcome');
});





Route::get('/clientes-form', function () {
    return view('clientes');
});

Route::resource('reservas', ReservaController::class);
Route::resource('canchas', CanchaController::class);



Route::resource('users', UserController::class)->only([
    'index', 'create', 'store', 'edit', 'update', 'destroy'
]);

