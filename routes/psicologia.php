<?php

use App\Http\Controllers\Psicologia\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas del Panel de Psicología - C.I. Piscícola New York
|--------------------------------------------------------------------------
*/

// Rutas públicas de evaluación para la persona que escanea el QR desde su celular (sin auth)
Route::get('/evaluacion-psicologica/{codigo?}', [DashboardController::class, 'evaluacionPublica'])
    ->name('psicologia.evaluacion.publica');

Route::post('/evaluacion-psicologica/{codigo?}/consentimiento', [DashboardController::class, 'guardarConsentimiento'])
    ->name('psicologia.evaluacion.guardar_consentimiento');

Route::get('/evaluacion-psicologica/{codigo?}/paso-2', [DashboardController::class, 'evaluacionPaso2'])
    ->name('psicologia.evaluacion.paso2');

// Rutas protegidas para el profesional de Psicología
Route::prefix('psicologia')->name('psicologia.')->middleware(['auth', 'rol:PSICOLOGIA'])->group(function () {
    // Selección de portal
    Route::get('/seleccionar-portal', [DashboardController::class, 'portalSeleccion'])->name('portal_seleccion');

    // Panel Principal
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // 1. Realizar Prueba (Generación de QR y sesión)
    Route::get('/realizar-prueba', [DashboardController::class, 'realizarPrueba'])->name('pruebas.realizar');
    Route::post('/realizar-prueba/nuevo-codigo', [DashboardController::class, 'generarNuevoCodigo'])->name('pruebas.nuevo_codigo');

    // 2. Reportes
    Route::get('/reportes', [DashboardController::class, 'reportes'])->name('reportes');

    // 3. Historial de Pruebas
    Route::get('/historial-pruebas', [DashboardController::class, 'historialPruebas'])->name('pruebas.historial');

    // 4. Certificado Aptitudinal
    Route::get('/certificado-aptitudinal', [DashboardController::class, 'certificadoAptitudinal'])->name('certificado_aptitudinal');
});
