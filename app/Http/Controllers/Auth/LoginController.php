<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Muestra la vista del formulario de login.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redireccionarPorRol(Auth::user());
        }

        return view('index');
    }

    /**
     * Procesa la autenticación del usuario.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'identification' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'identification.required' => 'El número de identificación es requerido.',
            'password.required' => 'La contraseña es requerida.',
        ]);

        $usuario = Usuario::where('identificacion', $credentials['identification'])->first();

        // Verificar existencia y que el usuario se encuentre activo
        if (!$usuario || !$usuario->estado) {
            throw ValidationException::withMessages([
                'identification' => ['El número de identificación no está registrado o la cuenta está inactiva.'],
            ]);
        }

        // Permitir autenticación tanto con hash como en texto plano si fue creado manualmente en BD con su cédula
        $esHashValido = Hash::check($credentials['password'], $usuario->password);
        $esPlanoValido = ($usuario->password === $credentials['password']);

        if (!$esHashValido && !$esPlanoValido) {
            throw ValidationException::withMessages([
                'password' => ['La contraseña ingresada es incorrecta.'],
            ]);
        }

        // Si fue ingresado en texto plano en la BD, actualizarlo a hash seguro
        if ($esPlanoValido && !$esHashValido) {
            $usuario->password = Hash::make($credentials['password']);
            $usuario->save();
        }

        // Autenticar la sesión
        Auth::login($usuario, $request->boolean('remember'));
        $request->session()->regenerate();

        // Redireccionar a la vista correspondiente según su rol
        return $this->redireccionarPorRol($usuario);
    }

    /**
     * Redirecciona al usuario a su panel correspondiente según su rol.
     */
    public function redireccionarPorRol(Usuario $usuario)
    {
        switch ($usuario->rol) {
            case 'ADMINISTRADOR':
                // Si existe ruta de administrador redirigir allí, si no al panel de capacitaciones
                return redirect()->intended(
                    route('admin.dashboard', [], false) ? route('admin.dashboard') : route('empleado.capacitaciones')
                );

            case 'JEFE_AREA':
                return redirect()->intended(
                    route('jefe.dashboard', [], false) ? route('jefe.dashboard') : route('empleado.capacitaciones')
                );

            case 'EMPLEADO':
            default:
                return redirect()->intended(route('empleado.capacitaciones'));
        }
    }

    /**
     * Actualiza la contraseña en el primer ingreso cuando usuario_nuevo es true.
     */
    public function actualizarPasswordPrimerIngreso(Request $request)
    {
        $request->validate([
            'new_password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'new_password.required' => 'Debes ingresar una nueva contraseña.',
            'new_password.min' => 'La nueva contraseña debe tener al menos 6 caracteres.',
            'new_password.confirmed' => 'La confirmación de la contraseña no coincide.',
        ]);

        /** @var \App\Models\Usuario $usuario */
        $usuario = Auth::user();

        if (!$usuario) {
            return redirect()->route('login');
        }

        // Actualizar la contraseña y desactivar la bandera de primer ingreso
        $usuario->password = Hash::make($request->new_password);
        $usuario->usuario_nuevo = false;
        $usuario->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Contraseña actualizada con éxito. ¡Bienvenido a la plataforma!',
            ]);
        }

        return back()->with('success_password', '¡Contraseña actualizada exitosamente! Ya puedes continuar.');
    }

    /**
     * Cierra la sesión del usuario.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Sesión finalizada correctamente.');
    }
}
