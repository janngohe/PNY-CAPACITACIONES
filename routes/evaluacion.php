<?php

use App\Http\Controllers\EvaluacionPublicaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Portal Público de Auto-registro y Evaluación Psicotécnica (Acceso QR)
| C.I. Piscícola New York S.A.
|--------------------------------------------------------------------------
*/

Route::prefix('evaluacion')->name('evaluacion.')->group(function () {
    // 1. Identificación y registro personal del aspirante
    Route::get('/', [EvaluacionPublicaController::class, 'inicio'])->name('inicio');
    Route::post('/registro', [EvaluacionPublicaController::class, 'registrar'])->name('registrar');

    // 2. Lectura y firma digital del Consentimiento Informado GH-FO-025
    Route::get('/consentimiento', [EvaluacionPublicaController::class, 'verConsentimiento'])->name('consentimiento');
    Route::post('/consentimiento/firmar', [EvaluacionPublicaController::class, 'firmarConsentimiento'])->name('consentimiento.firmar');
    Route::get('/consentimiento/{token}/pdf', [EvaluacionPublicaController::class, 'descargarConsentimientoPdf'])->name('consentimiento.pdf');

    // 3. Selección de pruebas psicotécnicas autorizadas
    Route::get('/pruebas', [EvaluacionPublicaController::class, 'seleccionarPrueba'])->name('pruebas');
    Route::post('/paca/iniciar', [EvaluacionPublicaController::class, 'iniciarPaca'])->name('paca.iniciar');

    // 4. Aplicación de la prueba PAC-A (40 afirmaciones)
    Route::get('/paca/{token}/{appToken}', [EvaluacionPublicaController::class, 'aplicarPaca'])->name('paca.aplicar');
    Route::post('/paca/{token}/{appToken}/borrador', [EvaluacionPublicaController::class, 'guardarBorradorPaca'])->name('paca.borrador');
    Route::post('/paca/{token}/{appToken}/finalizar', [EvaluacionPublicaController::class, 'finalizarPaca'])->name('paca.finalizar');

    // 5. Pantalla final de confirmación
    Route::get('/paca/{token}/{appToken}/completada', [EvaluacionPublicaController::class, 'pacaCompletada'])->name('paca.completada');
});
