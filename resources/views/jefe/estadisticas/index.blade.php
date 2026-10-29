@extends('layouts.jefe')

@section('title', 'Estadísticas del Área · C.I. Piscícola New York')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    <!-- BANNER DE BIENVENIDA Y RESUMEN DEL ÁREA -->
    <div class="relative bg-gradient-to-br from-brand-dark via-brand-deep to-brand-blue rounded-3xl p-6 sm:p-8 text-white shadow-lg overflow-hidden">
        <div class="absolute -right-8 -bottom-8 w-48 h-48 rounded-full bg-white/5 pointer-events-none"></div>
        <div class="absolute right-20 -top-10 w-36 h-36 rounded-full bg-brand-sky/15 pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="max-w-2xl space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-white/10 text-cyan-200 border border-white/15">
                    <i class="fa-solid fa-chart-pie"></i>
                    <span>Analítica de Formación · Área: {{ $areaNombre }}</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight font-serif">
                    Estadísticas de Capacitaciones e Inducciones
                </h1>
                <p class="text-xs sm:text-sm text-slate-200 leading-relaxed">
                    Monitorea en tiempo real el progreso formativo, aprobación de evaluaciones, participación en inducciones y cumplimiento de los colaboradores de tu área distribuidos en las diferentes sedes operativas.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                <a href="{{ route('jefe.personal.index') }}"
                   class="px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs tracking-wider uppercase transition-all border border-white/20 flex items-center gap-2">
                    <i class="fa-solid fa-users"></i>
                    <span>Ver Mi Equipo</span>
                </a>
                <a href="{{ route('jefe.inducciones.index') }}"
                   class="px-4 py-2.5 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-bold text-xs tracking-wider uppercase transition-all shadow-md flex items-center gap-2">
                    <i class="fa-solid fa-chalkboard-user"></i>
                    <span>Portal Inducciones</span>
                </a>
            </div>
        </div>
    </div>

    <!-- TARJETAS KPIS PRINCIPALES -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- KPI 1: Colaboradores -->
        <div class="bg-white p-5 rounded-3xl border border-line shadow-2xs hover:shadow-md transition-all flex flex-col justify-between">
            <div class="flex items-center justify-between gap-3">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Colaboradores</span>
                <span class="w-10 h-10 rounded-2xl bg-blue-50 text-brand-blue flex items-center justify-center text-base shrink-0">
                    <i class="fa-solid fa-users"></i>
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl sm:text-3xl font-black text-brand-dark tracking-tight">{{ $kpis['colaboradores_total'] }}</div>
                <div class="text-[11px] text-slate-500 mt-0.5 flex items-center gap-1">
                    <i class="fa-solid fa-building text-[9px] text-slate-400"></i>
                    <span>En <strong>{{ $kpis['sedes_con_presencia'] }}</strong> {{ $kpis['sedes_con_presencia'] === 1 ? 'sede' : 'sedes' }}</span>
                </div>
            </div>
        </div>

        <!-- KPI 2: Avance Capacitación -->
        <div class="bg-white p-5 rounded-3xl border border-line shadow-2xs hover:shadow-md transition-all flex flex-col justify-between">
            <div class="flex items-center justify-between gap-3">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Avance Global</span>
                <span class="w-10 h-10 rounded-2xl bg-cyan-50 text-cyan-700 flex items-center justify-center text-base shrink-0">
                    <i class="fa-solid fa-chart-line"></i>
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl sm:text-3xl font-black text-brand-dark tracking-tight">{{ $kpis['promedio_avance'] }}%</div>
                <div class="text-[11px] text-slate-500 mt-0.5 flex items-center gap-1">
                    <i class="fa-solid fa-circle-check text-[9px] text-emerald-500"></i>
                    <span><strong>{{ $kpis['completadas'] }}</strong> de {{ $kpis['total_asignaciones'] }} completadas</span>
                </div>
            </div>
        </div>

        <!-- KPI 3: Aprobación Evaluaciones -->
        <div class="bg-white p-5 rounded-3xl border border-line shadow-2xs hover:shadow-md transition-all flex flex-col justify-between">
            <div class="flex items-center justify-between gap-3">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Aprobación</span>
                <span class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-base shrink-0">
                    <i class="fa-solid fa-clipboard-check"></i>
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl sm:text-3xl font-black text-brand-dark tracking-tight">{{ $kpis['tasa_aprobacion'] }}%</div>
                <div class="text-[11px] text-slate-500 mt-0.5 flex items-center gap-1">
                    <i class="fa-solid fa-star text-[9px] text-amber-500"></i>
                    <span>Nota prom: <strong>{{ $kpis['promedio_calificacion'] }}%</strong> ({{ $kpis['intentos_total'] }} int.)</span>
                </div>
            </div>
        </div>

        <!-- KPI 4: Inducciones -->
        <div class="bg-white p-5 rounded-3xl border border-line shadow-2xs hover:shadow-md transition-all flex flex-col justify-between">
            <div class="flex items-center justify-between gap-3">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Inducciones</span>
                <span class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-700 flex items-center justify-center text-base shrink-0">
                    <i class="fa-solid fa-file-signature"></i>
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl sm:text-3xl font-black text-brand-dark tracking-tight">{{ $kpis['inducciones_asistencias'] }}</div>
                <div class="text-[11px] text-slate-500 mt-0.5 flex items-center gap-1">
                    <i class="fa-solid fa-signature text-[9px] text-cyan-600"></i>
                    <span><strong>{{ $kpis['inducciones_firmas'] }}</strong> firmas en {{ $kpis['inducciones_sesiones'] }} sesiones</span>
                </div>
            </div>
        </div>

        <!-- KPI 5: Certificados -->
        <div class="bg-white p-5 rounded-3xl border border-line shadow-2xs hover:shadow-md transition-all flex flex-col justify-between">
            <div class="flex items-center justify-between gap-3">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Certificados</span>
                <span class="w-10 h-10 rounded-2xl bg-purple-50 text-purple-700 flex items-center justify-center text-base shrink-0">
                    <i class="fa-solid fa-award"></i>
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl sm:text-3xl font-black text-brand-dark tracking-tight">{{ $kpis['certificados_count'] }}</div>
                <div class="text-[11px] text-slate-500 mt-0.5 flex items-center gap-1">
                    <i class="fa-solid fa-stamp text-[9px] text-purple-500"></i>
                    <span>Diplomas emitidos en tu área</span>
                </div>
            </div>
        </div>
    </div>

    <!-- SECCIÓN DE GRÁFICOS INTERACTIVOS (CHART.JS) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Gráfico 1: Desempeño por Sede (2 columnas) -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-line p-5 sm:p-6 shadow-2xs space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-line pb-3">
                <div>
                    <h3 class="text-base font-bold text-brand-dark flex items-center gap-2">
                        <i class="fa-solid fa-chart-column text-brand-blue"></i>
                        <span>Desempeño y Cumplimiento por Sede Operativa</span>
                    </h3>
                    <p class="text-xs text-slate-500">
                        Comparativa de avance de capacitación, tasa de cumplimiento y asistencias a inducción de tu área según la sede.
                    </p>
                </div>
                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-slate-600 bg-slate-100 px-3 py-1 rounded-full self-start sm:self-center">
                    <i class="fa-solid fa-layer-group text-slate-400"></i>
                    <span>{{ $porSede->count() }} Sedes Activas</span>
                </span>
            </div>

            <!-- Canvas Chart Sede -->
            <div class="relative w-full h-72 sm:h-80 pt-2">
                <canvas id="chartDesempenoSedes"></canvas>
            </div>
        </div>

        <!-- Gráfico 2: Distribución de Estados de Capacitación (1 columna) -->
        <div class="bg-white rounded-3xl border border-line p-5 sm:p-6 shadow-2xs space-y-4 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between border-b border-line pb-3">
                    <h3 class="text-base font-bold text-brand-dark flex items-center gap-2">
                        <i class="fa-solid fa-chart-pie text-cyan-600"></i>
                        <span>Estado de Capacitaciones</span>
                    </h3>
                    <span class="text-xs font-bold text-slate-500 bg-slate-100 px-2.5 py-0.5 rounded-full">
                        {{ $kpis['total_asignaciones'] }} asig.
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-2">
                    Proporción del avance formativo de los colaboradores de tu área.
                </p>

                <!-- Canvas Doughnut -->
                <div class="relative w-full h-56 sm:h-60 pt-2">
                    <canvas id="chartEstadosCapacitacion"></canvas>
                </div>
            </div>

            <!-- Leyenda Detallada -->
            <div class="grid grid-cols-2 gap-2 pt-3 border-t border-line text-xs">
                <div class="flex items-center gap-2 bg-emerald-50/70 p-2 rounded-xl border border-emerald-100">
                    <span class="w-3 h-3 rounded-full bg-emerald-500 shrink-0"></span>
                    <div>
                        <div class="font-bold text-emerald-900">{{ $kpis['completadas'] }}</div>
                        <div class="text-[10px] text-emerald-700">Completadas</div>
                    </div>
                </div>
                <div class="flex items-center gap-2 bg-blue-50/70 p-2 rounded-xl border border-blue-100">
                    <span class="w-3 h-3 rounded-full bg-cyan-600 shrink-0"></span>
                    <div>
                        <div class="font-bold text-blue-900">{{ $kpis['en_progreso'] }}</div>
                        <div class="text-[10px] text-blue-700">En Progreso</div>
                    </div>
                </div>
                <div class="flex items-center gap-2 bg-amber-50/70 p-2 rounded-xl border border-amber-100">
                    <span class="w-3 h-3 rounded-full bg-amber-500 shrink-0"></span>
                    <div>
                        <div class="font-bold text-amber-900">{{ $kpis['pendientes'] }}</div>
                        <div class="text-[10px] text-amber-700">Pendientes</div>
                    </div>
                </div>
                <div class="flex items-center gap-2 bg-rose-50/70 p-2 rounded-xl border border-rose-100">
                    <span class="w-3 h-3 rounded-full bg-rose-500 shrink-0"></span>
                    <div>
                        <div class="font-bold text-rose-900">{{ $kpis['no_aprobadas'] }}</div>
                        <div class="text-[10px] text-rose-700">No aprobadas</div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Gráfico 3: Actividad y Tendencia Mensual (Full width) -->
    <div class="bg-white rounded-3xl border border-line p-5 sm:p-6 shadow-2xs space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-line pb-3">
            <div>
                <h3 class="text-base font-bold text-brand-dark flex items-center gap-2">
                    <i class="fa-solid fa-chart-line text-brand-blue"></i>
                    <span>Evolución y Ritmo Formativo Mensual (Últimos 6 Meses)</span>
                </h3>
                <p class="text-xs text-slate-500">
                    Capacitaciones completadas, evaluaciones aprobadas y asistencias a inducciones logradas por el personal de tu área mes a mes.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold bg-blue-50 text-brand-blue border border-blue-100">
                    <span class="w-2 h-2 rounded-full bg-brand-blue"></span> Capacitaciones
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span> Inducciones
                </span>
            </div>
        </div>

        <div class="relative w-full h-72 sm:h-80 pt-2">
            <canvas id="chartEvolucionMensualArea"></canvas>
        </div>
    </div>

    <!-- SECCIÓN: DESGLOSE Y MÉTRICAS DETALLADAS POR SEDE -->
    <div class="bg-white rounded-3xl border border-line p-5 sm:p-6 shadow-2xs space-y-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-base sm:text-lg font-bold text-brand-dark flex items-center gap-2 font-serif">
                    <i class="fa-solid fa-building-circle-check text-brand-blue"></i>
                    <span>Desglose Operativo por Sedes de tu Área</span>
                </h3>
                <p class="text-xs text-slate-500">
                    Rendimiento comparativo de los colaboradores de tu área distribuidos en cada una de las sedes de la empresa.
                </p>
            </div>
            <span class="text-xs font-bold px-3 py-1 rounded-full bg-slate-100 text-slate-700 border border-slate-200 self-start sm:self-center">
                {{ $porSede->count() }} Sedes Registradas
            </span>
        </div>

        <!-- Grid de Tarjetas de Sedes -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($porSede as $sede)
                <div class="p-5 rounded-2xl border border-slate-200 bg-slate-50/50 hover:bg-white hover:border-brand-blue/40 hover:shadow-md transition-all space-y-4">
                    <!-- Cabecera Sede -->
                    <div class="flex items-start justify-between gap-3 border-b border-slate-200/80 pb-3">
                        <div class="flex items-center gap-3">
                            <span class="w-10 h-10 rounded-xl bg-blue-100/70 border border-blue-200 text-brand-blue flex items-center justify-center text-sm font-bold shrink-0">
                                <i class="fa-solid fa-building"></i>
                            </span>
                            <div>
                                <h4 class="font-bold text-slate-900 text-sm leading-snug">{{ $sede->nombre }}</h4>
                                <span class="text-[11px] text-slate-500 flex items-center gap-1 mt-0.5">
                                    <i class="fa-solid fa-location-dot text-rose-500 text-[10px]"></i>
                                    <span>{{ $sede->ciudad }}</span>
                                </span>
                            </div>
                        </div>

                        <span class="px-2.5 py-1 rounded-full text-xs font-extrabold bg-white border border-slate-200 text-slate-800 shadow-2xs shrink-0">
                            {{ $sede->colaboradores_count }} {{ $sede->colaboradores_count === 1 ? 'colab.' : 'colabs.' }}
                        </span>
                    </div>

                    <!-- Métricas de la Sede -->
                    <div class="space-y-3">
                        <!-- Barra de Avance Promedio -->
                        <div>
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="font-bold text-slate-600">Avance Promedio Capacitación</span>
                                <span class="font-mono font-black text-brand-dark">{{ $sede->promedio_avance }}%</span>
                            </div>
                            <div class="w-full bg-slate-200 rounded-full h-2.5 overflow-hidden">
                                <div class="bg-gradient-to-r from-brand-blue to-cyan-500 h-full rounded-full transition-all duration-500"
                                     style="width: {{ min(100, $sede->promedio_avance) }}%;"></div>
                            </div>
                        </div>

                        <!-- Grid de Indicadores de la Sede -->
                        <div class="grid grid-cols-2 gap-2 text-xs pt-1">
                            <div class="bg-white p-2.5 rounded-xl border border-slate-200/80">
                                <span class="text-[10px] uppercase font-bold text-slate-400 block mb-0.5">Cumplimiento</span>
                                <div class="font-bold text-emerald-700 text-xs flex items-center gap-1">
                                    <i class="fa-solid fa-circle-check text-[10px] text-emerald-500"></i>
                                    <span>{{ $sede->tasa_cumplimiento }}%</span>
                                    <span class="text-[10px] text-slate-400 font-normal">({{ $sede->completadas_count }}/{{ $sede->asignaciones_count }})</span>
                                </div>
                            </div>

                            <div class="bg-white p-2.5 rounded-xl border border-slate-200/80">
                                <span class="text-[10px] uppercase font-bold text-slate-400 block mb-0.5">Diplomas</span>
                                <div class="font-bold text-purple-700 text-xs flex items-center gap-1">
                                    <i class="fa-solid fa-award text-[10px] text-purple-500"></i>
                                    <span>{{ $sede->certificados_count }} emitidos</span>
                                </div>
                            </div>

                            <div class="col-span-2 bg-white p-2.5 rounded-xl border border-slate-200/80 flex items-center justify-between">
                                <span class="text-[10px] uppercase font-bold text-slate-400">Asistencias a Inducción</span>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                    <i class="fa-solid fa-file-signature text-[10px] text-amber-600"></i>
                                    {{ $sede->inducciones_count }} registradas
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Enlace directo a personal de esta sede -->
                    <div class="pt-2 border-t border-slate-200/80">
                        <a href="{{ route('jefe.personal.index', ['sede_id' => $sede->id]) }}"
                           class="w-full py-2 px-3 rounded-xl bg-white hover:bg-brand-blue hover:text-white text-slate-700 font-bold text-xs flex items-center justify-center gap-2 border border-slate-200 transition-colors shadow-2xs">
                            <i class="fa-solid fa-user-group text-[11px]"></i>
                            <span>Ver Colaboradores de esta Sede</span>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- SECCIÓN DUAL: RANKING DE CAPACITACIONES & ÚLTIMAS INDUCCIONES ASISTIDAS -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Columna 1: Ranking de Capacitaciones Asignadas a tu Área -->
        <div class="bg-white rounded-3xl border border-line p-5 sm:p-6 shadow-2xs space-y-4 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between border-b border-line pb-3">
                    <h3 class="text-base font-bold text-brand-dark flex items-center gap-2">
                        <i class="fa-solid fa-graduation-cap text-brand-blue"></i>
                        <span>Capacitaciones Asignadas a tu Área</span>
                    </h3>
                    <a href="{{ route('jefe.capacitaciones.index') }}" class="text-xs font-bold text-brand-blue hover:underline">
                        Ver todas
                    </a>
                </div>
                <p class="text-xs text-slate-500 mt-1">
                    Cursos asignados a los colaboradores de tu área ordenados por avance de cumplimiento.
                </p>

                @if($rankingCapacitaciones->isEmpty())
                    <div class="py-12 text-center text-xs text-slate-400 space-y-2">
                        <i class="fa-solid fa-book-open text-2xl text-slate-300"></i>
                        <p>No hay capacitaciones asignadas actualmente a tu área.</p>
                    </div>
                @else
                    <div class="space-y-3 pt-3">
                        @foreach($rankingCapacitaciones as $cap)
                            <div class="p-3.5 rounded-2xl bg-slate-50/70 border border-slate-200/80 space-y-2 hover:bg-white hover:border-brand-blue/30 transition-all">
                                <div class="flex items-start justify-between gap-3">
                                    <h4 class="font-bold text-slate-900 text-xs sm:text-sm leading-snug line-clamp-1">
                                        {{ $cap->titulo }}
                                    </h4>
                                    <span class="font-mono text-xs font-black text-brand-blue shrink-0">
                                        {{ $cap->promedio_avance }}%
                                    </span>
                                </div>
                                <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden">
                                    <div class="bg-gradient-to-r from-brand-blue to-emerald-500 h-full rounded-full transition-all duration-300"
                                         style="width: {{ min(100, $cap->promedio_avance) }}%;"></div>
                                </div>
                                <div class="flex items-center justify-between text-[11px] text-slate-500">
                                    <span>Colaboradores: <strong>{{ $cap->completadas }}</strong> completaron de {{ $cap->total_colaboradores }}</span>
                                    <span class="font-semibold text-emerald-700">{{ $cap->tasa_cumplimiento }}% cumplimiento</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="pt-3 border-t border-line text-center">
                <a href="{{ route('jefe.capacitaciones.index') }}"
                   class="inline-flex items-center gap-2 text-xs font-bold text-brand-blue hover:text-brand-dark transition-colors">
                    <span>Gestionar capacitaciones publicadas</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        </div>

        <!-- Columna 2: Últimas Inducciones Asistidas por Personal de tu Área -->
        <div class="bg-white rounded-3xl border border-line p-5 sm:p-6 shadow-2xs space-y-4 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between border-b border-line pb-3">
                    <h3 class="text-base font-bold text-brand-dark flex items-center gap-2">
                        <i class="fa-solid fa-chalkboard-user text-amber-600"></i>
                        <span>Últimas Asistencias a Inducción</span>
                    </h3>
                    <a href="{{ route('jefe.inducciones.index') }}" class="text-xs font-bold text-brand-blue hover:underline">
                        Ver módulo
                    </a>
                </div>
                <p class="text-xs text-slate-500 mt-1">
                    Registros oficiales más recientes firmados por personal de tu área.
                </p>

                @if($asistenciasInduccion->isEmpty())
                    <div class="py-12 text-center text-xs text-slate-400 space-y-2">
                        <i class="fa-solid fa-file-signature text-2xl text-slate-300"></i>
                        <p>No se registran asistencias de tu área aún.</p>
                    </div>
                @else
                    <div class="space-y-2.5 pt-3">
                        @foreach($asistenciasInduccion->take(5) as $asist)
                            <div class="p-3 rounded-2xl bg-slate-50/70 border border-slate-200/80 flex items-center justify-between gap-3 hover:bg-white hover:border-cyan-300 transition-all">
                                <div class="min-w-0 space-y-0.5">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-bold text-slate-900 text-xs truncate">{{ $asist->nombre_completo }}</span>
                                        @if(!empty($asist->firma_digital))
                                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[9px] font-bold">
                                                <i class="fa-solid fa-check text-[8px]"></i> Firmado
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-[11px] text-slate-500 truncate">
                                        {{ $asist->sesion?->induccion?->titulo ?? 'Inducción Institucional' }}
                                    </p>
                                    <div class="text-[10px] text-slate-400 flex items-center gap-2">
                                        <span>CC: {{ $asist->identificacion }}</span>
                                        <span>&bull;</span>
                                        <span>{{ $asist->sede ?: ($asist->sesion?->sede?->nombre ?? 'Sede Principal') }}</span>
                                        <span>&bull;</span>
                                        <span>{{ $asist->fecha_registro ? $asist->fecha_registro->format('d/m/Y') : '-' }}</span>
                                    </div>
                                </div>

                                @if($asist->sesion)
                                    <a href="{{ route('induccion.acta.pdf', $asist->sesion->codigo_sesion) }}"
                                       class="p-2 rounded-xl bg-white hover:bg-emerald-50 hover:text-emerald-700 text-slate-600 border border-slate-200 transition-colors shadow-2xs shrink-0"
                                       title="Descargar Acta en PDF">
                                        <i class="fa-solid fa-file-pdf text-red-500 text-xs"></i>
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="pt-3 border-t border-line text-center">
                <a href="{{ route('jefe.inducciones.index') }}"
                   class="inline-flex items-center gap-2 text-xs font-bold text-brand-blue hover:text-brand-dark transition-colors">
                    <span>Ver todas las sesiones y participantes con firmas</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        </div>

    </div>

