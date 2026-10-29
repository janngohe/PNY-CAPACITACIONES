<?php

use App\Http\Controllers\Jefe\CapacitacionController;
use App\Http\Controllers\Jefe\EstadisticaController;
use App\Http\Controllers\Jefe\EvaluacionController;
use App\Http\Controllers\Jefe\PersonalController;
use App\Http\Controllers\Jefe\PlantillaController;
use App\Http\Controllers\Jefe\ResultadoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas del Panel del Jefe de Área - C.I. Piscícola New York
|--------------------------------------------------------------------------
*/

Route::prefix('jefe')->name('jefe.')->middleware(['auth', 'rol:JEFE_AREA'])->group(function () {
    // Vista principal: Panel de Estadísticas y Analítica del Área
    Route::get('/', [EstadisticaController::class, 'index'])->name('dashboard');
    Route::get('/estadisticas', [EstadisticaController::class, 'index'])->name('estadisticas.index');

    // CRUD de capacitaciones
    Route::get('/capacitaciones', [CapacitacionController::class, 'index'])->name('capacitaciones.index');
    Route::get('/capacitaciones/borradores', [CapacitacionController::class, 'borradores'])->name('capacitaciones.borradores');
    Route::get('/capacitaciones/crear', [CapacitacionController::class, 'create'])->name('capacitaciones.create');
    Route::post('/capacitaciones', [CapacitacionController::class, 'store'])->name('capacitaciones.store');
    Route::get('/capacitaciones/{capacitacion}', [CapacitacionController::class, 'show'])->name('capacitaciones.show');
    Route::get('/capacitaciones/{capacitacion}/editar', [CapacitacionController::class, 'edit'])->name('capacitaciones.edit');
    Route::put('/capacitaciones/{capacitacion}', [CapacitacionController::class, 'update'])->name('capacitaciones.update');
    Route::patch('/capacitaciones/{capacitacion}/estado', [CapacitacionController::class, 'toggleEstado'])->name('capacitaciones.estado');
    Route::patch('/capacitaciones/{capacitacion}/publicar', [CapacitacionController::class, 'publicar'])->name('capacitaciones.publicar');
    Route::delete('/capacitaciones/{capacitacion}', [CapacitacionController::class, 'destroy'])->name('capacitaciones.destroy');

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

    // Mis Plantillas de Certificados
    Route::get('/plantillas', [PlantillaController::class, 'index'])->name('plantillas.index');
    Route::post('/plantillas', [PlantillaController::class, 'store'])->name('plantillas.store');
    Route::put('/plantillas/{plantilla}', [PlantillaController::class, 'update'])->name('plantillas.update');
    Route::patch('/plantillas/{plantilla}/firma', [PlantillaController::class, 'actualizarFirma'])->name('plantillas.firma');
    Route::patch('/plantillas/{plantilla}/estado', [PlantillaController::class, 'toggleEstado'])->name('plantillas.estado');
    Route::delete('/plantillas/{plantilla}', [PlantillaController::class, 'destroy'])->name('plantillas.destroy');

    // Guardar firma digital del perfil del Jefe de Área (primer ingreso / configuración)
    Route::post('/firma-perfil', [PlantillaController::class, 'guardarFirmaPerfil'])->name('firma.perfil');

    // Personal de Área
    Route::get('/personal', [PersonalController::class, 'index'])->name('personal.index');

    // Inducciones y Consulta de PIN
    Route::get('/inducciones', [\App\Http\Controllers\Jefe\InduccionJefeController::class, 'index'])->name('inducciones.index');
    Route::get('/inducciones/pin', [\App\Http\Controllers\Jefe\InduccionJefeController::class, 'consultarPin'])->name('inducciones.pin');
    Route::get('/inducciones/sesiones/{sesion}/participantes-json', [\App\Http\Controllers\Jefe\InduccionJefeController::class, 'participantesJson'])->name('inducciones.participantes_json');
});
