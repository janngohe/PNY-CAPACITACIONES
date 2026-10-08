<?php

use App\Http\Controllers\Empleado\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas del Panel del Empleado - C.I. Piscícola New York
|--------------------------------------------------------------------------
*/

Route::prefix('empleado')->name('empleado.')->middleware('auth')->group(function () {
    // Vista por defecto al iniciar sesión: Capacitaciones e Inducciones
    Route::get('/', [DashboardController::class, 'capacitaciones'])->name('dashboard');
    Route::get('/capacitaciones', [DashboardController::class, 'capacitaciones'])->name('capacitaciones');

    // Certificados generados de la plataforma
    Route::get('/certificados', [DashboardController::class, 'certificados'])->name('certificados');

    // Historial de capacitaciones aprobadas/completadas
    Route::get('/finalizadas', [DashboardController::class, 'finalizadas'])->name('finalizadas');

    // Anexo de certificados externos (SENA, ICA, etc.)
    Route::get('/anexo-certificados', [DashboardController::class, 'anexoCertificados'])->name('anexo');
    Route::post('/anexo-certificados', [DashboardController::class, 'guardarAnexo'])->name('anexo.guardar');
});
