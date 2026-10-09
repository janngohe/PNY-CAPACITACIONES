<?php

use App\Http\Controllers\Jefe\CapacitacionController;
use App\Http\Controllers\Jefe\EvaluacionController;
use App\Http\Controllers\Jefe\ResultadoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas del Panel del Jefe de Área - C.I. Piscícola New York
|--------------------------------------------------------------------------
*/

Route::prefix('jefe')->name('jefe.')->middleware(['auth', 'rol:JEFE_AREA'])->group(function () {
    // Vista por defecto: capacitaciones publicadas por el jefe
    Route::get('/', [CapacitacionController::class, 'index'])->name('dashboard');

    // CRUD de capacitaciones (no se eliminan, solo se activan / desactivan)
    Route::get('/capacitaciones', [CapacitacionController::class, 'index'])->name('capacitaciones.index');
    Route::get('/capacitaciones/crear', [CapacitacionController::class, 'create'])->name('capacitaciones.create');
    Route::post('/capacitaciones', [CapacitacionController::class, 'store'])->name('capacitaciones.store');
    Route::get('/capacitaciones/{capacitacion}', [CapacitacionController::class, 'show'])->name('capacitaciones.show');
    Route::get('/capacitaciones/{capacitacion}/editar', [CapacitacionController::class, 'edit'])->name('capacitaciones.edit');
    Route::put('/capacitaciones/{capacitacion}', [CapacitacionController::class, 'update'])->name('capacitaciones.update');
    Route::patch('/capacitaciones/{capacitacion}/estado', [CapacitacionController::class, 'toggleEstado'])->name('capacitaciones.estado');

    // Evaluaciones
    Route::get('/evaluaciones', [EvaluacionController::class, 'index'])->name('evaluaciones.index');
    Route::get('/evaluaciones/crear', [EvaluacionController::class, 'create'])->name('evaluaciones.create');
    Route::post('/evaluaciones', [EvaluacionController::class, 'store'])->name('evaluaciones.store');
    Route::get('/evaluaciones/{evaluacion}/editar', [EvaluacionController::class, 'edit'])->name('evaluaciones.edit');
    Route::put('/evaluaciones/{evaluacion}', [EvaluacionController::class, 'update'])->name('evaluaciones.update');
    Route::patch('/evaluaciones/{evaluacion}/estado', [EvaluacionController::class, 'toggleEstado'])->name('evaluaciones.estado');

    // Consultar resultados (submenú de evaluaciones)
    Route::get('/resultados', [ResultadoController::class, 'index'])->name('resultados.index');
    Route::get('/resultados/{evaluacion}', [ResultadoController::class, 'show'])->name('resultados.show');
});
