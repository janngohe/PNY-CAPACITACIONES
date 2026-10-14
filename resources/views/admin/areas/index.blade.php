@extends('layouts.admin')

@section('title', 'Gestión de Áreas')
@section('page_title', 'Áreas Institucionales')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    @include('admin._banner', [
        'insignia' => 'Estructura Organizacional',
        'titulo' => 'Áreas Institucionales',
        'descripcion' => 'Organiza los departamentos y áreas de C.I. Piscícola New York. Las capacitaciones se asignan a estas áreas para definir qué colaboradores deben cursarlas.',
        'icono' => 'fa-sitemap',
    ])

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

        <!-- FORMULARIO DE CREAR / EDITAR ÁREA -->
        <div class="bg-white rounded-3xl border border-line/80 shadow-2xs p-6 space-y-4">
            <div class="border-b border-line/60 pb-3">
                <span class="text-[10px] uppercase font-bold text-brand-blue tracking-wider">Gestión</span>
                <h3 id="form-area-title" class="font-heading font-extrabold text-base text-brand-dark">Crear Nueva Área</h3>
            </div>

            <form id="form-area" method="POST" action="{{ route('admin.areas.store') }}" class="space-y-4">
                @csrf
                <div id="method-container"></div>

                <div>
                    <label for="nombre" class="block text-xs font-bold text-brand-dark mb-1">Nombre del Área <span class="text-red-500">*</span></label>
                    <input type="text" id="nombre" name="nombre" required maxlength="150"
                           placeholder="Ej. Producción y Cosecha"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-line bg-slate-50/50 text-xs text-brand-dark focus:bg-white focus:outline-none focus:border-brand-blue">
                    <span class="text-[10px] text-muted block mt-1 flex items-center gap-1">
                        <i class="fa-solid fa-wand-magic-sparkles text-brand-blue/70"></i> Se convertirá automáticamente a formato título (ej. "Recursos Humanos").
                    </span>
                </div>

                <div>
                    <label for="descripcion" class="block text-xs font-bold text-brand-dark mb-1">Descripción</label>
                    <textarea id="descripcion" name="descripcion" rows="3" maxlength="255"
                              placeholder="Breve detalle de las funciones o procesos de esta área..."
                              class="w-full px-3.5 py-2 rounded-xl border border-line bg-slate-50/50 text-xs text-brand-dark focus:bg-white focus:outline-none focus:border-brand-blue resize-none"></textarea>
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <button type="submit" id="btn-submit-area"
                            class="flex-1 px-4 py-2.5 rounded-xl bg-brand-blue hover:bg-brand-deep text-white font-heading font-bold text-xs shadow-xs transition-colors cursor-pointer">
                        Crear Área
                    </button>
                    <button type="button" id="btn-cancel-area" onclick="limpiarFormArea()"
                            class="hidden px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors cursor-pointer">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>

        <!-- LISTADO DE ÁREAS CON DESGLOSE INTERACTIVO -->
        <div class="lg:col-span-2 space-y-4">
            <div class="flex items-center justify-between px-1">
                <div>
                    <h4 class="font-heading font-extrabold text-sm text-brand-dark">Áreas Registradas ({{ $areas->count() }})</h4>
                    <p class="text-[11px] text-muted">Haz clic sobre cualquier área o pulsa el botón para desplegar su equipo y cursos asignados.</p>
                </div>
            </div>

            <div class="space-y-3.5">
                @forelse ($areas as $area)
                    @php
                        $jefesArea = $area->usuarios->where('rol', 'JEFE_AREA');
                        $empleadosArea = $area->usuarios->where('rol', 'EMPLEADO');
                    @endphp
                    <div id="area-card-{{ $area->id }}"
                         class="bg-white rounded-3xl border border-line/80 shadow-2xs overflow-hidden transition-all hover:border-brand-blue/40 {{ $area->estado ? '' : 'opacity-70 bg-slate-50/50' }}">
                        
                        <!-- ENCABEZADO PRINCIPAL (CLICKEABLE PARA DESPLEGAR) -->
                        <div class="p-5 cursor-pointer select-none" onclick="toggleDetalleArea({{ $area->id }})">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-2xl bg-brand-light text-brand-blue flex items-center justify-center text-sm font-bold shrink-0">
                                        <i class="fa-solid fa-sitemap"></i>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h4 class="font-heading font-bold text-base text-brand-dark leading-tight">{{ $area->nombre }}</h4>
                                            <span class="shrink-0 px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $area->estado ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500' }}">
                                                {{ $area->estado ? 'Activa' : 'Inactiva' }}
                                            </span>
                                        </div>
                                        @if ($area->descripcion)
                                            <p class="text-xs text-slate-500 line-clamp-1 mt-0.5">{{ $area->descripcion }}</p>
                                        @else
                                            <p class="text-xs text-muted italic mt-0.5">Sin descripción registrada.</p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Resumen numérico y botón de desplegar -->
                                <div class="flex items-center gap-3 self-end sm:self-auto">
                                    <div class="flex items-center gap-1.5 text-xs">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-slate-100 text-slate-700 font-bold" title="Empleados activos">
                                            <i class="fa-solid fa-user-group text-[10px] text-slate-400"></i> {{ $area->empleados_count }}
                                        </span>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-blue-50 text-brand-blue font-bold" title="Jefes de área">
                                            <i class="fa-solid fa-user-tie text-[10px]"></i> {{ $area->jefes_count }}
                                        </span>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-indigo-50 text-indigo-700 font-bold" title="Capacitaciones vinculadas">
                                            <i class="fa-solid fa-graduation-cap text-[10px]"></i> {{ $area->capacitaciones_count }}
                                        </span>
                                    </div>

                                    <button type="button"
                                            id="btn-toggle-icon-{{ $area->id }}"
                                            class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-brand-light hover:text-brand-blue text-slate-600 flex items-center justify-center transition-transform duration-200 cursor-pointer">
                                        <i class="fa-solid fa-chevron-down text-xs transition-transform duration-200" id="icon-chevron-{{ $area->id }}"></i>
                                    </button>
                                </div>

                            </div>
                        </div>

                        <!-- PANEL DESPLEGABLE CON LA INFORMACIÓN DETALLADA -->
                        <div id="detalle-area-{{ $area->id }}" class="hidden border-t border-line/60 bg-slate-50/60 p-5 space-y-4">
                            
                            <!-- SECCIÓN: JEFES Y EQUIPO -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                
                                <!-- Jefes de Área -->
                                <div class="bg-white p-4 rounded-2xl border border-line/80 space-y-2">
                                    <div class="flex items-center justify-between pb-1.5 border-b border-line/50">
                                        <span class="text-[11px] font-bold text-brand-dark flex items-center gap-1.5">
                                            <i class="fa-solid fa-user-tie text-brand-blue"></i>
                                            Jefe(s) de Área
                                        </span>
                                        <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-blue-50 text-brand-blue">
                                            {{ $jefesArea->count() }}
                                        </span>
                                    </div>

                                    @if($jefesArea->isNotEmpty())
                                        <ul class="space-y-1.5 max-h-36 overflow-y-auto pr-1 text-xs">
                                            @foreach($jefesArea as $jf)
                                                <li class="flex items-center justify-between bg-slate-50 px-2.5 py-1.5 rounded-xl">
                                                    <span class="font-bold text-brand-dark">{{ $jf->nombre_completo }}</span>
                                                    <span class="text-[10px] font-mono text-muted">C.C. {{ $jf->identificacion }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p class="text-xs text-muted italic py-1">No hay jefes asignados actualmente a esta área.</p>
                                    @endif
                                </div>

                                <!-- Empleados del Área -->
                                <div class="bg-white p-4 rounded-2xl border border-line/80 space-y-2">
                                    <div class="flex items-center justify-between pb-1.5 border-b border-line/50">
                                        <span class="text-[11px] font-bold text-brand-dark flex items-center gap-1.5">
                                            <i class="fa-solid fa-users text-slate-500"></i>
                                            Colaboradores del Área
                                        </span>
                                        <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-slate-100 text-slate-700">
                                            {{ $empleadosArea->count() }}
                                        </span>
                                    </div>

                                    @if($empleadosArea->isNotEmpty())
                                        <ul class="space-y-1.5 max-h-36 overflow-y-auto pr-1 text-xs">
                                            @foreach($empleadosArea as $emp)
                                                <li class="flex items-center justify-between bg-slate-50 px-2.5 py-1.5 rounded-xl">
                                                    <span class="font-medium text-slate-800">{{ $emp->nombre_completo }}</span>
                                                    <span class="text-[10px] font-mono text-muted">C.C. {{ $emp->identificacion }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p class="text-xs text-muted italic py-1">No hay empleados adscritos a esta área.</p>
                                    @endif
                                </div>

                            </div>

                            <!-- SECCIÓN: CAPACITACIONES ASIGNADAS -->
                            <div class="bg-white p-4 rounded-2xl border border-line/80 space-y-2">
                                <div class="flex items-center justify-between pb-1.5 border-b border-line/50">
                                    <span class="text-[11px] font-bold text-brand-dark flex items-center gap-1.5">
                                        <i class="fa-solid fa-graduation-cap text-indigo-600"></i>
                                        Capacitaciones Asignadas a esta Área
                                    </span>
                                    <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-700">
                                        {{ $area->capacitaciones->count() }}
                                    </span>
                                </div>

                                @if($area->capacitaciones->isNotEmpty())
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1 max-h-44 overflow-y-auto pr-1">
                                        @foreach($area->capacitaciones as $cap)
                                            <div class="flex items-center justify-between p-2 rounded-xl bg-slate-50 border border-line/50 text-xs">
                                                <div class="truncate pr-2">
                                                    <span class="font-bold text-brand-dark block truncate">{{ $cap->titulo }}</span>
                                                    <span class="text-[10px] text-muted">
                                                        {{ $cap->duracion_estimada ? $cap->duracion_estimada . ' hrs' : 'Duración estándar' }} · Min: {{ (int)$cap->porcentaje_aprobacion }}%
                                                    </span>
                                                </div>
                                                <span class="shrink-0 w-2 h-2 rounded-full {{ $cap->estado ? 'bg-emerald-500' : 'bg-slate-300' }}" title="{{ $cap->estado ? 'Disponible' : 'Inactiva' }}"></span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-xs text-muted italic py-1">No hay programas de capacitación asociados específicamente a esta área.</p>
                                @endif
                            </div>

                            <!-- ACCIONES DE EDICIÓN Y ESTADO -->
                            <div class="flex items-center justify-between pt-2 border-t border-line/60">
                                <button type="button"
                                        onclick="event.stopPropagation(); editarArea({{ $area->id }}, '{{ addslashes($area->nombre) }}', '{{ addslashes($area->descripcion ?? '') }}')"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-brand-light hover:bg-brand-blue hover:text-white text-brand-blue font-bold text-xs transition-colors cursor-pointer">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                    <span>Editar Datos del Área</span>
                                </button>

                                <form method="POST" action="{{ route('admin.areas.estado', $area) }}" class="m-0"
                                      onclick="event.stopPropagation();"
                                      onsubmit="return confirm('¿{{ $area->estado ? 'Desactivar' : 'Activar' }} el área {{ $area->nombre }}?');">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            class="px-3 py-1.5 rounded-xl text-xs font-bold border transition-colors cursor-pointer {{ $area->estado ? 'border-amber-200 text-amber-700 bg-amber-50 hover:bg-amber-100' : 'border-emerald-200 text-emerald-700 bg-emerald-50 hover:bg-emerald-100' }}">
                                        {{ $area->estado ? 'Desactivar Área' : 'Activar Área' }}
                                    </button>
                                </form>
                            </div>

                        </div>

                    </div>
                @empty
                    <div class="bg-white rounded-3xl border border-line/80 p-8 text-center text-muted">
                        No hay áreas registradas todavía. Crea la primera en el formulario lateral.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script>
    function toggleDetalleArea(id) {
        const panel = document.getElementById(`detalle-area-${id}`);
        const icon = document.getElementById(`icon-chevron-${id}`);
        if (!panel) return;

        const estaOculto = panel.classList.contains('hidden');
        if (estaOculto) {
            panel.classList.remove('hidden');
            if (icon) icon.classList.add('rotate-180');
        } else {
            panel.classList.add('hidden');
            if (icon) icon.classList.remove('rotate-180');
        }
    }

    function editarArea(id, nombre, descripcion) {
        document.getElementById('form-area-title').innerText = 'Editar Área: ' + nombre;
        document.getElementById('nombre').value = nombre;
        document.getElementById('descripcion').value = descripcion;
        document.getElementById('btn-submit-area').innerText = 'Actualizar Área';
        document.getElementById('btn-cancel-area').classList.remove('hidden');

        const form = document.getElementById('form-area');
        form.action = `/admin/areas/${id}`;
        document.getElementById('method-container').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        window.scrollTo({ top: form.offsetTop - 80, behavior: 'smooth' });
    }

    function limpiarFormArea() {
        document.getElementById('form-area-title').innerText = 'Crear Nueva Área';
        document.getElementById('nombre').value = '';
        document.getElementById('descripcion').value = '';
        document.getElementById('btn-submit-area').innerText = 'Crear Área';
        document.getElementById('btn-cancel-area').classList.add('hidden');

        const form = document.getElementById('form-area');
        form.action = "{{ route('admin.areas.store') }}";
        document.getElementById('method-container').innerHTML = '';
    }

    function formatearNombreArea(texto) {
        if (!texto) return '';
        return texto
            .trim()
            .replace(/\s+/g, ' ')
            .toLowerCase()
            .split(' ')
            .map(word => word ? word.charAt(0).toUpperCase() + word.slice(1) : '')
            .join(' ');
    }

    const inputNombreArea = document.getElementById('nombre');
    if (inputNombreArea) {
        inputNombreArea.addEventListener('blur', function() {
            this.value = formatearNombreArea(this.value);
        });
    }

    const formArea = document.getElementById('form-area');
    if (formArea && inputNombreArea) {
        formArea.addEventListener('submit', function() {
            inputNombreArea.value = formatearNombreArea(inputNombreArea.value);
        });
    }
</script>
@endsection
