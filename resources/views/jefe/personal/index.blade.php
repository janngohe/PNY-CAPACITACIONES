@extends('layouts.jefe')

@section('title', 'Personal y Participaciones de Área')
@section('page_title', 'Personal y Participaciones de Área')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <!-- ========================================================
         BANNER HERO CON GRADIENTE Y RESUMEN DINÁMICO
         ======================================================== -->
    <div class="relative rounded-3xl bg-gradient-to-r from-brand-dark via-brand-deep to-[#0056b3] text-white p-6 sm:p-8 md:p-10 overflow-hidden shadow-lg border border-brand-blue/20">
        <div class="absolute -right-10 -top-10 w-64 h-64 rounded-full bg-brand-sky/15 blur-2xl pointer-events-none"></div>
        <div class="absolute right-1/3 -bottom-16 w-80 h-80 rounded-full bg-brand-blue/20 blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="max-w-2xl space-y-2">
                <!-- Badges de contexto activo -->
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-xs font-semibold text-brand-sky">
                        <i class="fa-solid fa-users text-xs"></i>
                        <span>Área: {{ $area->nombre ?? 'Sin área asignada' }}</span>
                    </span>

                    @if($filtroSede && ($sedeActiva = $sedes->firstWhere('id', $filtroSede)))
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-purple-500/20 backdrop-blur-md border border-purple-300/30 text-xs font-semibold text-purple-200">
                            <i class="fa-solid fa-location-dot text-xs text-purple-300"></i>
                            <span>Sede: {{ $sedeActiva->nombre }}</span>
                        </span>
                    @endif

                    @if($capacitacionSeleccionada)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-cyan-500/20 backdrop-blur-md border border-cyan-300/30 text-xs font-semibold text-cyan-200">
                            <i class="fa-solid fa-graduation-cap text-xs text-cyan-300"></i>
                            <span>Capacitación: {{ \Illuminate\Support\Str::limit($capacitacionSeleccionada->titulo, 35) }}</span>
                        </span>
                    @endif
                </div>

                <h1 class="font-heading font-extrabold text-2xl sm:text-3xl md:text-4xl tracking-tight leading-tight text-white">
                    Personal de <span class="text-transparent bg-clip-text bg-gradient-to-r from-white via-cyan-100 to-brand-sky">{{ $area->nombre ?? 'tu Área' }}</span>
                </h1>

                <p class="text-sm sm:text-base text-slate-200/90 leading-relaxed">
                    @if($capacitacionSeleccionada)
                        Monitoreo de participación y progreso en la capacitación <strong class="text-white">"{{ $capacitacionSeleccionada->titulo }}"</strong> para el personal de tu área.
                    @else
                        Consulta el equipo de trabajo de tu área, filtra por sedes institucionales y revisa su participación en las capacitaciones creadas.
                    @endif
                </p>
            </div>

            <!-- TARJETA RESUMEN DE INDICADORES DINÁMICOS -->
            <div class="lg:w-88 bg-white/10 backdrop-blur-md rounded-2xl p-4 sm:p-5 border border-white/15 shrink-0 shadow-inner">
                <div class="flex items-center justify-between text-xs text-white/90 mb-3 pb-2 border-b border-white/10">
                    <span class="font-semibold uppercase tracking-wider text-[11px] text-brand-sky">
                        {{ $capacitacionSeleccionada ? 'Participación en Curso' : 'Resumen del Personal' }}
                    </span>
                    <span class="font-bold text-xs text-white bg-white/15 px-2.5 py-0.5 rounded-full">
                        {{ $estadisticas['total'] }} {{ \Illuminate\Support\Str::plural('participante', $estadisticas['total']) }}
                    </span>
                </div>

                @if($capacitacionSeleccionada)
                    <div class="grid grid-cols-3 gap-2 text-center text-xs">
                        <div class="bg-white/10 rounded-xl p-2.5">
                            <span class="block text-xl font-heading font-extrabold text-emerald-300">{{ $estadisticas['completadas'] }}</span>
                            <span class="text-[10px] text-slate-300">Completadas</span>
                        </div>
                        <div class="bg-white/10 rounded-xl p-2.5">
                            <span class="block text-xl font-heading font-extrabold text-cyan-300">{{ $estadisticas['en_progreso'] }}</span>
                            <span class="text-[10px] text-slate-300">En Curso</span>
                        </div>
                        <div class="bg-white/10 rounded-xl p-2.5">
                            <span class="block text-xl font-heading font-extrabold text-amber-300">{{ $estadisticas['porcentaje'] }}%</span>
                            <span class="text-[10px] text-slate-300">Cumplimiento</span>
                        </div>
                    </div>
                @else
                    <div class="grid grid-cols-3 gap-2 text-center text-xs">
                        <div class="bg-white/10 rounded-xl p-2.5">
                            <span class="block text-xl font-heading font-extrabold text-emerald-300">{{ $estadisticas['activos'] }}</span>
                            <span class="text-[10px] text-slate-300">Activos</span>
                        </div>
                        <div class="bg-white/10 rounded-xl p-2.5">
                            <span class="block text-xl font-heading font-extrabold text-cyan-300">{{ $estadisticas['completadas'] }}</span>
                            <span class="text-[10px] text-slate-300">Cursos Listos</span>
                        </div>
                        <div class="bg-white/10 rounded-xl p-2.5">
                            <span class="block text-xl font-heading font-extrabold text-amber-300">{{ $estadisticas['con_certificados'] }}</span>
                            <span class="text-[10px] text-slate-300">Certificados</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="absolute bottom-0 left-0 w-full overflow-hidden leading-none pointer-events-none opacity-30">
            <svg class="relative block w-full h-5 text-white" viewBox="0 0 1200 40" preserveAspectRatio="none">
                <path d="M0,0 C150,35 350,10 500,25 C650,40 850,5 1000,20 C1100,30 1160,15 1200,25 L1200,40 L0,40 Z" fill="currentColor"/>
            </svg>
        </div>
    </div>

    @unless ($usuario->area_id)
        <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs sm:text-sm flex items-start gap-3" role="alert">
            <i class="fa-solid fa-triangle-exclamation text-amber-600 text-lg mt-0.5"></i>
            <p><strong>Tu usuario no tiene un área asignada.</strong> Para ver al personal correspondiente, solicita a administración que configure tu área en tu perfil.</p>
        </div>
    @endunless

    <!-- ========================================================
         SECCIÓN PRINCIPAL: FILTROS MULTI-CRITERIO Y LISTADO
         ======================================================== -->
    <section class="bg-white rounded-3xl border border-slate-300/80 shadow-sm ring-1 ring-slate-900/5 overflow-hidden" aria-labelledby="titulo-personal">
        
        <!-- BARRA SUPERIOR DE BÚSQUEDA Y FILTROS -->
        <div class="p-4 sm:p-6 border-b border-line/60 space-y-4">
            
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                <div>
                    <h2 id="titulo-personal" class="font-heading font-bold text-base sm:text-lg text-brand-dark flex items-center gap-2">
                        <span>Participantes del Área</span>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-brand-light text-brand-blue">
                            {{ $empleados->count() }}
                        </span>
                    </h2>
                    <p class="text-xs text-muted">
                        Personal perteneciente a <strong>{{ $area->nombre ?? 'tu área' }}</strong>
                        @if($filtroSede && ($s = $sedes->firstWhere('id', $filtroSede)))
                            en <strong>{{ $s->nombre }}</strong>
                        @else
                            en todas las sedes institucionales
                        @endif
                        @if($capacitacionSeleccionada)
                            &bull; Evaluando: <strong>{{ $capacitacionSeleccionada->titulo }}</strong>
                        @endif
                    </p>
                </div>

                @if ($buscar !== '' || $filtroEstado !== 'todos' || $filtroSede !== null && $filtroSede !== '' || $filtroCapacitacion !== null && $filtroCapacitacion !== '' || $filtroParticipacion !== 'todos')
                    <a href="{{ route('jefe.personal.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-rose-50 text-slate-600 hover:text-rose-700 text-xs font-bold transition-colors self-start md:self-auto" title="Limpiar todos los filtros">
                        <i class="fa-solid fa-rotate-left text-xs"></i>
                        <span>Restablecer Filtros</span>
                    </a>
                @endif
            </div>

            <!-- FORMULARIO DE FILTROS INTEGRADOS -->
            <form id="form-filtros-personal" method="GET" action="{{ route('jefe.personal.index') }}" class="space-y-3 m-0">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    
                    <!-- 1. Buscador de texto (Nombre o Documento) -->
                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text"
                               name="buscar"
                               value="{{ $buscar }}"
                               placeholder="Buscar por nombre o C.C..."
                               class="w-full pl-8 pr-3 py-2.5 rounded-xl bg-slate-50 border border-line focus:bg-white focus:border-brand-blue focus:ring-1 focus:ring-brand-blue text-xs font-medium text-brand-dark outline-hidden transition-all">
                    </div>

                    <!-- 2. Filtro por Sede Institucional -->
                    <div class="relative">
                        <i class="fa-solid fa-location-dot absolute left-3 top-1/2 -translate-y-1/2 text-purple-600 text-xs pointer-events-none"></i>
                        <select name="sede_id"
                                onchange="this.form.submit()"
                                class="w-full pl-8 pr-8 py-2.5 rounded-xl bg-slate-50 border border-line focus:bg-white focus:border-purple-600 focus:ring-1 focus:ring-purple-600 text-xs font-semibold text-brand-dark outline-hidden cursor-pointer appearance-none">
                            <option value="">Todas las Sedes ({{ $estadisticas['total'] }})</option>
                            @foreach ($sedes as $s)
                                <option value="{{ $s->id }}" {{ (string)$filtroSede === (string)$s->id ? 'selected' : '' }}>
                                    {{ $s->nombre }} ({{ $empleadosPorSede->get($s->id, 0) }})
                                </option>
                            @endforeach
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-[10px] pointer-events-none"></i>
                    </div>

                    <!-- 3. Filtro por Capacitación Creada / Asignada -->
                    <div class="relative">
                        <i class="fa-solid fa-graduation-cap absolute left-3 top-1/2 -translate-y-1/2 text-brand-blue text-xs pointer-events-none"></i>
                        <select name="capacitacion_id"
                                onchange="this.form.submit()"
                                class="w-full pl-8 pr-8 py-2.5 rounded-xl bg-slate-50 border border-line focus:bg-white focus:border-brand-blue focus:ring-1 focus:ring-brand-blue text-xs font-semibold text-brand-dark outline-hidden cursor-pointer appearance-none">
                            <option value="">Todas las Capacitaciones Creadas ({{ $capacitaciones->count() }})</option>
                            @foreach ($capacitaciones as $cap)
                                <option value="{{ $cap->id }}" {{ (string)$filtroCapacitacion === (string)$cap->id ? 'selected' : '' }}>
                                    {{ $cap->titulo }}
                                </option>
                            @endforeach
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-[10px] pointer-events-none"></i>
                    </div>

                    <!-- 4. Filtro por Estado del Usuario -->
                    <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl border border-line text-xs font-semibold">
                        <button type="submit"
                                name="estado"
                                value="todos"
                                class="flex-1 py-1.5 rounded-lg transition-colors text-center cursor-pointer {{ $filtroEstado === 'todos' ? 'bg-white text-brand-blue shadow-2xs font-bold' : 'text-slate-600 hover:text-brand-dark' }}">
                            Todos
                        </button>
                        <button type="submit"
                                name="estado"
                                value="activos"
                                class="flex-1 py-1.5 rounded-lg transition-colors text-center cursor-pointer {{ $filtroEstado === 'activos' ? 'bg-white text-emerald-700 shadow-2xs font-bold' : 'text-slate-600 hover:text-brand-dark' }}">
                            Activos
                        </button>
                        <button type="submit"
                                name="estado"
                                value="inactivos"
                                class="flex-1 py-1.5 rounded-lg transition-colors text-center cursor-pointer {{ $filtroEstado === 'inactivos' ? 'bg-white text-slate-700 shadow-2xs font-bold' : 'text-slate-600 hover:text-brand-dark' }}">
                            Inactivos
                        </button>
                    </div>

                </div>

                <!-- SUB-FILTRO DE PARTICIPACIÓN (SOLO CUANDO HAY UNA CAPACITACIÓN SELECCIONADA) -->
                @if ($capacitacionSeleccionada)
                    <div class="pt-2 border-t border-line/60 flex flex-wrap items-center justify-between gap-3 text-xs">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-slate-600 text-[11px] uppercase tracking-wider">
                                <i class="fa-solid fa-filter text-brand-blue"></i>
                                Participación en "{{ \Illuminate\Support\Str::limit($capacitacionSeleccionada->titulo, 30) }}":
                            </span>
                        </div>

                        <div class="inline-flex items-center gap-1.5 bg-slate-100 p-1 rounded-xl border border-line text-xs font-semibold">
                            <button type="submit"
                                    name="participacion"
                                    value="todos"
                                    class="px-2.5 py-1 rounded-lg transition-colors cursor-pointer {{ $filtroParticipacion === 'todos' ? 'bg-white text-brand-blue font-bold shadow-2xs' : 'text-slate-600 hover:text-brand-dark' }}">
                                Todos
                            </button>
                            <button type="submit"
                                    name="participacion"
                                    value="completadas"
                                    class="px-2.5 py-1 rounded-lg transition-colors cursor-pointer {{ $filtroParticipacion === 'completadas' ? 'bg-emerald-100 text-emerald-800 font-bold shadow-2xs' : 'text-slate-600 hover:text-emerald-700' }}">
                                <i class="fa-solid fa-circle-check text-[10px]"></i>
                                Completadas ({{ $estadisticas['completadas'] }})
                            </button>
                            <button type="submit"
                                    name="participacion"
                                    value="en_progreso"
                                    class="px-2.5 py-1 rounded-lg transition-colors cursor-pointer {{ $filtroParticipacion === 'en_progreso' ? 'bg-blue-100 text-blue-800 font-bold shadow-2xs' : 'text-slate-600 hover:text-blue-700' }}">
                                <i class="fa-solid fa-spinner text-[10px]"></i>
                                En Curso ({{ $estadisticas['en_progreso'] }})
                            </button>
                            <button type="submit"
                                    name="participacion"
                                    value="sin_iniciar"
                                    class="px-2.5 py-1 rounded-lg transition-colors cursor-pointer {{ $filtroParticipacion === 'sin_iniciar' ? 'bg-amber-100 text-amber-800 font-bold shadow-2xs' : 'text-slate-600 hover:text-amber-700' }}">
                                <i class="fa-regular fa-clock text-[10px]"></i>
                                Sin Iniciar ({{ $estadisticas['sin_iniciar'] }})
                            </button>
                        </div>
                    </div>
                @endif
            </form>
        </div>

        @if ($empleados->isEmpty())
            <div class="p-10 sm:p-14 text-center space-y-3">
                <div class="w-16 h-16 mx-auto rounded-2xl bg-blue-50 text-brand-blue flex items-center justify-center text-2xl shadow-xs">
                    <i class="fa-solid fa-user-group"></i>
                </div>
                <h3 class="font-heading font-extrabold text-base text-brand-dark">No se encontraron participantes</h3>
                <p class="text-xs text-muted max-w-sm mx-auto leading-relaxed">
                    @if ($buscar !== '' || $filtroEstado !== 'todos' || $filtroSede !== null && $filtroSede !== '' || $filtroCapacitacion !== null && $filtroCapacitacion !== '')
                        No hay personal del área que coincida con los filtros seleccionados (Sede, Capacitación o Búsqueda). Intenta restablecer los filtros.
                    @else
                        Aún no hay colaboradores asignados a tu área en el sistema.
                    @endif
                </p>
                @if ($buscar !== '' || $filtroEstado !== 'todos' || $filtroSede !== null && $filtroSede !== '' || $filtroCapacitacion !== null && $filtroCapacitacion !== '')
                    <a href="{{ route('jefe.personal.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-brand-light text-brand-blue font-bold text-xs hover:bg-brand-blue hover:text-white transition-colors">
                        <i class="fa-solid fa-rotate-left"></i>
                        Restablecer filtros
                    </a>
                @endif
            </div>
        @else

            <!-- ========================================================
                 VISTA MÓVIL: CARDS VERTICALES ADAPTADAS (SIN SCROLL HORIZONTAL)
                 ======================================================== -->
            <div class="block md:hidden divide-y divide-line/60">
                @foreach ($empleados as $emp)
                    @php
                        $activo = (bool) $emp->estado;
                        $iniciales = strtoupper(substr($emp->nombre_completo, 0, 1));
                        $sedeEmp = $emp->sedeActual();
                    @endphp
                    <div class="p-4 sm:p-5 space-y-3.5 hover:bg-slate-50/70 transition-colors">
                        
                        <!-- Cabecera de la Card: Avatar, Nombre y Estado -->
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-11 h-11 rounded-2xl {{ $activo ? 'bg-gradient-to-tr from-brand-deep to-brand-cyan text-white shadow-xs' : 'bg-slate-200 text-slate-500' }} font-heading font-extrabold text-sm flex items-center justify-center shrink-0">
                                    {{ $iniciales }}
                                </div>
                                <div class="min-w-0">
                                    <p class="font-heading font-bold text-brand-dark text-sm leading-tight truncate">
                                        {{ $emp->nombre_completo }}
                                    </p>
                                    <p class="text-[11px] text-muted mt-0.5 flex items-center gap-1.5">
                                        <i class="fa-solid fa-id-card text-[10px] text-slate-400"></i>
                                        <span>C.C. {{ $emp->identificacion }}</span>
                                    </p>
                                </div>
                            </div>

                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold shrink-0 {{ $activo ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $activo ? 'bg-emerald-500 animate-pulse' : 'bg-slate-400' }}"></span>
                                {{ $activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </div>

                        <!-- Badge de Sede del Colaborador -->
                        <div class="flex items-center justify-between text-xs pt-0.5">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-purple-50 text-purple-700 font-bold text-[11px] border border-purple-100">
                                <i class="fa-solid fa-location-dot text-[10px]"></i>
                                <span>{{ $sedeEmp?->nombre ?? 'Sin sede asignada' }}</span>
                            </span>

                            @if($emp->certificados_count > 0)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-50 text-amber-800 font-extrabold text-[11px] border border-amber-200">
                                    <i class="fa-solid fa-award text-amber-500"></i>
                                    <span>{{ $emp->certificados_count }} Certificados</span>
                                </span>
                            @endif
                        </div>

                        <!-- Participación en Capacitación Seleccionada o Resumen General -->
                        @if ($capacitacionSeleccionada)
                            @php
                                $estadoPart = $emp->participacion_estado ?? 'PENDIENTE';
                            @endphp
                            <div class="bg-slate-50 p-3 rounded-2xl border border-line/70 space-y-1.5">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-[11px] font-bold text-slate-600 truncate max-w-[200px]" title="{{ $capacitacionSeleccionada->titulo }}">
                                        {{ $capacitacionSeleccionada->titulo }}
                                    </span>
                                    @if($estadoPart === 'COMPLETADA')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-800 font-bold text-[10px]">
                                            <i class="fa-solid fa-circle-check"></i>
                                            Completada
                                        </span>
                                    @elseif(in_array($estadoPart, ['EN_PROGRESO', 'MODULOS_COMPLETOS']))
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-blue-100 text-blue-800 font-bold text-[10px] animate-pulse">
                                            <i class="fa-solid fa-spinner"></i>
                                            En Curso
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-200/80 text-slate-700 font-semibold text-[10px]">
                                            <i class="fa-regular fa-clock"></i>
                                            Sin Iniciar
                                        </span>
                                    @endif
                                </div>
                                <p class="text-[10px] text-muted">
                                    {{ $emp->participacion_fin ? 'Completado el ' . $emp->participacion_fin->format('d/m/Y') : ($emp->participacion_inicio ? 'Iniciado el ' . $emp->participacion_inicio->format('d/m/Y') : 'Pendiente por realizar') }}
                                </p>
                            </div>
                        @else
                            <div class="grid grid-cols-2 gap-2 text-xs">
                                <div class="bg-slate-50 p-2.5 rounded-xl border border-line/70 space-y-1">
                                    <span class="block text-[10px] font-bold uppercase tracking-wider text-muted">Capacitaciones</span>
                                    <div class="flex items-baseline gap-1">
                                        <span class="text-base font-extrabold text-brand-dark">{{ $emp->total_asignaciones_count ?? $emp->asignaciones->count() }}</span>
                                        <span class="text-[10px] text-muted">asignadas</span>
                                    </div>
                                    <div class="flex flex-wrap gap-1 pt-0.5">
                                        <span class="px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-700 text-[10px] font-bold">
                                            {{ $emp->completadas_count }} listos
                                        </span>
                                        <span class="px-1.5 py-0.5 rounded bg-brand-light text-brand-blue text-[10px] font-bold">
                                            {{ $emp->en_progreso_count }} en curso
                                        </span>
                                    </div>
                                </div>

                                <div class="bg-slate-50 p-2.5 rounded-xl border border-line/70 space-y-1">
                                    <span class="block text-[10px] font-bold uppercase tracking-wider text-muted">Cuenta</span>
                                    <div class="pt-1">
                                        @if ($emp->usuario_nuevo)
                                            <span class="inline-flex px-1.5 py-0.5 rounded-md bg-amber-100 text-amber-800 text-[10px] font-bold">
                                                Primer ingreso
                                            </span>
                                        @else
                                            <span class="inline-flex px-1.5 py-0.5 rounded-md bg-slate-200/80 text-slate-700 text-[10px] font-medium">
                                                Clave activa
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-[10px] text-muted pt-1 truncate">
                                        Actividad: {{ $emp->ultima_actividad ? \Illuminate\Support\Carbon::parse($emp->ultima_actividad)->format('d/m/Y') : 'Sin registro' }}
                                    </p>
                                </div>
                            </div>
                        @endif

                        <!-- Botón de detalle -->
                        <div class="flex items-center justify-between pt-1 text-xs">
                            <span class="text-[11px] text-muted">
                                C.C. {{ $emp->identificacion }}
                            </span>

                            <button type="button"
                                    onclick="verDetalleEmpleado({{ json_encode([
                                        'nombre' => $emp->nombre_completo,
                                        'identificacion' => $emp->identificacion,
                                        'sede' => $sedeEmp?->nombre ?? 'Sin sede asignada',
                                        'estado' => $emp->estado ? 'Activo' : 'Inactivo',
                                        'certificados' => $emp->certificados_count,
                                        'asignaciones' => $emp->asignaciones->map(fn($a) => [
                                            'titulo' => $a->capacitacion->titulo ?? 'Capacitación no disponible',
                                            'estado' => $a->estado,
                                            'porcentaje' => $a->porcentaje_calculado ?? 0,
                                            'fecha_inicio' => $a->fecha_inicio ? $a->fecha_inicio->format('d/m/Y') : null,
                                            'fecha_fin' => $a->fecha_finalizacion ? $a->fecha_finalizacion->format('d/m/Y') : null,
                                            'dias' => $a->dias_transcurridos ?? 0,
                                            'duracion_texto' => $a->duracion_texto ?? 'Sin iniciar',
                                            'duracion_detalle' => $a->duracion_detalle ?? 'Aún no ha iniciado',
                                            'modulos_completados' => $a->modulos_completados ?? 0,
                                            'total_modulos' => $a->total_modulos ?? 0,
                                            'diagnostico_titulo' => $a->diagnostico_titulo ?? '',
                                            'diagnostico_mensaje' => $a->diagnostico_mensaje ?? '',
                                            'diagnostico_tipo' => $a->diagnostico_tipo ?? 'neutral',
                                            'diagnostico_icono' => $a->diagnostico_icono ?? 'fa-solid fa-circle-info',
                                        ])
                                    ]) }})"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-brand-light text-brand-blue hover:bg-brand-blue hover:text-white font-heading font-bold text-xs transition-colors cursor-pointer">
                                <i class="fa-solid fa-list-check"></i>
                                <span>Ver cursos y notas</span>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- ========================================================
                 VISTA ESCRITORIO: TABLA TRADICIONAL COMPLETA CON SEDE
                 ======================================================== -->
            <div class="hidden md:block w-full overflow-hidden">
                <table class="w-full table-fixed text-left text-xs border-collapse">
                    <colgroup>
                        <col class="w-[26%]">
                        <col class="w-[15%]">
                        <col class="w-[10%]">
                        <col class="w-[20%]">
                        <col class="w-[9%]">
                        <col class="w-[11%]">
                        <col class="w-[9%]">
                    </colgroup>
                    <thead class="bg-gradient-to-r from-slate-900 via-slate-800 to-indigo-950 text-white">
                        <tr class="text-[10px] sm:text-[11px] font-extrabold uppercase tracking-wider text-slate-200">
                            <th class="px-3 sm:px-4 py-3.5">
                                <div class="flex items-center gap-1.5 truncate">
                                    <i class="fa-solid fa-user-tie text-indigo-400 text-xs shrink-0"></i>
                                    <span class="truncate">Empleado</span>
                                </div>
                            </th>
                            <th class="px-2.5 sm:px-3 py-3.5">
                                <div class="flex items-center gap-1.5 truncate">
                                    <i class="fa-solid fa-location-dot text-rose-400 text-xs shrink-0"></i>
                                    <span class="truncate">Sede</span>
                                </div>
                            </th>
                            <th class="px-2 py-3.5 font-bold text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <i class="fa-solid fa-circle-check text-emerald-400 text-xs shrink-0"></i>
                                    <span>Estado</span>
                                </div>
                            </th>
                            @if($capacitacionSeleccionada)
                                <th class="px-2.5 sm:px-3 py-3.5">
                                    <div class="flex items-center gap-1.5 truncate">
                                        <i class="fa-solid fa-graduation-cap text-sky-400 text-xs shrink-0"></i>
                                        <span class="truncate">Participación</span>
                                    </div>
                                </th>
                            @else
                                <th class="px-2.5 sm:px-3 py-3.5">
                                    <div class="flex items-center gap-1.5 truncate">
                                        <i class="fa-solid fa-graduation-cap text-sky-400 text-xs shrink-0"></i>
                                        <span class="truncate">Capacitaciones</span>
                                    </div>
                                </th>
                            @endif
                            <th class="px-2 py-3.5 font-bold text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <i class="fa-solid fa-award text-amber-300 text-xs shrink-0"></i>
                                    <span>Certif.</span>
                                </div>
                            </th>
                            <th class="px-2.5 sm:px-3 py-3.5">
                                <div class="flex items-center gap-1.5 truncate">
                                    <i class="fa-regular fa-clock text-cyan-400 text-xs shrink-0"></i>
                                    <span class="truncate">Última Act.</span>
                                </div>
                            </th>
                            <th class="px-3 sm:px-4 py-3.5 font-bold text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <i class="fa-solid fa-gears text-slate-300 text-xs shrink-0"></i>
                                    <span>Detalle</span>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200/80 text-slate-700 bg-white">
                        @foreach ($empleados as $emp)
                            @php
                                $activo = (bool) $emp->estado;
                                $iniciales = strtoupper(substr($emp->nombre_completo, 0, 1));
                                $sedeEmp = $emp->sedeActual();
                            @endphp
                            <tr class="hover:bg-sky-50/50 even:bg-slate-50/40 transition-colors">
                                
                                <!-- 1. Empleado -->
                                <td class="px-3 sm:px-4 py-3.5">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <div class="w-8 h-8 rounded-xl {{ $activo ? 'bg-gradient-to-tr from-brand-deep to-brand-cyan text-white shadow-2xs' : 'bg-slate-200 text-slate-500' }} font-heading font-extrabold text-xs flex items-center justify-center shrink-0">
                                            {{ $iniciales }}
                                        </div>
                                        <div class="min-w-0 truncate">
                                            <p class="font-heading font-bold text-brand-dark truncate leading-tight text-xs" title="{{ $emp->nombre_completo }}">{{ $emp->nombre_completo }}</p>
                                            <p class="text-[11px] text-muted truncate mt-0.5">C.C. {{ $emp->identificacion }}</p>
                                        </div>
                                    </div>
                                </td>

                                <!-- 2. Sede Institucional -->
                                <td class="px-2.5 sm:px-3 py-3.5">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-purple-50 text-purple-700 font-bold text-[10.5px] border border-purple-100 max-w-full truncate" title="{{ $sedeEmp?->nombre ?? 'Sin sede asignada' }}">
                                        <i class="fa-solid fa-location-dot text-purple-500 text-[9px] shrink-0"></i>
                                        <span class="truncate">{{ $sedeEmp?->nombre ?? 'Sin sede' }}</span>
                                    </span>
                                </td>

                                <!-- 3. Estado -->
                                <td class="px-2 py-3.5 text-center">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10.5px] font-bold {{ $activo ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $activo ? 'bg-emerald-500 animate-pulse' : 'bg-slate-400' }}"></span>
                                        <span>{{ $activo ? 'Activo' : 'Inactivo' }}</span>
                                    </span>
                                </td>

                                <!-- 4. Participación (específica o global) -->
                                @if($capacitacionSeleccionada)
                                    @php
                                        $estadoPart = $emp->participacion_estado ?? 'PENDIENTE';
                                    @endphp
                                    <td class="px-2.5 sm:px-3 py-3.5">
                                        <div class="space-y-0.5">
                                            @if($estadoPart === 'COMPLETADA')
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-extrabold text-[10.5px] border border-emerald-200">
                                                    <i class="fa-solid fa-circle-check text-[9px]"></i>
                                                    <span>Completada</span>
                                                </span>
                                            @elseif(in_array($estadoPart, ['EN_PROGRESO', 'MODULOS_COMPLETOS']))
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 font-extrabold text-[10.5px] border border-blue-200 animate-pulse">
                                                    <i class="fa-solid fa-spinner text-[9px]"></i>
                                                    <span>En Curso</span>
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 font-semibold text-[10.5px] border border-slate-200">
                                                    <i class="fa-regular fa-clock text-[9px]"></i>
                                                    <span>Sin Iniciar</span>
                                                </span>
                                            @endif
                                            <p class="text-[9.5px] text-muted truncate">
                                                {{ $emp->participacion_fin ? 'Fin: ' . $emp->participacion_fin->format('d/m/Y') : ($emp->participacion_inicio ? 'Ini: ' . $emp->participacion_inicio->format('d/m/Y') : 'Pendiente') }}
                                            </p>
                                        </div>
                                    </td>
                                @else
                                    <td class="px-2.5 sm:px-3 py-3.5">
                                        <div class="space-y-0.5">
                                            <div class="flex items-center gap-1 text-[11px]">
                                                <span class="font-extrabold text-brand-dark">{{ $emp->total_asignaciones_count ?? $emp->asignaciones->count() }}</span>
                                                <span class="text-[10px] text-muted">asig.</span>
                                            </div>
                                            <div class="flex items-center gap-1 text-[9.5px] font-bold">
                                                <span class="px-1.5 py-0.2 rounded bg-emerald-100 text-emerald-700" title="Completadas">
                                                    {{ $emp->completadas_count }} listos
                                                </span>
                                                <span class="px-1.5 py-0.2 rounded bg-brand-light text-brand-blue" title="En progreso">
                                                    {{ $emp->en_progreso_count }} curso
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                @endif

                                <!-- 5. Certificados -->
                                <td class="px-2 py-3.5 text-center">
                                    @if ($emp->certificados_count > 0)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-50 border border-amber-200 text-amber-900 font-extrabold text-[11px]">
                                            <i class="fa-solid fa-award text-amber-500 text-[10px]"></i>
                                            <span>{{ $emp->certificados_count }}</span>
                                        </span>
                                    @else
                                        <span class="text-slate-400 font-medium text-xs">—</span>
                                    @endif
                                </td>

                                <!-- 6. Última Actividad -->
                                <td class="px-2.5 sm:px-3 py-3.5 text-muted">
                                    @if($emp->ultima_actividad)
                                        <p class="font-medium text-slate-700 text-[11px] leading-tight">{{ \Illuminate\Support\Carbon::parse($emp->ultima_actividad)->format('d/m/Y') }}</p>
                                        <p class="text-[10px] text-slate-400">{{ \Illuminate\Support\Carbon::parse($emp->ultima_actividad)->format('H:i') }}</p>
                                    @else
                                        <span class="text-slate-400 text-[10.5px]">Sin actividad</span>
                                    @endif
                                </td>

                                <!-- 7. Acciones -->
                                <td class="px-3 sm:px-4 py-3.5 text-right">
                                    <button type="button"
                                            onclick="verDetalleEmpleado({{ json_encode([
                                                'nombre' => $emp->nombre_completo,
                                                'identificacion' => $emp->identificacion,
                                                'sede' => $sedeEmp?->nombre ?? 'Sin sede asignada',
                                                'estado' => $emp->estado ? 'Activo' : 'Inactivo',
                                                'certificados' => $emp->certificados_count,
                                                'asignaciones' => $emp->asignaciones->map(fn($a) => [
                                                    'titulo' => $a->capacitacion->titulo ?? 'Capacitación no disponible',
                                                    'estado' => $a->estado,
                                                    'porcentaje' => $a->porcentaje_calculado ?? 0,
                                                    'fecha_inicio' => $a->fecha_inicio ? $a->fecha_inicio->format('d/m/Y') : null,
                                                    'fecha_fin' => $a->fecha_finalizacion ? $a->fecha_finalizacion->format('d/m/Y') : null,
                                                    'dias' => $a->dias_transcurridos ?? 0,
                                                    'duracion_texto' => $a->duracion_texto ?? 'Sin iniciar',
                                                    'duracion_detalle' => $a->duracion_detalle ?? 'Aún no ha iniciado',
                                                    'modulos_completados' => $a->modulos_completados ?? 0,
                                                    'total_modulos' => $a->total_modulos ?? 0,
                                                    'diagnostico_titulo' => $a->diagnostico_titulo ?? '',
                                                    'diagnostico_mensaje' => $a->diagnostico_mensaje ?? '',
                                                    'diagnostico_tipo' => $a->diagnostico_tipo ?? 'neutral',
                                                    'diagnostico_icono' => $a->diagnostico_icono ?? 'fa-solid fa-circle-info',
                                                ])
                                            ]) }})"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-brand-blue hover:text-white text-brand-blue font-heading font-bold text-xs transition-colors cursor-pointer shadow-2xs">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                        <span>Detalle</span>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        @endif
    </section>

