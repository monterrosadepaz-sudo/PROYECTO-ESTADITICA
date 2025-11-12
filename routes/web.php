<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('vistas.carga');
});


Route::get('/dashboard', function () {
    return view('vistas.dashboard'); // o 'dashboard' si está en la raíz
})->name('dashboard');
