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
    <section class="bg-white rounded-3xl border border-line/80 shadow-2xs overflow-hidden" aria-labelledby="titulo-personal">
        
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
                                            'fecha_inicio' => $a->fecha_inicio ? $a->fecha_inicio->format('d/m/Y') : null,
                                            'fecha_fin' => $a->fecha_finalizacion ? $a->fecha_finalizacion->format('d/m/Y') : null,
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
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-muted border-b border-line/60">
                        <tr>
                            <th class="px-5 sm:px-6 py-3.5 font-bold">Empleado</th>
                            <th class="px-4 py-3.5 font-bold">Sede Institucional</th>
                            <th class="px-4 py-3.5 font-bold text-center">Estado</th>
                            @if($capacitacionSeleccionada)
                                <th class="px-4 py-3.5 font-bold">Participación en Curso</th>
                            @else
                                <th class="px-4 py-3.5 font-bold">Capacitaciones de Área</th>
                            @endif
                            <th class="px-4 py-3.5 font-bold text-center">Certificados</th>
                            <th class="px-4 py-3.5 font-bold">Última Actividad</th>
                            <th class="px-5 py-3.5 font-bold text-right">Detalle</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line/50">
                        @foreach ($empleados as $emp)
                            @php
                                $activo = (bool) $emp->estado;
                                $iniciales = strtoupper(substr($emp->nombre_completo, 0, 1));
                                $sedeEmp = $emp->sedeActual();
                            @endphp
                            <tr class="hover:bg-brand-light/35 transition-colors">
                                
                                <!-- 1. Empleado -->
                                <td class="px-5 sm:px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl {{ $activo ? 'bg-gradient-to-tr from-brand-deep to-brand-cyan text-white shadow-2xs' : 'bg-slate-200 text-slate-500' }} font-heading font-extrabold text-xs flex items-center justify-center shrink-0">
                                            {{ $iniciales }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-heading font-bold text-brand-dark truncate leading-tight">{{ $emp->nombre_completo }}</p>
                                            <p class="text-[11px] text-muted mt-0.5">C.C. {{ $emp->identificacion }}</p>
                                        </div>
                                    </div>
                                </td>

                                <!-- 2. Sede Institucional -->
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-purple-50 text-purple-700 font-bold text-[11px] border border-purple-100">
                                        <i class="fa-solid fa-location-dot text-purple-500 text-[10px]"></i>
                                        <span>{{ $sedeEmp?->nombre ?? 'Sin sede asignada' }}</span>
                                    </span>
                                </td>

                                <!-- 3. Estado -->
                                <td class="px-4 py-4 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold {{ $activo ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $activo ? 'bg-emerald-500 animate-pulse' : 'bg-slate-400' }}"></span>
                                        {{ $activo ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>

                                <!-- 4. Participación (específica o global) -->
                                @if($capacitacionSeleccionada)
                                    @php
                                        $estadoPart = $emp->participacion_estado ?? 'PENDIENTE';
                                    @endphp
                                    <td class="px-4 py-4">
                                        <div class="space-y-1">
                                            @if($estadoPart === 'COMPLETADA')
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-extrabold text-[11px] border border-emerald-200">
                                                    <i class="fa-solid fa-circle-check text-[10px]"></i>
                                                    Completada
                                                </span>
                                            @elseif(in_array($estadoPart, ['EN_PROGRESO', 'MODULOS_COMPLETOS']))
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-800 font-extrabold text-[11px] border border-blue-200 animate-pulse">
                                                    <i class="fa-solid fa-spinner text-[10px]"></i>
                                                    En Curso
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 font-semibold text-[11px] border border-slate-200">
                                                    <i class="fa-regular fa-clock text-[10px]"></i>
                                                    Sin Iniciar
                                                </span>
                                            @endif
                                            <p class="text-[10px] text-muted">
                                                {{ $emp->participacion_fin ? 'Aprobado: ' . $emp->participacion_fin->format('d/m/Y') : ($emp->participacion_inicio ? 'Iniciado: ' . $emp->participacion_inicio->format('d/m/Y') : 'Pendiente de inicio') }}
                                            </p>
                                        </div>
                                    </td>
                                @else
                                    <td class="px-4 py-4">
                                        <div class="space-y-1">
                                            <div class="flex items-center gap-1.5">
                                                <span class="font-extrabold text-brand-dark">{{ $emp->total_asignaciones_count ?? $emp->asignaciones->count() }}</span>
                                                <span class="text-[11px] text-muted">asignadas</span>
                                            </div>
                                            <div class="flex items-center gap-1.5 text-[10px] font-bold">
                                                <span class="px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-700" title="Completadas">
                                                    {{ $emp->completadas_count }} completadas
                                                </span>
                                                <span class="px-1.5 py-0.5 rounded bg-brand-light text-brand-blue" title="En progreso">
                                                    {{ $emp->en_progreso_count }} en curso
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                @endif

                                <!-- 5. Certificados -->
                                <td class="px-4 py-4 text-center whitespace-nowrap">
                                    @if ($emp->certificados_count > 0)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-amber-50 border border-amber-200 text-amber-900 font-extrabold text-xs">
                                            <i class="fa-solid fa-award text-amber-500"></i>
                                            <span>{{ $emp->certificados_count }}</span>
                                        </span>
                                    @else
                                        <span class="text-slate-400 font-medium">—</span>
                                    @endif
                                </td>

                                <!-- 6. Última Actividad -->
                                <td class="px-4 py-4 text-muted whitespace-nowrap">
                                    {{ $emp->ultima_actividad ? \Illuminate\Support\Carbon::parse($emp->ultima_actividad)->format('d/m/Y H:i') : 'Sin actividad' }}
                                </td>

                                <!-- 7. Acciones -->
                                <td class="px-5 sm:px-6 py-4 text-right whitespace-nowrap">
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
                                                    'fecha_inicio' => $a->fecha_inicio ? $a->fecha_inicio->format('d/m/Y') : null,
                                                    'fecha_fin' => $a->fecha_finalizacion ? $a->fecha_finalizacion->format('d/m/Y') : null,
                                                ])
                                            ]) }})"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-brand-blue hover:text-white text-slate-700 font-heading font-bold text-xs transition-colors cursor-pointer">
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
        <div class="relative w-full max-w-lg rounded-3xl bg-white text-left shadow-2xl transition-all border border-line overflow-hidden flex flex-col max-h-[90vh]">
            
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
                        <p id="modal-emp-cc" class="text-xs text-brand-sky mt-0.5">C.C. — · Sede —</p>
                    </div>
                </div>
                <button type="button" onclick="cerrarDetalleEmpleado()" class="p-2 rounded-xl text-white/80 hover:text-white hover:bg-white/10 transition-colors cursor-pointer">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Cuerpo del Modal -->
            <div class="p-5 overflow-y-auto space-y-4">
                <div class="flex items-center justify-between pb-2 border-b border-line/60 text-xs">
                    <span class="font-bold text-slate-700">Participación en capacitaciones</span>
                    <span id="modal-emp-total-cursos" class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 font-extrabold text-[11px]">0 cursos</span>
                </div>

                <div id="modal-emp-lista-cursos" class="space-y-2.5">
                    <!-- Se llena dinámicamente con JS -->
                </div>
            </div>

            <!-- Footer del Modal -->
            <div class="p-4 bg-slate-50 border-t border-line/60 flex justify-end">
                <button type="button" onclick="cerrarDetalleEmpleado()" class="px-4 py-2 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 font-heading font-bold text-xs transition-colors cursor-pointer">
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
        document.getElementById('modal-emp-cc').textContent = 'C.C. ' + data.identificacion + ' · ' + data.sede + ' · ' + data.estado;
        document.getElementById('modal-emp-total-cursos').textContent = data.asignaciones.length + (data.asignaciones.length === 1 ? ' capacitación' : ' capacitaciones');

        const contenedor = document.getElementById('modal-emp-lista-cursos');
        contenedor.innerHTML = '';

        if (!data.asignaciones || data.asignaciones.length === 0) {
            contenedor.innerHTML = '<p class="text-xs text-muted text-center py-6">Este colaborador aún no tiene capacitaciones asignadas en el área.</p>';
        } else {
            data.asignaciones.forEach(c => {
                const badgeClase = estadosClases[c.estado] || 'bg-slate-100 text-slate-700 border-slate-200';
                const badgeTexto = estadosEtiquetas[c.estado] || c.estado;
                
                const item = document.createElement('div');
                item.className = 'p-3 rounded-2xl border border-line bg-slate-50/70 hover:bg-slate-50 transition-colors flex items-center justify-between gap-3';
                item.innerHTML = `
                    <div class="min-w-0">
                        <p class="font-bold text-xs text-brand-dark leading-tight">${c.titulo}</p>
                        <p class="text-[10px] text-muted mt-0.5">
                            ${c.fecha_inicio ? 'Iniciado: ' + c.fecha_inicio : 'No iniciado'}
                            ${c.fecha_fin ? ' · Finalizado: ' + c.fecha_fin : ''}
                        </p>
                    </div>
                    <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold border shrink-0 ${badgeClase}">
                        ${badgeTexto}
                    </span>
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
