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
