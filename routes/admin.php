<?php

use App\Http\Controllers\Admin\AreaController;
use App\Http\Controllers\Admin\AsignacionController;
use App\Http\Controllers\Admin\BeneficioController;
use App\Http\Controllers\Admin\CapacitacionController;
use App\Http\Controllers\Admin\CertificadoController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EvaluacionController;
use App\Http\Controllers\Admin\ProgresoController;
use App\Http\Controllers\Admin\ReporteController;
use App\Http\Controllers\Admin\ResultadoController;
use App\Http\Controllers\Admin\SedeController;
use App\Http\Controllers\Admin\UsuarioController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas del Panel del Administrador - C.I. Piscícola New York
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->middleware(['auth', 'rol:ADMINISTRADOR'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Sedes
    Route::get('/sedes', [SedeController::class, 'index'])->name('sedes.index');
    Route::post('/sedes', [SedeController::class, 'store'])->name('sedes.store');
    Route::put('/sedes/{sede}', [SedeController::class, 'update'])->name('sedes.update');
    Route::patch('/sedes/{sede}/estado', [SedeController::class, 'toggleEstado'])->name('sedes.estado');

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
    Route::get('/capacitaciones/borradores', [CapacitacionController::class, 'borradores'])->name('capacitaciones.borradores');
    Route::get('/capacitaciones/crear', [CapacitacionController::class, 'create'])->name('capacitaciones.create');
    Route::post('/capacitaciones', [CapacitacionController::class, 'store'])->name('capacitaciones.store');
    Route::get('/capacitaciones/{capacitacion}/editar', [CapacitacionController::class, 'edit'])->name('capacitaciones.edit');
    Route::put('/capacitaciones/{capacitacion}', [CapacitacionController::class, 'update'])->name('capacitaciones.update');
    Route::patch('/capacitaciones/{capacitacion}/estado', [CapacitacionController::class, 'toggleEstado'])->name('capacitaciones.estado');
    Route::patch('/capacitaciones/{capacitacion}/publicar', [CapacitacionController::class, 'publicar'])->name('capacitaciones.publicar');
    Route::delete('/capacitaciones/{capacitacion}', [CapacitacionController::class, 'destroy'])->name('capacitaciones.destroy');

    // Asignación de capacitaciones a áreas
    Route::get('/asignaciones', [AsignacionController::class, 'index'])->name('asignaciones.index');
    Route::put('/asignaciones/{capacitacion}', [AsignacionController::class, 'update'])->name('asignaciones.update');

    // Catálogo de Beneficios y Puntos
    Route::get('/beneficios', [BeneficioController::class, 'index'])->name('beneficios.index');
    Route::post('/beneficios', [BeneficioController::class, 'store'])->name('beneficios.store');
    Route::put('/beneficios/{beneficio}', [BeneficioController::class, 'update'])->name('beneficios.update');
    Route::match(['post', 'patch'], '/beneficios/{beneficio}/toggle-estado', [BeneficioController::class, 'toggleEstado'])->name('beneficios.toggle-estado');
    Route::match(['post', 'patch'], '/beneficios/{beneficio}/estado', [BeneficioController::class, 'toggleEstado'])->name('beneficios.estado');
    Route::delete('/beneficios/{beneficio}', [BeneficioController::class, 'destroy'])->name('beneficios.destroy');

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
    Route::get('/certificados/{certificado}', [CertificadoController::class, 'show'])->name('certificados.show');
    Route::get('/certificados/{certificado}/pdf', [CertificadoController::class, 'descargarPdf'])->name('certificados.pdf');
    Route::post('/certificados/generar', [CertificadoController::class, 'generar'])->name('certificados.generar');
    Route::patch('/certificados/externos/{certificadoExterno}/aprobar', [CertificadoController::class, 'aprobarExterno'])->name('certificados.externos.aprobar');
    Route::patch('/certificados/externos/{certificadoExterno}/rechazar', [CertificadoController::class, 'rechazarExterno'])->name('certificados.externos.rechazar');

    // Plantillas de Certificados
    Route::post('/certificados/plantillas', [CertificadoController::class, 'guardarPlantilla'])->name('certificados.plantillas.store');
    Route::put('/certificados/plantillas/{plantilla}', [CertificadoController::class, 'actualizarPlantilla'])->name('certificados.plantillas.update');
    Route::patch('/certificados/plantillas/{plantilla}/estado', [CertificadoController::class, 'togglePlantillaEstado'])->name('certificados.plantillas.estado');
    Route::delete('/certificados/plantillas/{plantilla}', [CertificadoController::class, 'eliminarPlantilla'])->name('certificados.plantillas.destroy');

    // Reportes
    Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
    Route::get('/reportes/{tipo}/{formato}', [ReporteController::class, 'descargar'])
        ->whereIn('tipo', ['progreso', 'resultados', 'certificados', 'usuarios'])
        ->whereIn('formato', ['csv', 'pdf'])
        ->name('reportes.descargar');

    // Inducciones (CRUD completo y PIN de Facilitador)
    Route::get('/inducciones', [\App\Http\Controllers\Admin\InduccionAdminController::class, 'index'])->name('inducciones.index');
    Route::get('/inducciones/sesiones', [\App\Http\Controllers\Admin\InduccionAdminController::class, 'sesiones'])->name('inducciones.sesiones');
    Route::get('/inducciones/crear', [\App\Http\Controllers\Admin\InduccionAdminController::class, 'create'])->name('inducciones.create');
    Route::post('/inducciones', [\App\Http\Controllers\Admin\InduccionAdminController::class, 'store'])->name('inducciones.store');
    Route::get('/inducciones/{induccion}/editar', [\App\Http\Controllers\Admin\InduccionAdminController::class, 'edit'])->name('inducciones.edit');
    Route::put('/inducciones/{induccion}', [\App\Http\Controllers\Admin\InduccionAdminController::class, 'update'])->name('inducciones.update');
    Route::delete('/inducciones/{induccion}', [\App\Http\Controllers\Admin\InduccionAdminController::class, 'destroy'])->name('inducciones.destroy');
    Route::patch('/inducciones/{induccion}/toggle-estado', [\App\Http\Controllers\Admin\InduccionAdminController::class, 'toggleEstado'])->name('inducciones.toggle_estado');
    Route::post('/inducciones/regenerar-pin', [\App\Http\Controllers\Admin\InduccionAdminController::class, 'regenerarPin'])->name('inducciones.regenerar_pin');
    Route::get('/inducciones/sesiones/{sesion}/acta-pdf', [\App\Http\Controllers\Admin\InduccionAdminController::class, 'descargarActaPdf'])->name('inducciones.acta_pdf');
});
