<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\CanchaController;

Route::apiResource('categorias', CategoriaController::class);
Route::apiResource('canchas', CanchaController::class);
