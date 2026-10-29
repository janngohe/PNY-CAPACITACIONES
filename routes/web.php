<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas Principales de Autenticación - C.I. Piscícola New York
|--------------------------------------------------------------------------
*/

Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout.get');

// Ruta global para cambio obligatorio de contraseña en primer ingreso (usuario_nuevo)
Route::post('/password/primer-ingreso', [LoginController::class, 'actualizarPasswordPrimerIngreso'])
    ->name('password.primer_ingreso')
    ->middleware('auth');

// Módulos del sistema
require __DIR__ . '/empleado.php';
require __DIR__ . '/jefe.php';
require __DIR__ . '/admin.php';
require __DIR__ . '/psicologia.php';

/*
|--------------------------------------------------------------------------
| Módulo de Inducciones Presenciales y Registro de Asistencia QR
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Induccion\FacilitadorController;
use App\Http\Controllers\Induccion\AsistenciaController;

// Acceso al Portal de Facilitador (Cédula + PIN de 4 dígitos)
Route::get('/induccion/acceso', [FacilitadorController::class, 'showLogin'])->name('induccion.acceso');
Route::post('/induccion/acceso', [FacilitadorController::class, 'login'])->name('induccion.acceso.post');
Route::post('/induccion/salir', [FacilitadorController::class, 'logout'])->name('induccion.salir');

// Portal del Facilitador (protegido por middleware facilitador)
Route::prefix('induccion')->name('induccion.')->middleware('facilitador')->group(function () {
    Route::get('/', [FacilitadorController::class, 'index'])->name('index');
    Route::get('/crear', [FacilitadorController::class, 'create'])->name('create');
    Route::post('/', [FacilitadorController::class, 'store'])->name('store');
    Route::post('/{induccion}/iniciar', [FacilitadorController::class, 'iniciarSesion'])->name('iniciar');
    Route::get('/sesion/{codigo}/presentacion', [FacilitadorController::class, 'presentacion'])->name('presentacion');
    Route::get('/sesion/{codigo}/asistencias-stream', [FacilitadorController::class, 'asistenciasStream'])->name('asistencias.stream');
    Route::get('/sesion/{codigo}/detalles', [FacilitadorController::class, 'detallesSesion'])->name('sesion.detalles');
    Route::post('/sesion/{codigo}/finalizar', [FacilitadorController::class, 'finalizarSesion'])->name('finalizar');
    Route::get('/sesion/{codigo}/acta-pdf', [FacilitadorController::class, 'descargarActaPdf'])->name('acta.pdf');
    Route::get('/{induccion}/slides-json', [FacilitadorController::class, 'slidesJson'])->name('slides.json');
    Route::get('/{induccion}/visor-diapositivas', [FacilitadorController::class, 'visorDiapositivas'])->name('visor_diapositivas');
});


// Registro Móvil de Asistencia mediante Código QR o Código Manual
Route::get('/asistencia/{codigo?}', [AsistenciaController::class, 'formulario'])->name('induccion.asistencia.form');
Route::post('/asistencia/buscar-identificacion', [AsistenciaController::class, 'buscarIdentificacion'])->name('induccion.asistencia.buscar');
Route::post('/asistencia/{codigo}/registrar', [AsistenciaController::class, 'registrar'])->name('induccion.asistencia.registrar');

// Verificación y confirmación pública de Canje de Beneficios vía Código QR
Route::get('/canje/verificar/{codigo}', [App\Http\Controllers\Empleado\BeneficioController::class, 'verificarCanjePublico'])->name('canje.verificar');
Route::match(['get', 'post'], '/canje/buscar-responsable', [App\Http\Controllers\Empleado\BeneficioController::class, 'buscarResponsable'])->name('canje.buscar_responsable');
Route::post('/canje/confirmar/{codigo}', [App\Http\Controllers\Empleado\BeneficioController::class, 'confirmarCanjePublico'])->name('canje.confirmar');


