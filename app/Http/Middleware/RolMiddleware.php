<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restringe el acceso a rutas según el rol del usuario autenticado.
 * Uso: ->middleware('rol:JEFE_AREA') o ->middleware('rol:JEFE_AREA,ADMINISTRADOR')
 */
class RolMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        /** @var \App\Models\Usuario|null $usuario */
        $usuario = $request->user();

        if (! $usuario) {
            return redirect()->route('login');
        }

        if (! in_array($usuario->rol, $roles, true)) {
            return redirect()->route($usuario->rutaPanel())
                ->with('error_acceso', 'No tienes permisos para acceder a esa sección.');
        }

        return $next($request);
    }
}
