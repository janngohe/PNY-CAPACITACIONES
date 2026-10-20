@extends('layouts.admin')

@section('title', 'Gestión de Sedes')
@section('page_title', 'Sedes Institucionales')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    @include('admin._banner', [
        'insignia' => 'Estructura Institucional',
        'titulo' => 'Sedes y Complejos Operativos',
        'descripcion' => 'Administra las sedes físicas, plantas de procesamiento y complejos acuícolas de C.I. Piscícola New York. Asocia áreas a cada sede para estructurar el seguimiento y las estadísticas de avance.',
        'icono' => 'fa-building-shield',
    ])

    <!-- MÉTRICAS RÁPIDAS DE SEDES -->
    @php
        $totalSedes = $sedes->count();
        $sedesActivas = $sedes->where('estado', true)->count();
        $totalAreasEnSedes = $sedes->sum('areas_count');
        $totalEmpleadosEnSedes = $sedes->sum('empleados_count');
    @endphp
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-line/80 p-4 shadow-2xs flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-blue-50 text-brand-blue flex items-center justify-center text-lg font-bold shrink-0">
                <i class="fa-solid fa-building"></i>
            </div>
            <div>
                <span class="text-[11px] text-muted font-bold block uppercase tracking-wider">Total Sedes</span>
                <span class="font-heading font-extrabold text-xl text-brand-dark">{{ $totalSedes }}</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-line/80 p-4 shadow-2xs flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg font-bold shrink-0">
                <i class="fa-solid fa-check-double"></i>
            </div>
            <div>
                <span class="text-[11px] text-muted font-bold block uppercase tracking-wider">Sedes Activas</span>
                <span class="font-heading font-extrabold text-xl text-brand-dark">{{ $sedesActivas }}</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-line/80 p-4 shadow-2xs flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg font-bold shrink-0">
                <i class="fa-solid fa-sitemap"></i>
            </div>
            <div>
                <span class="text-[11px] text-muted font-bold block uppercase tracking-wider">Áreas Asignadas</span>
                <span class="font-heading font-extrabold text-xl text-brand-dark">{{ $totalAreasEnSedes }}</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-line/80 p-4 shadow-2xs flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg font-bold shrink-0">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <span class="text-[11px] text-muted font-bold block uppercase tracking-wider">Colaboradores</span>
                <span class="font-heading font-extrabold text-xl text-brand-dark">{{ $totalEmpleadosEnSedes }}</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

        <!-- FORMULARIO DE CREAR / EDITAR SEDE -->
        <div class="bg-white rounded-3xl border border-line/80 shadow-2xs p-6 space-y-4">
            <div class="border-b border-line/60 pb-3">
                <span class="text-[10px] uppercase font-bold text-brand-blue tracking-wider">Formulario</span>
                <h3 id="form-sede-title" class="font-heading font-extrabold text-base text-brand-dark">Registrar Nueva Sede</h3>
            </div>

            <form id="form-sede" method="POST" action="{{ route('admin.sedes.store') }}" class="space-y-4">
                @csrf
                <div id="method-container-sede"></div>

                <div>
                    <label for="sede-nombre" class="block text-xs font-bold text-brand-dark mb-1">Nombre de la Sede <span class="text-red-500">*</span></label>
                    <input type="text" id="sede-nombre" name="nombre" required maxlength="150"
                           placeholder="Ej. Sede Represa de Betania"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-line bg-slate-50/50 text-xs text-brand-dark focus:bg-white focus:outline-none focus:border-brand-blue">
                </div>

                <div>
                    <label for="sede-ciudad" class="block text-xs font-bold text-brand-dark mb-1">Ciudad / Municipio <span class="text-red-500">*</span></label>
                    <input type="text" id="sede-ciudad" name="ciudad" required maxlength="100"
                           placeholder="Ej. Rivera - Huila o Yaguará - Huila"
                           value="Rivera - Huila"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-line bg-slate-50/50 text-xs text-brand-dark focus:bg-white focus:outline-none focus:border-brand-blue">
                </div>

                <div>
                    <label for="sede-direccion" class="block text-xs font-bold text-brand-dark mb-1">Dirección / Ubicación Específica</label>
                    <input type="text" id="sede-direccion" name="direccion" maxlength="255"
                           placeholder="Ej. Km 4 Vía Rivera, Huila"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-line bg-slate-50/50 text-xs text-brand-dark focus:bg-white focus:outline-none focus:border-brand-blue">
                </div>

                <div>
                    <label for="sede-descripcion" class="block text-xs font-bold text-brand-dark mb-1">Descripción / Función Operativa</label>
                    <textarea id="sede-descripcion" name="descripcion" rows="3" maxlength="255"
                              placeholder="Detalla las actividades primarias de esta sede (ej. centro de alevinaje, jaulas flotantes, empaque)..."
                              class="w-full px-3.5 py-2 rounded-xl border border-line bg-slate-50/50 text-xs text-brand-dark focus:bg-white focus:outline-none focus:border-brand-blue resize-none"></textarea>
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <button type="submit" id="btn-submit-sede"
                            class="flex-1 px-4 py-2.5 rounded-xl bg-brand-blue hover:bg-brand-deep text-white font-heading font-bold text-xs shadow-xs transition-colors cursor-pointer">
                        Registrar Sede
                    </button>
                    <button type="button" id="btn-cancel-sede" onclick="limpiarFormSede()"
                            class="hidden px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors cursor-pointer">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>

        <!-- LISTADO DE SEDES -->
        <div class="lg:col-span-2 space-y-4">
            <div class="flex items-center justify-between px-1">
                <div>
                    <h4 class="font-heading font-extrabold text-sm text-brand-dark">Sedes Institucionales Registradas ({{ $sedes->count() }})</h4>
                    <p class="text-[11px] text-muted">Haz clic sobre cualquier sede para explorar sus áreas vinculadas y progreso formativo.</p>
                </div>
            </div>

            <div class="space-y-4">
                @forelse ($sedes as $sede)
                    <div id="sede-card-{{ $sede->id }}"
                         class="bg-white rounded-3xl border border-line/80 shadow-2xs overflow-hidden transition-all hover:border-brand-blue/40 {{ $sede->estado ? '' : 'opacity-70 bg-slate-50/50' }}">
                        
                        <!-- ENCABEZADO DE LA SEDE -->
                        <div class="p-5 cursor-pointer select-none" onclick="toggleDetalleSede({{ $sede->id }})">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="flex items-start gap-3.5">
                                    <div class="w-12 h-12 rounded-2xl bg-brand-light text-brand-blue flex items-center justify-center text-lg font-bold shrink-0 mt-0.5">
                                        <i class="fa-solid fa-building"></i>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <h4 class="font-heading font-bold text-base text-brand-dark leading-tight">{{ $sede->nombre }}</h4>
                                            <span class="shrink-0 px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $sede->estado ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500' }}">
                                                {{ $sede->estado ? 'Activa' : 'Inactiva' }}
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-3 text-xs text-slate-500 mt-1 flex-wrap">
                                            <span class="inline-flex items-center gap-1 font-semibold text-brand-blue">
                                                <i class="fa-solid fa-location-dot text-[11px]"></i> {{ $sede->ciudad }}
                                            </span>
                                            @if ($sede->direccion)
                                                <span class="text-slate-400">·</span>
                                                <span class="text-slate-600 truncate max-w-xs">{{ $sede->direccion }}</span>
                                            @endif
                                        </div>
                                        @if ($sede->descripcion)
                                            <p class="text-xs text-slate-500 line-clamp-1 mt-1">{{ $sede->descripcion }}</p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Métricas rápidas de la sede -->
                                <div class="flex items-center gap-2.5 self-end sm:self-auto shrink-0">
                                    <div class="flex items-center gap-1.5 text-xs">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-slate-100 text-slate-700 font-bold" title="Áreas asociadas">
                                            <i class="fa-solid fa-sitemap text-[10px] text-slate-400"></i> {{ $sede->areas_count }} Áreas
                                        </span>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-blue-50 text-brand-blue font-bold" title="Empleados activos">
                                            <i class="fa-solid fa-users text-[10px]"></i> {{ $sede->empleados_count }}
                                        </span>
                                    </div>

                                    <button type="button"
                                            id="btn-toggle-icon-sede-{{ $sede->id }}"
                                            class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-brand-light hover:text-brand-blue text-slate-600 flex items-center justify-center transition-transform duration-200 cursor-pointer">
                                        <i class="fa-solid fa-chevron-down text-xs transition-transform duration-200" id="icon-chevron-sede-{{ $sede->id }}"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Barra de progreso de capacitaciones de la sede -->
                            <div class="mt-4 pt-3 border-t border-line/60">
                                <div class="flex items-center justify-between text-xs mb-1.5">
                                    <span class="font-bold text-slate-700">Avance de Capacitación en la Sede</span>
                                    <span class="font-heading font-extrabold text-brand-blue">{{ $sede->promedio_avance }}%</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                                    <div class="bg-gradient-to-r from-brand-blue to-cyan-500 h-2.5 rounded-full transition-all duration-500" style="width: {{ $sede->promedio_avance }}%"></div>
                                </div>
                                <div class="flex items-center justify-between text-[11px] text-muted mt-1">
                                    <span>{{ $sede->total_asignaciones }} asignaciones formativas</span>
                                    <span>{{ $sede->completadas }} completadas · {{ $sede->en_progreso }} en curso · {{ $sede->pendientes }} pendientes</span>
                                </div>
                            </div>
                        </div>

                        <!-- DESGLOSE DETALLADO DE ÁREAS Y ACCIONES (ACORDEÓN) -->
                        <div id="detalle-sede-{{ $sede->id }}" class="hidden border-t border-line/60 bg-slate-50/60 p-5 space-y-4">
                            
                            <!-- Acciones de administración de la sede -->
                            <div class="flex items-center justify-between pb-3 border-b border-line/50">
                                <span class="text-xs font-bold text-brand-dark uppercase tracking-wider">Áreas vinculadas a esta sede</span>
                                <div class="flex items-center gap-2">
                                    <button type="button"
                                            onclick="editarSede({{ $sede->id }}, '{{ addslashes($sede->nombre) }}', '{{ addslashes($sede->ciudad) }}', '{{ addslashes($sede->direccion ?? '') }}', '{{ addslashes($sede->descripcion ?? '') }}')"
                                            class="px-3 py-1.5 rounded-lg bg-white border border-line hover:border-brand-blue hover:text-brand-blue text-xs font-bold text-slate-700 transition-colors shadow-2xs cursor-pointer">
                                        <i class="fa-solid fa-pen-to-square text-[11px]"></i> Editar Sede
                                    </button>
                                    
                                    <form method="POST" action="{{ route('admin.sedes.estado', $sede) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="px-3 py-1.5 rounded-lg text-xs font-bold transition-colors cursor-pointer {{ $sede->estado ? 'bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200' : 'bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200' }}">
                                            <i class="fa-solid {{ $sede->estado ? 'fa-ban' : 'fa-check' }} text-[11px]"></i>
                                            {{ $sede->estado ? 'Desactivar' : 'Activar' }}
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Listado de Áreas vinculadas -->
                            @if ($sede->areas->isNotEmpty())
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    @foreach ($sede->areas as $areaVinculada)
                                        <div class="bg-white p-3.5 rounded-2xl border border-line/70 flex items-center justify-between gap-3 shadow-2xs">
                                            <div class="flex items-center gap-2.5">
                                                <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center text-xs font-bold">
                                                    <i class="fa-solid fa-sitemap"></i>
                                                </div>
                                                <div>
                                                    <p class="text-xs font-bold text-brand-dark">{{ $areaVinculada->nombre }}</p>
                                                    <p class="text-[11px] text-muted">{{ $areaVinculada->empleados_count }} colaboradores activos</p>
                                                </div>
                                            </div>
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $areaVinculada->estado ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                                {{ $areaVinculada->estado ? 'Activa' : 'Inactiva' }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="p-4 rounded-2xl bg-amber-50/60 border border-amber-200/60 text-center">
                                    <p class="text-xs text-amber-800 font-medium">Aún no hay áreas vinculadas a esta sede.</p>
                                    <p class="text-[11px] text-amber-700 mt-0.5">Puedes asignarlas desde la sección <a href="{{ route('admin.areas.index') }}" class="font-bold underline hover:text-amber-900">Gestión de Áreas</a>.</p>
                                </div>
                            @endif

                        </div>
                    </div>
                @empty
                    <div class="p-8 bg-white rounded-3xl border border-line/80 text-center">
                        <i class="fa-solid fa-building-circle-exclamation text-3xl text-slate-300 mb-2"></i>
                        <p class="text-xs text-muted">No hay sedes institucionales registradas en el sistema.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

</div>

<script>
    function toggleDetalleSede(id) {
        const detalle = document.getElementById('detalle-sede-' + id);
        const icon = document.getElementById('icon-chevron-sede-' + id);
        if (!detalle) return;

        if (detalle.classList.contains('hidden')) {
            detalle.classList.remove('hidden');
            if (icon) icon.classList.add('rotate-180');
        } else {
            detalle.classList.add('hidden');
            if (icon) icon.classList.remove('rotate-180');
        }
    }

    function editarSede(id, nombre, ciudad, direccion, descripcion) {
        document.getElementById('form-sede-title').innerText = 'Editar Sede: ' + nombre;
        document.getElementById('sede-nombre').value = nombre;
        document.getElementById('sede-ciudad').value = ciudad || 'Rivera - Huila';
        document.getElementById('sede-direccion').value = direccion || '';
        document.getElementById('sede-descripcion').value = descripcion || '';

        const form = document.getElementById('form-sede');
        form.action = `/admin/sedes/${id}`;

        const methodContainer = document.getElementById('method-container-sede');
        methodContainer.innerHTML = '<input type="hidden" name="_method" value="PUT">';

        document.getElementById('btn-submit-sede').innerText = 'Guardar Cambios';
        document.getElementById('btn-cancel-sede').classList.remove('hidden');

        form.scrollIntoView({ behavior: 'smooth', block: 'center' });
        document.getElementById('sede-nombre').focus();
    }

    function limpiarFormSede() {
        document.getElementById('form-sede-title').innerText = 'Registrar Nueva Sede';
        document.getElementById('sede-nombre').value = '';
        document.getElementById('sede-ciudad').value = 'Rivera - Huila';
        document.getElementById('sede-direccion').value = '';
        document.getElementById('sede-descripcion').value = '';

        const form = document.getElementById('form-sede');
        form.action = "{{ route('admin.sedes.store') }}";

        document.getElementById('method-container-sede').innerHTML = '';
        document.getElementById('btn-submit-sede').innerText = 'Registrar Sede';
        document.getElementById('btn-cancel-sede').classList.add('hidden');
    }
</script>
@endsection
