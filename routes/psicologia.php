<?php

use App\Http\Controllers\Psicologia\ConsentimientoController;
use App\Http\Controllers\Psicologia\DashboardController;
use App\Http\Controllers\Psicologia\PacaController;
use App\Http\Controllers\Psicologia\PruebaController;
use App\Http\Controllers\Psicologia\QrController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas del Módulo de Gestión Humana / Psicología - C.I. Piscícola New York
|--------------------------------------------------------------------------
| Protegidas por autenticación existente y el middleware de rol 'PSICOLOGIA'.
*/

Route::prefix('psicologia')->name('psicologia.')->middleware(['auth', 'rol:PSICOLOGIA'])->group(function () {

    // Panel de Resumen General
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Custodia y Descarga de Consentimientos Informados (GH-FO-025)
    Route::get('/consentimientos/{consentimiento}', [ConsentimientoController::class, 'show'])->name('consentimientos.show');
    Route::get('/consentimientos/{consentimiento}/docx', [ConsentimientoController::class, 'descargarDocx'])->name('consentimientos.docx');
    Route::get('/consentimientos/{consentimiento}/pdf', [ConsentimientoController::class, 'descargarPdf'])->name('consentimientos.pdf');

    // Historial de Pruebas Psicotécnicas
    Route::get('/pruebas/historial', [PruebaController::class, 'historial'])->name('pruebas.historial');

    // Aplicación y Calificación de la Prueba PAC-A
    Route::get('/paca/{aplicacion}', [PacaController::class, 'aplicar'])->name('paca.aplicar');
    Route::post('/paca/{aplicacion}/borrador', [PacaController::class, 'guardarBorrador'])->name('paca.borrador');
    Route::post('/paca/{aplicacion}/finalizar', [PacaController::class, 'finalizar'])->name('paca.finalizar');
    Route::get('/paca/{aplicacion}/resultado', [PacaController::class, 'resultado'])->name('paca.resultado');

    // Módulo de Auto-registro QR para Evaluados
    Route::get('/qr-evaluados', [QrController::class, 'index'])->name('qr.index');
    Route::get('/qr-evaluados/cartel-imprimir', [QrController::class, 'imprimirCartel'])->name('qr.imprimir');

});