</div>

<!-- ========================================================================= -->
<!-- SCRIPTS: CHART.JS & CONFIGURACIÓN DE LOS GRÁFICOS DEL JEFE DE ÁREA         -->
<!-- ========================================================================= -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Fuente y configuración base para Chart.js
        Chart.defaults.font.family = "'DM Sans', sans-serif";
        Chart.defaults.color = '#64748b';

        // ========================================================
        // 1. GRÁFICA: DESEMPEÑO POR SEDE DE TU ÁREA (Bar Chart)
        // ========================================================
        const canvasSedes = document.getElementById('chartDesempenoSedes');
        if (canvasSedes) {
            const sedesData = @json($porSede);
            const sedesNombres = sedesData.map(s => s.nombre);
            const avanceSedes = sedesData.map(s => s.promedio_avance);
            const cumplimientoSedes = sedesData.map(s => s.tasa_cumplimiento);
            const induccionesSedes = sedesData.map(s => s.inducciones_count);

            new Chart(canvasSedes, {
                type: 'bar',
                data: {
                    labels: sedesNombres,
                    datasets: [
                        {
                            label: '% Avance Promedio',
                            data: avanceSedes,
                            backgroundColor: 'rgba(2, 132, 199, 0.85)', // brand-blue
                            borderColor: '#0284c7',
                            borderWidth: 1.5,
                            borderRadius: 8,
                        },
                        {
                            label: '% Tasa de Cumplimiento',
                            data: cumplimientoSedes,
                            backgroundColor: 'rgba(16, 185, 129, 0.85)', // emerald
                            borderColor: '#10b981',
                            borderWidth: 1.5,
                            borderRadius: 8,
                        },
                        {
                            label: 'Asistencias a Inducción',
                            data: induccionesSedes,
                            backgroundColor: 'rgba(245, 158, 11, 0.85)', // amber
                            borderColor: '#f59e0b',
                            borderWidth: 1.5,
                            borderRadius: 8,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                boxWidth: 12,
                                font: { size: 11, weight: '600' },
                                padding: 14
                            }
                        },
                        tooltip: {
                            padding: 10,
                            boxPadding: 4,
                            cornerRadius: 8,
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false }
                        },
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                callback: function (val) {
                                    return val + '%';
                                }
                            },
                            grid: { color: 'rgba(226, 232, 240, 0.6)' }
                        }
                    }
                }
            });
        }

        // ========================================================
        // 2. GRÁFICA: DISTRIBUCIÓN DE ESTADOS (Doughnut Chart)
        // ========================================================
        const canvasEstados = document.getElementById('chartEstadosCapacitacion');
        if (canvasEstados) {
            const completadas = {{ $kpis['completadas'] }};
            const enProgreso = {{ $kpis['en_progreso'] }};
            const pendientes = {{ $kpis['pendientes'] }};
            const noAprobadas = {{ $kpis['no_aprobadas'] }};

            const total = completadas + enProgreso + pendientes + noAprobadas;

            new Chart(canvasEstados, {
                type: 'doughnut',
                data: {
                    labels: ['Completadas', 'En Progreso', 'Pendientes', 'No Aprobadas'],
                    datasets: [{
                        data: total > 0 ? [completadas, enProgreso, pendientes, noAprobadas] : [1],
                        backgroundColor: total > 0
                            ? ['#10b981', '#0284c7', '#f59e0b', '#f43f5e']
                            : ['#e2e8f0'],
                        borderWidth: 2,
                        borderColor: '#ffffff',
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            enabled: total > 0,
                            padding: 10,
                            callbacks: {
                                label: function (context) {
                                    const val = context.raw || 0;
                                    const pct = total > 0 ? Math.round((val / total) * 100) : 0;
                                    return ` ${context.label}: ${val} (${pct}%)`;
                                }
                            }
                        }
                    },
                    cutout: '70%'
                }
            });
        }

        // ========================================================
        // 3. GRÁFICA: EVOLUCIÓN MENSUAL DEL ÁREA (Line / Bar)
        // ========================================================
        const canvasEvolucion = document.getElementById('chartEvolucionMensualArea');
        if (canvasEvolucion) {
            const evolucionData = @json($evolucionMensual);
            const meses = evolucionData.map(d => d.mes);
            const completadasMes = evolucionData.map(d => d.completadas);
            const induccionesMes = evolucionData.map(d => d.inducciones);
            const aprobadosMes = evolucionData.map(d => d.aprobados);

            new Chart(canvasEvolucion, {
                type: 'bar',
                data: {
                    labels: meses,
                    datasets: [
                        {
                            label: 'Capacitaciones Completadas',
                            data: completadasMes,
                            backgroundColor: 'rgba(2, 132, 199, 0.85)',
                            borderColor: '#0284c7',
                            borderWidth: 1.5,
                            borderRadius: 6,
                        },
                        {
                            label: 'Asistencias a Inducción',
                            data: induccionesMes,
                            backgroundColor: 'rgba(245, 158, 11, 0.85)',
                            borderColor: '#f59e0b',
                            borderWidth: 1.5,
                            borderRadius: 6,
                        },
                        {
                            label: 'Evaluaciones Aprobadas',
                            data: aprobadosMes,
                            backgroundColor: 'rgba(16, 185, 129, 0.85)',
                            borderColor: '#10b981',
                            borderWidth: 1.5,
                            borderRadius: 6,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                boxWidth: 12,
                                font: { size: 11, weight: '600' },
                                padding: 14
                            }
                        },
                        tooltip: {
                            padding: 10,
                            boxPadding: 4,
                            cornerRadius: 8
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 },
                            grid: { color: 'rgba(226, 232, 240, 0.6)' }
                        }
                    }
                }
            });
        }
    });
</script>
@endsection

