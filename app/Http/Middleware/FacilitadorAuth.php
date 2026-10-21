<?php

namespace App\Http\Middleware;

use App\Models\Usuario;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FacilitadorAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Si ya hay usuario autenticado en Laravel con rol ADMIN o JEFE, se le permite acceso directo
        if (auth()->check()) {
            $user = auth()->user();
            if (in_array($user->rol, ['ADMINISTRADOR', 'JEFE_AREA']) && $user->estado) {
                if (!session()->has('facilitador_usuario_id')) {
                    session(['facilitador_usuario_id' => $user->id]);
                }
                return $next($request);
            }
        }

        // 2. Si hay sesión de facilitador activa por Cédula + PIN
        $facilitadorId = session('facilitador_usuario_id');
        if ($facilitadorId) {
            $facilitador = Usuario::where('id', $facilitadorId)->where('estado', true)->first();
            if ($facilitador) {
                return $next($request);
            }
        }

        // Si no está autorizado, redirige al formulario de acceso por Cédula + PIN en el login principal
        session()->forget('facilitador_usuario_id');
        return redirect()->route('login', ['modo' => 'induccion'])->with('status', 'Ingresa tu número de cédula y el PIN de 4 dígitos para acceder al módulo de Inducciones.');
    }
}
