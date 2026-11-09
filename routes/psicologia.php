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

Route::post('/evaluacion-psicologica/{codigo?}/retomar', [DashboardController::class, 'retomarEvaluacionPorCedula'])
    ->name('psicologia.evaluacion.retomar');

Route::post('/evaluacion-psicologica/{codigo?}/consentimiento', [DashboardController::class, 'guardarConsentimiento'])
    ->name('psicologia.evaluacion.guardar_consentimiento');

// Paso 2: Primera Prueba (VALANTI)
Route::get('/evaluacion-psicologica/{codigo}/paso-2/{evaluacion?}', [DashboardController::class, 'evaluacionPaso2'])
    ->name('psicologia.evaluacion.paso2');

Route::post('/evaluacion-psicologica/{codigo}/paso-2/{evaluacion}/borrador', [DashboardController::class, 'guardarBorradorValanti'])
    ->name('psicologia.evaluacion.valanti.borrador');

Route::post('/evaluacion-psicologica/{codigo}/paso-2/{evaluacion}/finalizar', [DashboardController::class, 'finalizarValanti'])
    ->name('psicologia.evaluacion.valanti.finalizar');

// Paso 3: Segunda Prueba (PAC-A)
Route::get('/evaluacion-psicologica/{codigo}/paso-3/{evaluacion?}', [DashboardController::class, 'evaluacionPaso3'])
    ->name('psicologia.evaluacion.paso3');

Route::post('/evaluacion-psicologica/{codigo}/paso-3/{evaluacion}/borrador', [DashboardController::class, 'guardarBorradorPaca'])
    ->name('psicologia.evaluacion.paca.borrador');

Route::post('/evaluacion-psicologica/{codigo}/paso-3/{evaluacion}/finalizar', [DashboardController::class, 'finalizarPaca'])
    ->name('psicologia.evaluacion.paca.finalizar');

// Pantalla Final de Evaluación Completada
Route::get('/evaluacion-psicologica/{codigo}/completada/{evaluacion?}', [DashboardController::class, 'evaluacionCompletada'])
    ->name('psicologia.evaluacion.completada');

// Rutas protegidas para el profesional de Psicología y Administradores del Sistema
Route::prefix('psicologia')->name('psicologia.')->middleware(['auth', 'rol:PSICOLOGIA,ADMINISTRADOR'])->group(function () {
    // Selección de portal
    Route::get('/seleccionar-portal', [DashboardController::class, 'portalSeleccion'])->name('portal_seleccion');

    // Panel Principal
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // 1. Realizar Prueba (Código QR institucional único)
    Route::get('/realizar-prueba', [DashboardController::class, 'realizarPrueba'])->name('pruebas.realizar');

    // 2. Reportes y Descarga de Excel Anual
    Route::get('/reportes', [DashboardController::class, 'reportes'])->name('reportes');
    Route::get('/reportes/excel', [DashboardController::class, 'descargarReporteExcel'])->name('reportes.excel');

    // 3. Historial de Pruebas y Resultados (PAC-A + VALANTI) + Custodia GH-FO-025 + PDF Gráfico VALANTI
    Route::get('/historial-pruebas', [DashboardController::class, 'historialPruebas'])->name('pruebas.historial');
    Route::get('/pruebas/{evaluacion}/resultado', [DashboardController::class, 'verResultadoPaca'])->name('paca.resultado');
    Route::get('/pruebas/{evaluacion}/paca-pdf', [DashboardController::class, 'verPacaPdf'])->name('paca.pdf');
    Route::post('/pruebas/{evaluacion}/paca-respuestas', [DashboardController::class, 'actualizarRespuestasPaca'])->name('paca.actualizar_respuestas');
    Route::get('/pruebas/{evaluacion}/resultado-valanti', [DashboardController::class, 'verResultadoValanti'])->name('valanti.resultado');
    Route::post('/pruebas/{evaluacion}/valanti-respuestas', [DashboardController::class, 'actualizarRespuestasValanti'])->name('valanti.actualizar_respuestas');
    Route::get('/pruebas/{evaluacion}/valanti-pdf', [DashboardController::class, 'verValantiPdf'])->name('valanti.pdf');
    Route::get('/pruebas/{evaluacion}/consentimiento-pdf', [DashboardController::class, 'verConsentimientoPdf'])->name('consentimientos.pdf');

    // 4. Certificado Aptitudinal (GH-FO-028)
    Route::get('/certificado-aptitudinal', [DashboardController::class, 'certificadoAptitudinal'])->name('certificado_aptitudinal');
    Route::post('/certificado-aptitudinal/{evaluacion}', [DashboardController::class, 'guardarCertificadoAptitudinal'])->name('certificado_aptitudinal.guardar');
    Route::get('/certificado-aptitudinal/{evaluacion}/pdf', [DashboardController::class, 'verCertificadoAptitudinalPdf'])->name('certificado_aptitudinal.pdf');

    // 5. Configuración de Firma Digital Oficial de Psicología
    Route::post('/guardar-firma', [DashboardController::class, 'guardarFirmaPsicologia'])->name('firma.guardar');
    Route::post('/eliminar-firma', [DashboardController::class, 'eliminarFirmaPsicologia'])->name('firma.eliminar');
});