</div>

<!-- ========================================================
     MODAL DE DETALLE DE CURSOS Y SEDE DEL EMPLEADO
     ======================================================== -->
<div id="modal-detalle-empleado" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-empleado-titulo" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-brand-dark/60 backdrop-blur-xs transition-opacity" onclick="cerrarDetalleEmpleado()"></div>

    <div class="flex min-h-screen items-center justify-center p-3 sm:p-6 text-center">
        <div class="relative w-full max-w-2xl rounded-3xl bg-white text-left shadow-2xl transition-all border border-line overflow-hidden flex flex-col max-h-[90vh]">
            
            <!-- Encabezado del Modal -->
            <div class="bg-gradient-to-r from-brand-dark via-brand-deep to-brand-blue p-5 text-white flex items-center justify-between shrink-0">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 rounded-xl bg-white/10 backdrop-blur-md flex items-center justify-center text-lg text-brand-sky shrink-0">
                        <i class="fa-solid fa-user-graduate"></i>
                    </div>
                    <div class="min-w-0">
                        <h3 id="modal-emp-nombre" class="font-heading font-extrabold text-base leading-tight text-white truncate">
                            Nombre del Empleado
                        </h3>
                        <p id="modal-emp-cc" class="text-xs text-brand-sky mt-0.5 truncate">C.C. — · Sede —</p>
                    </div>
                </div>
                <button type="button" onclick="cerrarDetalleEmpleado()" class="p-2 rounded-xl text-white/80 hover:text-white hover:bg-white/10 transition-colors cursor-pointer shrink-0">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Cuerpo del Modal -->
            <div class="p-5 overflow-y-auto space-y-4">
                <div class="flex items-center justify-between pb-2 border-b border-line/60 text-xs">
                    <span class="font-bold text-slate-700 flex items-center gap-1.5">
                        <i class="fa-solid fa-book-open text-brand-blue"></i>
                        <span>Capacitaciones y Progreso Detallado</span>
                    </span>
                    <span id="modal-emp-total-cursos" class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 font-extrabold text-[11px]">0 cursos</span>
                </div>

                <div id="modal-emp-lista-cursos" class="space-y-3">
                    <!-- Se llena dinámicamente con JS -->
                </div>
            </div>

            <!-- Footer del Modal -->
            <div class="p-4 bg-slate-50 border-t border-line/60 flex items-center justify-between">
                <span class="text-[11px] text-muted flex items-center gap-1">
                    <i class="fa-solid fa-circle-info text-sky-500"></i>
                    <span>Tiempos calculados automáticamente desde la fecha de inicio hasta hoy.</span>
                </span>
                <button type="button" onclick="cerrarDetalleEmpleado()" class="px-4 py-2 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 font-heading font-bold text-xs transition-colors cursor-pointer shadow-2xs">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const estadosClases = {
        'COMPLETADA': 'bg-emerald-100 text-emerald-800 border-emerald-200',
        'MODULOS_COMPLETOS': 'bg-teal-100 text-teal-800 border-teal-200',
        'EN_PROGRESO': 'bg-blue-100 text-blue-800 border-blue-200',
        'PENDIENTE': 'bg-slate-100 text-slate-700 border-slate-200',
        'NO_APROBADA': 'bg-rose-100 text-rose-800 border-rose-200'
    };

    const estadosEtiquetas = {
        'COMPLETADA': 'Completada',
        'MODULOS_COMPLETOS': 'Módulos Listos',
        'EN_PROGRESO': 'En Progreso',
        'PENDIENTE': 'Sin Iniciar',
        'NO_APROBADA': 'No Aprobada'
    };

    function verDetalleEmpleado(data) {
        document.getElementById('modal-emp-nombre').textContent = data.nombre;
        document.getElementById('modal-emp-cc').textContent = 'C.C. ' + data.identificacion + ' · Sede: ' + data.sede + ' · ' + data.estado;
        document.getElementById('modal-emp-total-cursos').textContent = data.asignaciones.length + (data.asignaciones.length === 1 ? ' capacitación' : ' capacitaciones');

        const contenedor = document.getElementById('modal-emp-lista-cursos');
        contenedor.innerHTML = '';

        if (!data.asignaciones || data.asignaciones.length === 0) {
            contenedor.innerHTML = `
                <div class="p-8 text-center text-muted space-y-2">
                    <i class="fa-solid fa-graduation-cap text-3xl text-slate-300 block"></i>
                    <p class="text-xs">Este colaborador aún no tiene capacitaciones asignadas en el área.</p>
                </div>
            `;
        } else {
            data.asignaciones.forEach(c => {
                const badgeClase = estadosClases[c.estado] || 'bg-slate-100 text-slate-700 border-slate-200';
                const badgeTexto = estadosEtiquetas[c.estado] || c.estado;
                const porcentaje = parseInt(c.porcentaje, 10) || 0;

                // Colores dinámicos según el avance
                let barraColor = 'bg-brand-blue';
                let porcentajeColor = 'text-brand-blue';
                if (c.estado === 'COMPLETADA' || porcentaje >= 100) {
                    barraColor = 'bg-emerald-500';
                    porcentajeColor = 'text-emerald-700';
                } else if (porcentaje === 0) {
                    barraColor = 'bg-slate-300';
                    porcentajeColor = 'text-slate-500';
                } else if (porcentaje < 40) {
                    barraColor = 'bg-amber-500';
                    porcentajeColor = 'text-amber-700';
                }

                // Estilos para el diagnóstico automático
                let diagBg = 'bg-slate-50';
                let diagBorder = 'border-slate-200';
                let diagTitleColor = 'text-slate-800';
                let diagTextColor = 'text-slate-600';
                let diagBadgeBg = 'bg-slate-100 text-slate-700 border-slate-200';

                if (c.diagnostico_tipo === 'danger') {
                    diagBg = 'bg-rose-50/90';
                    diagBorder = 'border-rose-200';
                    diagTitleColor = 'text-rose-900';
                    diagTextColor = 'text-rose-800';
                    diagBadgeBg = 'bg-rose-100 text-rose-800 border-rose-300';
                } else if (c.diagnostico_tipo === 'warning') {
                    diagBg = 'bg-amber-50/90';
                    diagBorder = 'border-amber-200';
                    diagTitleColor = 'text-amber-900';
                    diagTextColor = 'text-amber-800';
                    diagBadgeBg = 'bg-amber-100 text-amber-800 border-amber-300';
                } else if (c.diagnostico_tipo === 'success') {
                    diagBg = 'bg-emerald-50/90';
                    diagBorder = 'border-emerald-200';
                    diagTitleColor = 'text-emerald-900';
                    diagTextColor = 'text-emerald-800';
                    diagBadgeBg = 'bg-emerald-100 text-emerald-800 border-emerald-300';
                } else if (c.diagnostico_tipo === 'info') {
                    diagBg = 'bg-blue-50/90';
                    diagBorder = 'border-blue-200';
                    diagTitleColor = 'text-brand-blue';
                    diagTextColor = 'text-slate-700';
                    diagBadgeBg = 'bg-blue-100 text-brand-blue border-blue-300';
                }

                const item = document.createElement('div');
                item.className = 'p-4 rounded-2xl border border-slate-200/90 bg-white hover:border-brand-blue/30 shadow-2xs transition-all space-y-3';
                item.innerHTML = `
                    <!-- Encabezado del curso: Título y Estado -->
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <h4 class="font-heading font-extrabold text-xs sm:text-sm text-brand-dark leading-snug">
                                ${c.titulo}
                            </h4>
                            <div class="flex items-center flex-wrap gap-2 mt-1">
                                ${c.total_modulos > 0 ? `
                                    <span class="inline-flex items-center gap-1 text-[10.5px] text-muted font-semibold bg-slate-100 px-2 py-0.5 rounded-md">
                                        <i class="fa-solid fa-layer-group text-slate-400 text-[10px]"></i>
                                        <span>${c.modulos_completados} de ${c.total_modulos} módulos listos</span>
                                    </span>
                                ` : ''}
                                ${c.fecha_fin ? `
                                    <span class="inline-flex items-center gap-1 text-[10.5px] text-emerald-700 font-semibold bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-100">
                                        <i class="fa-solid fa-circle-check text-[10px]"></i>
                                        <span>Finalizado el ${c.fecha_fin}</span>
                                    </span>
                                ` : ''}
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10.5px] font-bold border shrink-0 ${badgeClase}">
                            ${badgeTexto}
                        </span>
                    </div>

                    <!-- Indicador de Avance / Porcentaje de Completado -->
                    <div class="space-y-1.5 bg-slate-50/90 p-3 rounded-xl border border-slate-100">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-[11px] font-bold text-slate-700 flex items-center gap-1.5">
                                <i class="fa-solid fa-chart-pie text-brand-blue"></i>
                                <span>Porcentaje de Completado</span>
                            </span>
                            <span class="font-heading font-black text-xs sm:text-sm ${porcentajeColor}">
                                ${porcentaje}%
                            </span>
                        </div>
                        <div class="w-full bg-slate-200/90 rounded-full h-2.5 overflow-hidden">
                            <div class="h-full rounded-full ${barraColor} transition-all duration-500" style="width: ${porcentaje}%"></div>
                        </div>
                    </div>

                    <!-- Duración (solo si no se ha completado) o Estado de Finalización -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs pt-2 border-t border-slate-100">
                        ${c.estado === 'COMPLETADA' ? `
                            <div class="flex items-center gap-2 min-w-0">
                                <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs shrink-0 border border-emerald-100">
                                    <i class="fa-solid fa-circle-check"></i>
                                </div>
                                <div class="min-w-0">
                                    <span class="text-[10px] text-muted font-bold uppercase tracking-wider block">
                                        Estado de Finalización
                                    </span>
                                    <span class="text-[11px] font-bold text-emerald-800 block truncate">
                                        ${c.fecha_fin ? 'Completada el ' + c.fecha_fin : 'Capacitación Finalizada'}
                                    </span>
                                </div>
                            </div>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 font-extrabold text-[11px] border border-emerald-200/80 shrink-0 self-start sm:self-auto">
                                <i class="fa-solid fa-award text-[10px] text-amber-500"></i>
                                <span>Finalizada</span>
                            </span>
                        ` : `
                            <div class="flex items-center gap-2 min-w-0">
                                <div class="w-7 h-7 rounded-lg bg-sky-50 text-brand-blue flex items-center justify-center text-xs shrink-0 border border-sky-100">
                                    <i class="fa-regular fa-clock"></i>
                                </div>
                                <div class="min-w-0">
                                    <span class="text-[10px] text-muted font-bold uppercase tracking-wider block">
                                        Duración desde inicio hasta hoy
                                    </span>
                                    <span class="text-[11px] font-bold text-brand-dark block truncate">
                                        ${c.duracion_detalle}
                                    </span>
                                </div>
                            </div>

                            ${c.fecha_inicio ? `
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-blue-50 text-brand-blue font-mono font-extrabold text-[11px] border border-blue-200/60 shrink-0 self-start sm:self-auto">
                                    <i class="fa-solid fa-stopwatch text-[10px]"></i>
                                    <span>${c.duracion_texto}</span>
                                </span>
                            ` : `
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 text-slate-500 font-semibold text-[10.5px] shrink-0 self-start sm:self-auto">
                                    <i class="fa-regular fa-calendar-xmark text-[10px]"></i>
                                    <span>Sin iniciar</span>
                                </span>
                            `}
                        `}
                    </div>

                    <!-- Diagnóstico Contextual Inteligente -->
                    ${c.diagnostico_mensaje ? `
                        <div class="p-3 rounded-xl border ${diagBg} ${diagBorder} space-y-1">
                            <div class="flex items-center justify-between gap-2">
                                <span class="inline-flex items-center gap-1.5 font-heading font-extrabold text-[11px] ${diagTitleColor}">
                                    <i class="${c.diagnostico_icono || 'fa-solid fa-circle-info'} text-xs shrink-0"></i>
                                    <span>Diagnóstico: ${c.diagnostico_titulo || 'Estado de Capacitación'}</span>
                                </span>
                                <span class="inline-flex px-2 py-0.5 rounded-full text-[9.5px] font-extrabold border ${diagBadgeBg} shrink-0">
                                    Seguimiento
                                </span>
                            </div>
                            <p class="text-[11px] ${diagTextColor} leading-relaxed pl-5 font-medium">
                                ${c.diagnostico_mensaje}
                            </p>
                        </div>
                    ` : ''}
                `;
                contenedor.appendChild(item);
            });
        }

        const modal = document.getElementById('modal-detalle-empleado');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function cerrarDetalleEmpleado() {
        const modal = document.getElementById('modal-detalle-empleado');
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }
</script>
@endpush
