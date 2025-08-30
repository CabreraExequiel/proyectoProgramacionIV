<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\UserController;

Route::resource('users', UserController::class)->only([
    'index', 'create', 'store', 'edit', 'update', 'destroy'
]);
