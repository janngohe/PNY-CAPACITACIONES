@extends('layouts.admin')

@php
    $editando = (bool) $usuarioEditado;
    $input = 'w-full px-4 py-2.5 rounded-xl border border-line bg-white text-xs sm:text-[13px] font-sans text-brand-dark placeholder:text-slate-400 focus:outline-none focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition-all';
@endphp

@section('title', $editando ? 'Editar Usuario' : 'Registrar Usuario')
@section('page_title', $editando ? 'Editar Usuario' : 'Registrar Nuevo Usuario')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">

    @include('admin._banner', [
        'insignia' => $editando ? 'Modificación de Usuario' : 'Nuevo Colaborador',
        'titulo' => $editando ? 'Editar: ' . $usuarioEditado->nombre_completo : 'Registrar Nuevo Usuario',
        'descripcion' => $editando
            ? 'Actualiza los datos del usuario, su rol institucional o el área a la que pertenece.'
            : 'La contraseña inicial del nuevo usuario será su mismo número de documento. Al iniciar sesión por primera vez, se le obligará a crear una nueva contraseña segura.',
        'icono' => $editando ? 'fa-user-pen' : 'fa-user-plus',
    ])

    @if (isset($errors) && $errors->any())
        <div class="p-4 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-xs shadow-xs" role="alert">
            <p class="font-bold mb-1">Por favor verifica los siguientes errores:</p>
            <ul class="list-disc pl-5 space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-3xl border border-line/80 shadow-2xs p-6 sm:p-8">
        <form method="POST" action="{{ $editando ? route('admin.usuarios.update', $usuarioEditado) : route('admin.usuarios.store') }}" class="space-y-6">
            @csrf
            @if ($editando) @method('PUT') @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <!-- Identificación / Cédula -->
                <div>
                    <label for="identificacion" class="block text-xs font-bold text-brand-dark mb-1.5">
                        Número de Identificación (C.C.) <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="identificacion" name="identificacion" required
                           value="{{ old('identificacion', $usuarioEditado?->identificacion) }}"
                           placeholder="Ej. 1007342111" class="{{ $input }}">
                    <p class="text-[11px] text-muted mt-1">Se usará como usuario para ingresar y como contraseña inicial.</p>
                </div>

                <!-- Nombre Completo -->
                <div>
                    <label for="nombre_completo" class="block text-xs font-bold text-brand-dark mb-1.5">
                        Nombre Completo <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="nombre_completo" name="nombre_completo" required
                           value="{{ old('nombre_completo', $usuarioEditado?->nombre_completo) }}"
                           placeholder="Ej. Emerson Triviño Trujillo" class="{{ $input }}">
                    <p class="text-[11px] text-muted mt-1">Nombre tal como aparecerá en los certificados emitidos.</p>
                </div>

                <!-- Rol -->
                <div>
                    <label for="rol" class="block text-xs font-bold text-brand-dark mb-1.5">
                        Rol en la Plataforma <span class="text-red-500">*</span>
                    </label>
                    <select id="rol" name="rol" required class="{{ $input }}" onchange="verificarRol(this.value)">
                        @foreach ($roles as $clave => $nombre)
                            <option value="{{ $clave }}" {{ old('rol', $usuarioEditado?->rol ?? 'EMPLEADO') === $clave ? 'selected' : '' }}>
                                {{ $nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Área Asignada -->
                <div id="contenedor-area">
                    <label for="area_id" class="block text-xs font-bold text-brand-dark mb-1.5">
                        Área Institucional <span id="req-area" class="text-red-500">*</span>
                    </label>
                    <select id="area_id" name="area_id" class="{{ $input }}">
                        <option value="">— Seleccionar Área —</option>
                        @foreach ($areas as $area)
                            <option value="{{ $area->id }}" {{ (string) old('area_id', $usuarioEditado?->area_id) === (string) $area->id ? 'selected' : '' }}>
                                {{ $area->nombre }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-[11px] text-muted mt-1">Define las capacitaciones que podrá ver si es empleado o publicar si es jefe.</p>
                </div>
            </div>

            @unless ($editando)
                <div class="p-4 rounded-2xl bg-brand-light/60 border border-brand-blue/20 flex items-start gap-3 text-xs text-brand-dark">
                    <i class="fa-solid fa-circle-info text-brand-blue mt-0.5 text-sm"></i>
                    <div>
                        <strong class="font-bold">Política de primer ingreso:</strong>
                        <p class="text-slate-600 mt-0.5">El usuario iniciará sesión ingresando su cédula en ambos campos (usuario y clave). El sistema le obligará a crear una nueva contraseña personal antes de permitirle navegar.</p>
                    </div>
                </div>
            @endunless

            <div class="flex items-center justify-between pt-4 border-t border-line/60">
                <a href="{{ route('admin.usuarios.index') }}"
                   class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-heading font-bold text-xs transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-brand-blue hover:bg-brand-deep text-white font-heading font-bold text-xs sm:text-sm shadow-xs hover:-translate-y-0.5 transition-all cursor-pointer">
                    <i class="fa-solid fa-check"></i>
                    <span>{{ $editando ? 'Actualizar Usuario' : 'Registrar y Habilitar' }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function verificarRol(rol) {
        const reqArea = document.getElementById('req-area');
        if (rol === 'ADMINISTRADOR') {
            reqArea.classList.add('hidden');
        } else {
            reqArea.classList.remove('hidden');
        }
    }
    document.addEventListener('DOMContentLoaded', () => {
        verificarRol(document.getElementById('rol').value);
    });
</script>
@endsection
