<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SesionController;
use App\Http\Controllers\DatosSimplesController;
use App\Http\Controllers\ClaseAgrupadaController;
use App\Http\Controllers\ResultadoController;

// Vista inicial
Route::get('/', function () {
    return view('vistas.carga');
});

// Dashboard principal
Route::get('/dashboard', [SesionController::class, 'index'])->name('dashboard');

// Sesiones estadísticas
Route::post('/sesion', [SesionController::class, 'store'])->name('sesion.store');
Route::get('/sesiones', [SesionController::class, 'index'])->name('sesion.index');
Route::get('/sesion/{id}', [SesionController::class, 'show'])->name('sesion.show');

// Datos simples (serie simple)
Route::post('/datos-simples', [DatosSimplesController::class, 'store'])->name('datos.simples.store');
Route::post('/datos-simples/clonar/{sesion_id}', [DatosSimplesController::class, 'clonar'])->name('datos.simples.clonar');

// Clases agrupadas (serie agrupada)
Route::post('/clases-agrupadas', [ClaseAgrupadaController::class, 'store'])->name('clases.agrupadas.store');
Route::post('/clases-agrupadas/clonar/{sesion_id}', [ClaseAgrupadaController::class, 'clonar'])->name('clases.agrupadas.clonar');

// Resultados estadísticos
Route::post('/resultados/calcular/{sesion_id}', [ResultadoController::class, 'calcular'])->name('resultados.calcular');

//pdf
Route::get('/sesion/{id}/reporte', [SesionController::class, 'reportePDF'])->name('sesion.reporte');


//cerrar sesion
Route::post('/sesion/cerrar', [SesionController::class, 'cerrar'])->name('sesion.cerrar');

