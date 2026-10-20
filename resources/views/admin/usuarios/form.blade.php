@extends('layouts.admin')

@php
    $editando = (bool) $usuarioEditado;
    $input = 'w-full px-4 py-2.5 rounded-xl border border-line bg-white text-xs sm:text-[13px] font-sans text-brand-dark placeholder:text-slate-400 focus:outline-none focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition-all disabled:bg-slate-100 disabled:text-slate-400 disabled:cursor-not-allowed';
    $sedeSeleccionada = old('sede_id', $usuarioEditado?->sede_id ?? $usuarioEditado?->area?->sede_id);
    $areaSeleccionada = old('area_id', $usuarioEditado?->area_id);
@endphp

@section('title', $editando ? 'Editar Usuario' : 'Registrar Usuario')
@section('page_title', $editando ? 'Editar Usuario' : 'Registrar Nuevo Usuario')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">

    @include('admin._banner', [
        'insignia' => $editando ? 'Modificación de Usuario' : 'Nuevo Colaborador',
        'titulo' => $editando ? 'Editar: ' . $usuarioEditado->nombre_completo : 'Registrar Nuevo Usuario',
        'descripcion' => $editando
            ? 'Actualiza los datos del usuario, su rol institucional, sede asignada o área a la que pertenece.'
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

                <!-- Sede Asignada -->
                <div>
                    <label for="sede_id" class="block text-xs font-bold text-brand-dark mb-1.5">
                        Sede Institucional <span id="req-sede" class="text-red-500">*</span>
                    </label>
                    <select id="sede_id" name="sede_id" class="{{ $input }}">
                        <option value="">— Seleccionar Sede Institucional —</option>
                        @foreach ($sedes as $sede)
                            <option value="{{ $sede->id }}" {{ (string) $sedeSeleccionada === (string) $sede->id ? 'selected' : '' }}>
                                {{ $sede->nombre }} · {{ $sede->ciudad }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-[11px] text-muted mt-1">Ubicación física o planta donde labora el colaborador.</p>
                </div>

                <!-- Área Asignada (según la Sede seleccionada) -->
                <div id="contenedor-area">
                    <label for="area_id" class="block text-xs font-bold text-brand-dark mb-1.5">
                        Área Institucional <span id="req-area" class="text-red-500">*</span>
                    </label>
                    <select id="area_id" name="area_id" class="{{ $input }}" {{ !$sedeSeleccionada ? 'disabled' : '' }}>
                        @if (!$sedeSeleccionada)
                            <option value="">— Primero selecciona una sede institucional —</option>
                        @else
                            <option value="">— Seleccionar Área —</option>
                            @php
                                $areasDeSede = $areas->filter(fn($a) => (string) $a->sede_id === (string) $sedeSeleccionada);
                            @endphp
                            @forelse ($areasDeSede as $area)
                                <option value="{{ $area->id }}" {{ (string) $areaSeleccionada === (string) $area->id ? 'selected' : '' }}>
                                    {{ $area->nombre }}
                                </option>
                            @empty
                                <option value="">— No hay áreas registradas para esta sede —</option>
                            @endforelse
                        @endif
                    </select>
                    <p class="text-[11px] text-muted mt-1">
                        <i class="fa-solid fa-filter text-brand-blue/70 text-[10px] mr-0.5"></i>
                        Solo se permiten áreas pertenecientes a la sede asignada.
                    </p>
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

            <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-between gap-3 sm:gap-0 pt-4 border-t border-line/60">
                <a href="{{ route('admin.usuarios.index') }}"
                   class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-heading font-bold text-xs text-center transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-brand-blue hover:bg-brand-deep text-white font-heading font-bold text-xs sm:text-sm shadow-xs hover:-translate-y-0.5 transition-all cursor-pointer">
                    <i class="fa-solid fa-check"></i>
                    <span>{{ $editando ? 'Actualizar Usuario' : 'Registrar y Habilitar' }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const todasLasAreas = @json($areas);
    const selectSede = document.getElementById('sede_id');
    const selectArea = document.getElementById('area_id');
    const reqArea = document.getElementById('req-area');
    const reqSede = document.getElementById('req-sede');
    const selectRol = document.getElementById('rol');

    function actualizarAreasPorSede(areaSeleccionadaId = null) {
        if (!selectSede || !selectArea) return;

        const sedeId = selectSede.value;
        const valorPrevio = areaSeleccionadaId !== null ? String(areaSeleccionadaId) : selectArea.value;

        selectArea.innerHTML = '';

        if (!sedeId) {
            selectArea.disabled = true;
            const opt = document.createElement('option');
            opt.value = '';
            opt.textContent = '— Primero selecciona una sede institucional —';
            selectArea.appendChild(opt);
            return;
        }

        const filtradas = todasLasAreas.filter(a => String(a.sede_id) === String(sedeId));

        if (filtradas.length === 0) {
            selectArea.disabled = true;
            const opt = document.createElement('option');
            opt.value = '';
            opt.textContent = '— No hay áreas registradas para esta sede —';
            selectArea.appendChild(opt);
            return;
        }

        selectArea.disabled = false;
        const optDefault = document.createElement('option');
        optDefault.value = '';
        optDefault.textContent = '— Seleccionar Área —';
        selectArea.appendChild(optDefault);

        let encontrada = false;
        filtradas.forEach(a => {
            const opt = document.createElement('option');
            opt.value = a.id;
            opt.textContent = a.nombre;
            if (String(a.id) === valorPrevio) {
                opt.selected = true;
                encontrada = true;
            }
            selectArea.appendChild(opt);
        });

        if (!encontrada) {
            selectArea.value = '';
        }
    }

    function verificarRol(rol) {
        if (rol === 'ADMINISTRADOR') {
            if (reqArea) reqArea.classList.add('hidden');
            if (reqSede) reqSede.classList.add('hidden');
        } else {
            if (reqArea) reqArea.classList.remove('hidden');
            if (reqSede) reqSede.classList.remove('hidden');
        }
    }

    if (selectSede) {
        selectSede.addEventListener('change', () => {
            actualizarAreasPorSede();
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (selectRol) {
            verificarRol(selectRol.value);
        }
        const areaInicial = @json($areaSeleccionada);
        if (selectSede && selectSede.value) {
            actualizarAreasPorSede(areaInicial);
        }
    });
</script>
@endsection
