<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * Registro, consulta, edición y desactivación de usuarios.
 * Los usuarios nunca se eliminan (conservan su historial): solo se activan o desactivan.
 */
class UsuarioController extends Controller
{
    private const ROLES = [
        'EMPLEADO' => 'Empleado',
        'JEFE_AREA' => 'Jefe de Área',
        'ADMINISTRADOR' => 'Administrador',
    ];

    public function index(Request $request)
    {
        $q = trim((string) $request->query('q'));
        $rol = $request->query('rol');
        $areaId = $request->query('area_id');
        $estado = $request->query('estado');

        $usuarios = Usuario::query()
            ->with('area:id,nombre')
            ->when($q !== '', fn ($query) => $query->where(fn ($w) => $w
                ->where('nombre_completo', 'like', "%$q%")
                ->orWhere('identificacion', 'like', "%$q%")))
            ->when(isset(self::ROLES[$rol]), fn ($query) => $query->where('rol', $rol))
            ->when($areaId, fn ($query) => $query->where('area_id', $areaId))
            ->when(in_array($estado, ['1', '0'], true), fn ($query) => $query->where('estado', $estado === '1'))
            ->orderBy('nombre_completo')
            ->paginate(15)
            ->withQueryString();

        return view('admin.usuarios.index', [
            'usuarios' => $usuarios,
            'areas' => Area::orderBy('nombre')->get(['id', 'nombre']),
            'roles' => self::ROLES,
            'filtros' => compact('q', 'rol', 'areaId', 'estado'),
            'resumen' => [
                'total' => Usuario::count(),
                'activos' => Usuario::where('estado', true)->count(),
                'inactivos' => Usuario::where('estado', false)->count(),
            ],
        ]);
    }

    public function create()
    {
        return view('admin.usuarios.form', [
            'usuarioEditado' => null,
            'areas' => Area::where('estado', true)->orderBy('nombre')->get(['id', 'nombre']),
            'roles' => self::ROLES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $this->validar($request);

        Usuario::create([
            'identificacion' => $datos['identificacion'],
            'nombre_completo' => $datos['nombre_completo'],
            'rol' => $datos['rol'],
            'area_id' => $datos['area_id'] ?? null,
            // Contraseña inicial = número de identificación; se exige cambiarla en el primer ingreso
            'password' => $datos['identificacion'],
            'usuario_nuevo' => true,
            'estado' => $request->boolean('estado', true),
        ]);

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuario registrado. Su contraseña inicial es su número de identificación y deberá cambiarla al ingresar.');
    }

    public function edit(Usuario $usuario)
    {
        return view('admin.usuarios.form', [
            'usuarioEditado' => $usuario,
            'areas' => Area::where('estado', true)->orWhere('id', $usuario->area_id)->orderBy('nombre')->get(['id', 'nombre']),
            'roles' => self::ROLES,
        ]);
    }

    public function update(Request $request, Usuario $usuario): RedirectResponse
    {
        $datos = $this->validar($request, $usuario);

        if ($usuario->id === Auth::id() && $datos['rol'] !== 'ADMINISTRADOR') {
            return back()->withInput()->with('error', 'No puedes quitarte a ti mismo el rol de administrador.');
        }

        $usuario->update([
            'identificacion' => $datos['identificacion'],
            'nombre_completo' => $datos['nombre_completo'],
            'rol' => $datos['rol'],
            'area_id' => $datos['area_id'] ?? null,
        ]);

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function toggleEstado(Usuario $usuario): RedirectResponse
    {
        if ($usuario->id === Auth::id()) {
            return back()->with('error', 'No puedes desactivar tu propia cuenta.');
        }

        $usuario->update(['estado' => ! $usuario->estado]);

        return back()->with('success', $usuario->estado
            ? "Usuario {$usuario->nombre_completo} activado."
            : "Usuario {$usuario->nombre_completo} desactivado: ya no podrá ingresar (se conserva su historial).");
    }

    public function restablecerPassword(Usuario $usuario): RedirectResponse
    {
        $usuario->update([
            'password' => $usuario->identificacion,
            'usuario_nuevo' => true,
        ]);

        return back()->with('success', "Contraseña de {$usuario->nombre_completo} restablecida a su número de identificación. Deberá cambiarla en su próximo ingreso.");
    }

    private function validar(Request $request, ?Usuario $usuario = null): array
    {
        return $request->validate([
            'identificacion' => ['required', 'string', 'max:30', 'regex:/^[A-Za-z0-9\-]+$/', Rule::unique('usuarios', 'identificacion')->ignore($usuario?->id)],
            'nombre_completo' => ['required', 'string', 'max:150'],
            'rol' => ['required', Rule::in(array_keys(self::ROLES))],
            'area_id' => [
                Rule::requiredIf(fn () => in_array($request->input('rol'), ['EMPLEADO', 'JEFE_AREA'], true)),
                'nullable', 'integer', 'exists:areas,id',
            ],
        ], [
            'identificacion.required' => 'El número de identificación es obligatorio.',
            'identificacion.unique' => 'Ya existe un usuario con ese número de identificación.',
            'identificacion.regex' => 'La identificación solo admite letras, números y guiones.',
            'nombre_completo.required' => 'El nombre completo es obligatorio.',
            'rol.required' => 'Selecciona un rol.',
            'area_id.required' => 'Los empleados y jefes de área deben pertenecer a un área.',
        ]);
    }
}
