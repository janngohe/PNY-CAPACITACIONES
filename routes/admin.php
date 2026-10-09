<?php

use App\Http\Controllers\Admin\AreaController;
use App\Http\Controllers\Admin\AsignacionController;
use App\Http\Controllers\Admin\CapacitacionController;
use App\Http\Controllers\Admin\CertificadoController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EvaluacionController;
use App\Http\Controllers\Admin\ProgresoController;
use App\Http\Controllers\Admin\ReporteController;
use App\Http\Controllers\Admin\ResultadoController;
use App\Http\Controllers\Admin\UsuarioController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas del Panel del Administrador - C.I. Piscícola New York
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->middleware(['auth', 'rol:ADMINISTRADOR'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Usuarios (se registran, consultan, editan y desactivan; nunca se eliminan)
    Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
    Route::get('/usuarios/crear', [UsuarioController::class, 'create'])->name('usuarios.create');
    Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
    Route::get('/usuarios/{usuario}/editar', [UsuarioController::class, 'edit'])->name('usuarios.edit');
    Route::put('/usuarios/{usuario}', [UsuarioController::class, 'update'])->name('usuarios.update');
    Route::patch('/usuarios/{usuario}/estado', [UsuarioController::class, 'toggleEstado'])->name('usuarios.estado');
    Route::post('/usuarios/{usuario}/restablecer', [UsuarioController::class, 'restablecerPassword'])->name('usuarios.restablecer');

    // Áreas
    Route::get('/areas', [AreaController::class, 'index'])->name('areas.index');
    Route::post('/areas', [AreaController::class, 'store'])->name('areas.store');
    Route::put('/areas/{area}', [AreaController::class, 'update'])->name('areas.update');
    Route::patch('/areas/{area}/estado', [AreaController::class, 'toggleEstado'])->name('areas.estado');

    // Capacitaciones (con módulos y contenidos)
    Route::get('/capacitaciones', [CapacitacionController::class, 'index'])->name('capacitaciones.index');
    Route::get('/capacitaciones/crear', [CapacitacionController::class, 'create'])->name('capacitaciones.create');
    Route::post('/capacitaciones', [CapacitacionController::class, 'store'])->name('capacitaciones.store');
    Route::get('/capacitaciones/{capacitacion}/editar', [CapacitacionController::class, 'edit'])->name('capacitaciones.edit');
    Route::put('/capacitaciones/{capacitacion}', [CapacitacionController::class, 'update'])->name('capacitaciones.update');
    Route::patch('/capacitaciones/{capacitacion}/estado', [CapacitacionController::class, 'toggleEstado'])->name('capacitaciones.estado');

    // Asignación de capacitaciones a áreas
    Route::get('/asignaciones', [AsignacionController::class, 'index'])->name('asignaciones.index');
    Route::put('/asignaciones/{capacitacion}', [AsignacionController::class, 'update'])->name('asignaciones.update');

    // Evaluaciones, preguntas y respuestas
    Route::get('/evaluaciones', [EvaluacionController::class, 'index'])->name('evaluaciones.index');
    Route::get('/evaluaciones/crear', [EvaluacionController::class, 'create'])->name('evaluaciones.create');
    Route::post('/evaluaciones', [EvaluacionController::class, 'store'])->name('evaluaciones.store');
    Route::get('/evaluaciones/{evaluacion}/editar', [EvaluacionController::class, 'edit'])->name('evaluaciones.edit');
    Route::put('/evaluaciones/{evaluacion}', [EvaluacionController::class, 'update'])->name('evaluaciones.update');
    Route::patch('/evaluaciones/{evaluacion}/estado', [EvaluacionController::class, 'toggleEstado'])->name('evaluaciones.estado');

    // Seguimiento
    Route::get('/progreso', [ProgresoController::class, 'index'])->name('progreso.index');
    Route::get('/resultados', [ResultadoController::class, 'index'])->name('resultados.index');
    Route::get('/resultados/{evaluacion}', [ResultadoController::class, 'show'])->name('resultados.show');

    // Certificados
    Route::get('/certificados', [CertificadoController::class, 'index'])->name('certificados.index');
    Route::post('/certificados/generar', [CertificadoController::class, 'generar'])->name('certificados.generar');

    // Reportes
    Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
    Route::get('/reportes/{tipo}/{formato}', [ReporteController::class, 'descargar'])
        ->whereIn('tipo', ['progreso', 'resultados', 'certificados', 'usuarios'])
        ->whereIn('formato', ['csv', 'pdf'])
        ->name('reportes.descargar');
});
