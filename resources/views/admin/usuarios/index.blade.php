@extends('layouts.admin')

@section('title', 'Gestión de Usuarios')
@section('page_title', 'Gestión de Usuarios')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    @include('admin._banner', [
        'insignia' => 'Seguridad y Accesos',
        'titulo' => 'Usuarios de la Plataforma',
        'descripcion' => 'Registra nuevos colaboradores, edita sus datos, asigna roles y áreas, o desactiva cuentas cuando se requiera (se conserva todo su historial).',
        'icono' => 'fa-users-gear',
    ])

    <!-- BARRA SUPERIOR DE ACCIONES Y RESUMEN -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-2">
            <span class="px-3 py-1.5 rounded-xl bg-white border border-line text-xs font-semibold text-slate-700 shadow-2xs">
                Total: <strong class="text-brand-dark">{{ $resumen['total'] }}</strong>
            </span>
            <span class="px-3 py-1.5 rounded-xl bg-emerald-50 border border-emerald-200 text-xs font-semibold text-emerald-800">
                Activos: <strong>{{ $resumen['activos'] }}</strong>
            </span>
            <span class="px-3 py-1.5 rounded-xl bg-slate-100 border border-line text-xs font-semibold text-slate-600">
                Inactivos: <strong>{{ $resumen['inactivos'] }}</strong>
            </span>
        </div>

        <a href="{{ route('admin.usuarios.create') }}" id="btn-crear-usuario"
           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-blue hover:bg-brand-deep text-white font-heading font-bold text-xs sm:text-sm shadow-xs transition-colors self-start sm:self-auto cursor-pointer">
            <i class="fa-solid fa-user-plus"></i>
            <span>Registrar Nuevo Usuario</span>
        </a>
    </div>

    <!-- FILTROS DE BÚSQUEDA -->
    <div class="bg-white p-4 sm:p-5 rounded-3xl border border-line/80 shadow-2xs">
        <form method="GET" action="{{ route('admin.usuarios.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div class="lg:col-span-2">
                <label for="filtro-q" class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">Buscar por nombre o cédula</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>
                    <input type="text" id="filtro-q" name="q" value="{{ $filtros['q'] }}"
                           placeholder="Ej. Emerson, 1007342111..."
                           class="w-full pl-9 pr-3 py-2 rounded-xl border border-line bg-slate-50/50 text-xs text-brand-dark placeholder:text-slate-400 focus:bg-white focus:outline-none focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20">
                </div>
            </div>

            <div>
                <label for="filtro-rol" class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">Rol</label>
                <select id="filtro-rol" name="rol"
                        class="w-full px-3 py-2 rounded-xl border border-line bg-slate-50/50 text-xs text-brand-dark focus:bg-white focus:outline-none focus:border-brand-blue">
                    <option value="">Todos los roles</option>
                    @foreach ($roles as $clave => $nombre)
                        <option value="{{ $clave }}" {{ $filtros['rol'] === $clave ? 'selected' : '' }}>{{ $nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="filtro-area" class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">Área</label>
                <select id="filtro-area" name="area_id"
                        class="w-full px-3 py-2 rounded-xl border border-line bg-slate-50/50 text-xs text-brand-dark focus:bg-white focus:outline-none focus:border-brand-blue">
                    <option value="">Todas las áreas</option>
                    @foreach ($areas as $area)
                        <option value="{{ $area->id }}" {{ (string)$filtros['areaId'] === (string)$area->id ? 'selected' : '' }}>{{ $area->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="filtro-estado" class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">Estado</label>
                <div class="flex gap-2">
                    <select id="filtro-estado" name="estado"
                            class="flex-1 px-3 py-2 rounded-xl border border-line bg-slate-50/50 text-xs text-brand-dark focus:bg-white focus:outline-none focus:border-brand-blue">
                        <option value="">Todos</option>
                        <option value="1" {{ $filtros['estado'] === '1' ? 'selected' : '' }}>Activos</option>
                        <option value="0" {{ $filtros['estado'] === '0' ? 'selected' : '' }}>Inactivos</option>
                    </select>
                    <button type="submit" class="px-3.5 py-2 rounded-xl bg-brand-blue hover:bg-brand-deep text-white text-xs font-bold transition-colors cursor-pointer" title="Aplicar filtros">
                        <i class="fa-solid fa-filter"></i>
                    </button>
                    @if ($filtros['q'] || $filtros['rol'] || $filtros['areaId'] || $filtros['estado'] !== null)
                        <a href="{{ route('admin.usuarios.index') }}" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold transition-colors" title="Limpiar filtros">
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- TABLA DE USUARIOS -->
    <div class="bg-white rounded-3xl border border-line/80 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-muted border-b border-line/60">
                    <tr>
                        <th class="px-5 sm:px-6 py-3.5 font-bold">Colaborador</th>
                        <th class="px-4 py-3.5 font-bold">Identificación (C.C.)</th>
                        <th class="px-4 py-3.5 font-bold">Rol</th>
                        <th class="px-4 py-3.5 font-bold">Área Asignada</th>
                        <th class="px-4 py-3.5 font-bold text-center">Estado</th>
                        <th class="px-5 sm:px-6 py-3.5 font-bold text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line/50">
                    @forelse ($usuarios as $u)
                        <tr class="hover:bg-brand-light/30 transition-colors {{ $u->estado ? '' : 'opacity-60 bg-slate-50/50' }}">
                            <td class="px-5 sm:px-6 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-brand-deep to-brand-cyan text-white font-heading font-extrabold text-xs flex items-center justify-center shadow-2xs shrink-0">
                                        {{ strtoupper(substr($u->nombre_completo, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-bold text-brand-dark truncate leading-tight">{{ $u->nombre_completo }}</p>
                                        @if ($u->usuario_nuevo)
                                            <span class="inline-block text-[9px] font-bold text-amber-600 bg-amber-50 px-1.5 py-0.2 rounded mt-0.5">Primer ingreso pendiente</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 font-mono text-slate-700 font-semibold">{{ $u->identificacion }}</td>
                            <td class="px-4 py-3.5">
                                @php
                                    $rolBadge = match($u->rol) {
                                        'ADMINISTRADOR' => 'bg-purple-50 text-purple-700 border-purple-200',
                                        'JEFE_AREA' => 'bg-blue-50 text-brand-blue border-blue-200',
                                        default => 'bg-slate-100 text-slate-700 border-slate-200',
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $rolBadge }}">
                                    {{ $roles[$u->rol] ?? $u->rol }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-slate-600">
                                {{ $u->area->nombre ?? '— Sin área —' }}
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $u->estado ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-300' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $u->estado ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                    {{ $u->estado ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="px-5 sm:px-6 py-3.5 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('admin.usuarios.edit', $u) }}"
                                       class="p-2 rounded-lg bg-slate-100 hover:bg-brand-light hover:text-brand-blue text-slate-600 transition-colors"
                                       title="Editar usuario">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>

                                    <form method="POST" action="{{ route('admin.usuarios.restablecer', $u) }}" class="m-0"
                                          onsubmit="return confirm('¿Restablecer la contraseña de {{ $u->nombre_completo }} a su número de cédula ({{ $u->identificacion }})? Se le solicitará cambiarla al ingresar.');">
                                        @csrf
                                        <button type="submit"
                                                class="p-2 rounded-lg bg-slate-100 hover:bg-amber-100 hover:text-amber-800 text-slate-600 transition-colors cursor-pointer"
                                                title="Restablecer contraseña a la cédula">
                                            <i class="fa-solid fa-key"></i>
                                        </button>
                                    </form>

                                    @if ($u->id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.usuarios.estado', $u) }}" class="m-0"
                                              onsubmit="return confirm('¿{{ $u->estado ? 'Desactivar' : 'Activar' }} al usuario {{ $u->nombre_completo }}?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="p-2 rounded-lg border transition-colors cursor-pointer {{ $u->estado ? 'border-amber-200 bg-amber-50 hover:bg-amber-100 text-amber-700' : 'border-emerald-200 bg-emerald-50 hover:bg-emerald-100 text-emerald-700' }}"
                                                    title="{{ $u->estado ? 'Desactivar acceso' : 'Activar acceso' }}">
                                                <i class="fa-solid {{ $u->estado ? 'fa-user-slash' : 'fa-user-check' }}"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-muted">
                                <i class="fa-solid fa-users text-3xl mb-2 text-slate-300 block"></i>
                                No se encontraron usuarios con los filtros aplicados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($usuarios->hasPages())
            <div class="px-6 py-4 border-t border-line/60 bg-slate-50/50">
                {{ $usuarios->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
