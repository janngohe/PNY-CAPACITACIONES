<?php

use App\Http\Controllers\Psicologia\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas del Panel de Psicología - C.I. Piscícola New York
|--------------------------------------------------------------------------
*/

Route::prefix('psicologia')->name('psicologia.')->middleware(['auth', 'rol:PSICOLOGIA'])->group(function () {
    // Selección de portal
    Route::get('/seleccionar-portal', [DashboardController::class, 'portalSeleccion'])->name('portal_seleccion');

    // Panel Principal
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // 1. Realizar Prueba
    Route::get('/realizar-prueba', [DashboardController::class, 'realizarPrueba'])->name('pruebas.realizar');

    // 2. Reportes
    Route::get('/reportes', [DashboardController::class, 'reportes'])->name('reportes');

    // 3. Historial de Pruebas
    Route::get('/historial-pruebas', [DashboardController::class, 'historialPruebas'])->name('pruebas.historial');

    // 4. Certificado Aptitudinal
    Route::get('/certificado-aptitudinal', [DashboardController::class, 'certificadoAptitudinal'])->name('certificado_aptitudinal');
});
