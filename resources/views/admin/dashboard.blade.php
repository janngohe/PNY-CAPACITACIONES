@extends('layouts.admin')

@section('title', 'Resumen General')
@section('page_title', 'Resumen General')

@section('content')
@php
    $usuario = $usuario ?? auth()->user();
    $tarjetas = [
        ['Usuarios activos', $usuarios['activos'], 'de ' . $usuarios['total'] . ' registrados', 'fa-users', 'bg-blue-50 text-brand-blue'],
        ['Sedes activas', $kpis['sedes'], 'de ' . $kpis['sedes_total'] . ' operando', 'fa-building-shield', 'bg-indigo-50 text-indigo-700'],
        ['Capacitaciones activas', $kpis['capacitaciones'], 'de ' . $kpis['capacitaciones_total'] . ' creadas', 'fa-book-open', 'bg-cyan-50 text-cyan-700'],
        ['Avance global', $kpis['avance_global'] . '%', $kpis['completadas'] . ' completadas', 'fa-chart-line', 'bg-emerald-50 text-emerald-700'],
        ['Aprobación en evaluaciones', $kpis['tasa_aprobacion'] . '%', $kpis['intentos'] . ' intentos', 'fa-clipboard-check', 'bg-amber-50 text-amber-700'],
        ['Certificados emitidos', $kpis['certificados'], 'en toda la plataforma', 'fa-award', 'bg-violet-50 text-violet-700'],
        ['Áreas activas', $kpis['areas'], $kpis['evaluaciones'] . ' evaluaciones activas', 'fa-sitemap', 'bg-rose-50 text-rose-700'],
    ];
@endphp
<div class="w-full space-y-6 sm:space-y-8 max-w-[1600px] mx-auto">

    @include('admin._banner', [
        'insignia' => 'Administrador del sistema',
        'titulo' => '¡Bienvenido, ' . $usuario->nombre_completo . '!',
        'descripcion' => 'Visión general de la plataforma de inducción y capacitación: usuarios, formación, sedes, áreas, evaluaciones y certificados en un solo lugar.',
        'icono' => 'fa-fish-fins',
    ])

    <!-- ACCESOS RÁPIDOS -->
    <div class="flex flex-wrap gap-2.5">
        <a href="{{ route('admin.capacitaciones.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-brand-blue hover:bg-brand-deep text-white font-heading font-bold text-xs shadow-xs transition-colors">
            <i class="fa-solid fa-plus"></i> Nueva capacitación
        </a>
        <a href="{{ route('admin.sedes.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-line hover:border-brand-blue hover:text-brand-blue text-slate-700 font-heading font-bold text-xs transition-colors shadow-2xs">
            <i class="fa-solid fa-building"></i> Gestionar sedes
        </a>
        <a href="{{ route('admin.areas.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-line hover:border-brand-blue hover:text-brand-blue text-slate-700 font-heading font-bold text-xs transition-colors shadow-2xs">
            <i class="fa-solid fa-sitemap"></i> Gestionar áreas
        </a>
        <a href="{{ route('admin.usuarios.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-line hover:border-brand-blue hover:text-brand-blue text-slate-700 font-heading font-bold text-xs transition-colors shadow-2xs">
            <i class="fa-solid fa-user-plus"></i> Registrar usuario
        </a>
        <a href="{{ route('admin.evaluaciones.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-line hover:border-brand-blue hover:text-brand-blue text-slate-700 font-heading font-bold text-xs transition-colors shadow-2xs">
            <i class="fa-solid fa-clipboard-question"></i> Nueva evaluación
        </a>
        <a href="{{ route('admin.reportes.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-line hover:border-brand-blue hover:text-brand-blue text-slate-700 font-heading font-bold text-xs transition-colors shadow-2xs">
            <i class="fa-solid fa-file-arrow-down"></i> Generar reportes
        </a>
    </div>

    <!-- INDICADORES GENERALES -->
    <section aria-label="Indicadores generales" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @foreach ($tarjetas as [$etiqueta, $valor, $detalle, $icono, $color])
            <div class="bg-white rounded-3xl border border-line/80 shadow-2xs hover:shadow-md hover:-translate-y-0.5 transition-all p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl {{ $color }} flex items-center justify-center text-lg shrink-0">
                    <i class="fa-solid {{ $icono }}"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-muted truncate">{{ $etiqueta }}</p>
                    <p class="font-heading font-extrabold text-2xl text-brand-dark leading-tight">{{ $valor }}</p>
                    <p class="text-[11px] text-muted truncate">{{ $detalle }}</p>
                </div>
            </div>
        @endforeach
    </section>

    <!-- ESTADÍSTICAS: ESTADO DE ASIGNACIONES Y AVANCE PROMEDIO POR ÁREA -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">

        <!-- ESTADO DE LAS ASIGNACIONES -->
        <section class="bg-white rounded-3xl border border-line/80 shadow-2xs p-5 sm:p-6 space-y-4 lg:col-span-5 xl:col-span-4 flex flex-col justify-between" aria-labelledby="t-estado">
            <div class="space-y-4">
                <h2 id="t-estado" class="font-heading font-bold text-base text-brand-dark">Estado de las capacitaciones asignadas</h2>
                @php
                    $totalEstados = max(1, $kpis['completadas'] + $kpis['en_progreso'] + $kpis['pendientes']);
                    $segmentos = [
                        ['Completadas', $kpis['completadas'], 'bg-emerald-500'],
                        ['En progreso', $kpis['en_progreso'], 'bg-brand-blue'],
                        ['Pendientes', $kpis['pendientes'], 'bg-slate-300'],
                    ];
                @endphp
                <div class="flex w-full h-3 rounded-full overflow-hidden bg-slate-100">
                    @foreach ($segmentos as [$nombre, $cantidad, $clase])
                        <div class="{{ $clase }} transition-all duration-500" style="width: {{ ($cantidad / $totalEstados) * 100 }}%" title="{{ $nombre }}: {{ $cantidad }}"></div>
                    @endforeach
                </div>
                <ul class="space-y-2.5">
                    @foreach ($segmentos as [$nombre, $cantidad, $clase])
                        <li class="flex items-center justify-between text-xs">
                            <span class="inline-flex items-center gap-2 font-semibold text-slate-700"><span class="w-2.5 h-2.5 rounded-full {{ $clase }}"></span>{{ $nombre }}</span>
                            <span class="font-bold text-brand-dark">{{ $cantidad }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="pt-3 border-t border-line/60 grid grid-cols-3 gap-2 text-center">
                <div class="bg-slate-50 rounded-xl p-2.5"><span class="block font-heading font-extrabold text-lg text-brand-dark">{{ $usuarios['empleados'] }}</span><span class="text-[10px] text-muted">Empleados</span></div>
                <div class="bg-slate-50 rounded-xl p-2.5"><span class="block font-heading font-extrabold text-lg text-brand-dark">{{ $usuarios['jefes'] }}</span><span class="text-[10px] text-muted">Jefes</span></div>
                <div class="bg-slate-50 rounded-xl p-2.5"><span class="block font-heading font-extrabold text-lg text-brand-dark">{{ $usuarios['admins'] }}</span><span class="text-[10px] text-muted">Admins</span></div>
            </div>
        </section>

        <!-- AVANCE POR ÁREA (DISCRIMINADO POR SEDE) -->
        <section class="bg-white rounded-3xl border border-line/80 shadow-2xs p-5 sm:p-6 space-y-4 lg:col-span-7 xl:col-span-8 flex flex-col justify-between" aria-labelledby="t-areas">
            <div class="flex items-center justify-between">
                <div>
                    <h2 id="t-areas" class="font-heading font-bold text-base text-brand-dark">Avance promedio por área</h2>
                    <p class="text-xs text-muted">Progreso discriminado por cada área y su sede institucional.</p>
                </div>
                <a href="{{ route('admin.progreso.index') }}" class="text-xs font-bold text-brand-blue hover:underline">Ver detalle</a>
            </div>
            <div class="space-y-3 max-h-80 overflow-y-auto pr-1">
                @forelse ($porArea as $area)
                    <div class="space-y-1.5 p-3 rounded-2xl bg-slate-50/70 border border-line/60 hover:border-brand-blue/30 hover:bg-white transition-all shadow-2xs">
                        <div class="flex flex-wrap items-center justify-between text-xs gap-2">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="font-bold text-slate-800 text-xs">{{ $area->nombre }}</span>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-700 text-[10px] font-bold border border-indigo-100">
                                    <i class="fa-solid fa-building text-[9px]"></i>
                                    {{ $area->sede_nombre }}
                                </span>
                                @if(!empty($area->sede_ciudad))
                                    <span class="text-[10px] text-muted">({{ $area->sede_ciudad }})</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-2.5">
                                <span class="text-[11px] text-muted">{{ $area->completadas }}/{{ $area->asignaciones }} cursos ({{ $area->empleados }} emp.)</span>
                                <span class="font-heading font-extrabold text-sm {{ $area->promedio >= 80 ? 'text-emerald-600' : ($area->promedio >= 50 ? 'text-brand-blue' : 'text-amber-600') }}">{{ $area->promedio }}%</span>
                            </div>
                        </div>
                        <div class="w-full bg-slate-200/70 rounded-full h-2 overflow-hidden">
                            <div class="h-2 rounded-full transition-all duration-500 {{ $area->promedio >= 80 ? 'bg-gradient-to-r from-emerald-500 to-teal-400' : ($area->promedio >= 50 ? 'bg-gradient-to-r from-brand-blue to-brand-sky' : 'bg-gradient-to-r from-amber-500 to-orange-400') }}" 
                                 style="width: {{ $area->promedio }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-muted py-6 text-center">Aún no hay áreas registradas.</p>
                @endforelse
            </div>
        </section>
    </div>

    <!-- ========================================================================= -->
    <!-- 1 & 2. NUEVAS ESTADÍSTICAS PRIORITARIAS: TASA DE FINALIZACIÓN Y PENDIENTES -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- 1. TASA DE FINALIZACIÓN DE CAPACITACIONES -->
        <section class="bg-white rounded-3xl border border-line/80 shadow-2xs p-5 sm:p-7 space-y-5" aria-labelledby="t-tasa-fin">
            <div class="flex items-start justify-between gap-3 border-b border-line/60 pb-3.5">
                <div>
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold mb-1">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>Prioridad Muy Alta</span>
                    </div>
                    <h2 id="t-tasa-fin" class="font-heading font-extrabold text-lg text-brand-dark">Tasa de finalización de capacitaciones</h2>
                    <p class="text-xs text-muted mt-0.5">Porcentaje de colaboradores que completaron exitosamente todas sus capacitaciones asignadas.</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shrink-0">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
            </div>

            <div class="bg-gradient-to-br from-emerald-50/70 via-emerald-50/30 to-white rounded-2xl p-4 sm:p-5 border border-emerald-100/80 space-y-3">
                <div class="flex items-baseline justify-between">
                    <div>
                        <span class="font-heading font-black text-4xl sm:text-5xl text-emerald-600">{{ $tasaFinalizacion }}%</span>
                        <p class="text-xs font-semibold text-slate-600 mt-1">de colaboradores completaron sus capacitaciones</p>
                    </div>
                    <div class="text-right">
                        <span class="text-xs font-extrabold text-brand-dark block">{{ $totalEmpleadosCompletados }} de {{ $totalEmpleadosAsignados }}</span>
                        <span class="text-[11px] text-muted">empleados al 100%</span>
                    </div>
                </div>

                <div class="w-full bg-emerald-100 rounded-full h-3 overflow-hidden">
                    <div class="bg-gradient-to-r from-emerald-500 to-teal-400 h-3 rounded-full transition-all duration-700" style="width: {{ $tasaFinalizacion }}%"></div>
                </div>

                <div class="grid grid-cols-2 gap-2 pt-1 text-center text-xs">
                    <div class="bg-white/80 rounded-xl p-2.5 border border-emerald-100">
                        <span class="block text-[10px] uppercase font-bold text-muted">Cursos finalizados</span>
                        <span class="font-heading font-extrabold text-base text-emerald-700">{{ $kpis['completadas'] }}</span>
                    </div>
                    <div class="bg-white/80 rounded-xl p-2.5 border border-emerald-100">
                        <span class="block text-[10px] uppercase font-bold text-muted">Certificados logrados</span>
                        <span class="font-heading font-extrabold text-base text-amber-600">{{ $kpis['certificados'] }}</span>
                    </div>
                </div>
            </div>

            <!-- BOTÓN PARA VER EMPLEADOS -->
            <div class="pt-1 flex items-center justify-between gap-3">
                <span class="text-[11px] text-muted flex items-center gap-1">
                    <i class="fa-solid fa-info-circle text-slate-400"></i>
                    Consulta colaboradores con historial completo
                </span>
                <a href="{{ route('admin.progreso.index', ['estado' => 'COMPLETADA']) }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-heading font-bold text-xs shadow-xs transition-colors cursor-pointer">
                    <i class="fa-solid fa-user-check"></i>
                    <span>Ver empleados completados</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        </section>

        <!-- 2. EMPLEADOS PENDIENTES DE CAPACITACIÓN -->
        <section class="bg-white rounded-3xl border border-line/80 shadow-2xs p-5 sm:p-7 space-y-5" aria-labelledby="t-empleados-pend">
            <div class="flex items-start justify-between gap-3 border-b border-line/60 pb-3.5">
                <div>
                    <h2 id="t-empleados-pend" class="font-heading font-extrabold text-lg text-brand-dark">Empleados pendientes de capacitación</h2>
                    <p class="text-xs text-muted mt-0.5">Colaboradores que todavía no han iniciado o terminado cursos obligatorios o asignados.</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shrink-0">
                    <i class="fa-solid fa-user-clock"></i>
                </div>
            </div>

            <!-- Resumen principal y estado general -->
            <div class="bg-gradient-to-br from-amber-50/70 via-amber-50/30 to-white rounded-2xl p-4 sm:p-5 border border-amber-100/90 space-y-4">
                <div class="flex items-baseline justify-between flex-wrap gap-2">
                    <div>
                        <div class="flex items-baseline gap-2">
                            <span class="font-heading font-black text-4xl sm:text-5xl text-amber-600">{{ $pendientesDistribucion['total'] }}</span>
                            <span class="text-xs sm:text-sm font-semibold text-slate-600">empleados pendientes de finalización</span>
                        </div>
                        <p class="text-[11px] text-muted mt-0.5">Distribuidos por sedes y sus respectivas áreas operativas.</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold {{ $pendientesDistribucion['total'] > 0 ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-emerald-100 text-emerald-800 border border-emerald-200' }}">
                        {{ $pendientesDistribucion['total'] > 0 ? 'Requieren seguimiento' : 'Todo al día' }}
                    </span>
                </div>

                <!-- SEDES Y ÁREAS DESLIZANTES (HORIZONTAL Y VERTICAL RESPONSIVE) -->
                <div class="pt-2 border-t border-amber-100/80 space-y-2">
                    <div class="flex items-center justify-between text-[11px] font-bold text-slate-700 uppercase tracking-wide">
                        <span class="flex items-center gap-1.5">
                            <i class="fa-solid fa-building text-amber-500"></i>
                            <span>Sedes y Áreas en seguimiento</span>
                        </span>
                        <span class="text-[10px] text-muted normal-case font-medium hidden sm:inline-flex items-center gap-1">
                            <i class="fa-solid fa-arrows-left-right text-slate-400"></i> Desliza horizontalmente para ver más sedes
                        </span>
                    </div>

                    <!-- Contenedor Deslizante Horizontal (Sedes actuales y futuras) -->
                    <div class="flex gap-3 overflow-x-auto pb-2.5 pt-1 -mx-1 px-1 snap-x scrollbar-thin scrollbar-thumb-amber-200 scrollbar-track-transparent">
                        @forelse($pendientesDistribucion['sedes_detalle'] as $sedeDetalle)
                            <div class="min-w-[260px] sm:min-w-[280px] max-w-[320px] flex-shrink-0 snap-start bg-white/95 rounded-2xl border {{ $sedeDetalle->total_pendientes > 0 ? 'border-amber-200/90 shadow-2xs hover:border-amber-300' : 'border-slate-200/80 shadow-2xs' }} p-3.5 flex flex-col justify-between transition-all">
                                <div>
                                    <!-- Cabecera de Sede -->
                                    <div class="flex items-start justify-between gap-2 border-b border-slate-100 pb-2 mb-2.5">
                                        <div class="min-w-0">
                                            <h4 class="font-heading font-extrabold text-sm text-brand-dark truncate flex items-center gap-1.5">
                                                <i class="fa-solid fa-city text-xs {{ $sedeDetalle->total_pendientes > 0 ? 'text-amber-500' : 'text-slate-400' }}"></i>
                                                <span class="truncate">{{ $sedeDetalle->nombre }}</span>
                                            </h4>
                                            @if($sedeDetalle->ciudad)
                                                <span class="text-[10px] text-muted block truncate">{{ $sedeDetalle->ciudad }}</span>
                                            @endif
                                        </div>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold shrink-0 {{ $sedeDetalle->total_pendientes > 0 ? 'bg-amber-100 text-amber-800' : 'bg-emerald-50 text-emerald-700' }}">
                                            {{ $sedeDetalle->total_pendientes }} {{ \Illuminate\Support\Str::plural('pendiente', $sedeDetalle->total_pendientes) }}
                                        </span>
                                    </div>

                                    <!-- Lista Deslizante Vertical de Áreas (para cuando haya muchas áreas) -->
                                    <div class="space-y-1.5 max-h-36 overflow-y-auto pr-1 scrollbar-thin scrollbar-thumb-slate-200 scrollbar-track-transparent">
                                        @forelse($sedeDetalle->areas as $areaDetalle)
                                            <div class="flex items-center justify-between gap-2 p-1.5 rounded-lg {{ $areaDetalle->pendientes > 0 ? 'bg-amber-50/60 text-slate-800' : 'bg-slate-50/50 text-slate-600' }} text-xs">
                                                <span class="truncate font-medium flex items-center gap-1.5">
                                                    <i class="fa-solid fa-folder-tree text-[10px] {{ $areaDetalle->pendientes > 0 ? 'text-amber-500' : 'text-slate-400' }}"></i>
                                                    <span class="truncate">{{ $areaDetalle->nombre }}</span>
                                                </span>
                                                <span class="px-1.5 py-0.5 rounded-md text-[10px] font-bold shrink-0 {{ $areaDetalle->pendientes > 0 ? 'bg-amber-200/80 text-amber-900' : 'bg-slate-200/70 text-slate-600' }}">
                                                    {{ $areaDetalle->pendientes }}
                                                </span>
                                            </div>
                                        @empty
                                            <p class="text-[11px] text-muted italic py-1">Sin áreas asignadas a esta sede.</p>
                                        @endforelse
                                    </div>
                                </div>

                                <!-- Pie de Sede: Enlace directo al filtrado de sede -->
                                <div class="pt-2.5 mt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px]">
                                    <span class="text-slate-400 text-[10px]">{{ $sedeDetalle->areas->count() }} {{ \Illuminate\Support\Str::plural('área', $sedeDetalle->areas->count()) }}</span>
                                    <a href="{{ route('admin.progreso.index', ['sede_id' => $sedeDetalle->id, 'estado' => 'PENDIENTE']) }}"
                                       class="font-bold text-brand-blue hover:text-brand-deep hover:underline inline-flex items-center gap-1 transition-colors">
                                        <span>Filtrar sede</span>
                                        <i class="fa-solid fa-arrow-right text-[9px]"></i>
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="w-full p-4 rounded-xl bg-white border border-slate-200 text-center text-xs text-muted italic">
                                No se encontraron sedes activas registradas en el sistema.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- BOTÓN PARA VER EN PROGRESO (SIN BOTÓN VER LISTA RÁPIDA) -->
            <div class="pt-1 flex flex-col sm:flex-row items-center justify-between gap-3">
                <span class="text-xs text-muted flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-info text-amber-500"></i>
                    Consulta el detalle individual por usuario y curso desde el módulo de progreso.
                </span>

                <a href="{{ route('admin.progreso.index', ['estado' => 'PENDIENTE']) }}" 
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-heading font-bold text-xs sm:text-sm shadow-xs transition-colors cursor-pointer">
                    <i class="fa-solid fa-users-viewfinder"></i>
                    <span>Ver en Progreso</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        </section>
    </div>

    <!-- ========================================================================= -->
    <!-- 3 & 5. EVOLUCIÓN MENSUAL & CAPACITACIONES VENCIDAS O PRÓXIMAS A VENCER     -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">

        <!-- 3. EVOLUCIÓN MENSUAL DE CAPACITACIONES (GRÁFICA) -->
        <section class="bg-white rounded-3xl border border-line/80 shadow-2xs p-5 sm:p-7 space-y-4 lg:col-span-7 xl:col-span-8 flex flex-col justify-between" aria-labelledby="t-evolucion">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-line/60 pb-3.5">
                <div>
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 text-brand-blue text-xs font-bold mb-1">
                        <i class="fa-solid fa-chart-column"></i>
                        <span>Tendencia y Actividad</span>
                    </div>
                    <h2 id="t-evolucion" class="font-heading font-extrabold text-lg text-brand-dark">Evolución mensual de capacitaciones</h2>
                    <p class="text-xs text-muted mt-0.5">Comparativa histórica de asignaciones realizadas, colaboradores participantes y cursos completados por mes.</p>
                </div>
                <div class="flex items-center gap-2 text-xs">
                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-lg">
                        <i class="fa-regular fa-calendar"></i> Últimos 6 meses
                    </span>
                </div>
            </div>

            <!-- Contenedor Canvas Gráfica -->
            <div class="relative w-full h-72 sm:h-80 pt-2">
                <canvas id="chartEvolucionMensual"></canvas>
            </div>

            <!-- Resumen de tarjetas numéricas del periodo -->
            <div class="grid grid-cols-3 gap-2 pt-2 border-t border-line/60 text-center">
                <div class="bg-slate-50 rounded-2xl p-3 border border-line/50">
                    <span class="block text-[10px] font-bold uppercase text-muted truncate">Cursos Asignados</span>
                    <span class="font-heading font-extrabold text-base text-brand-blue">{{ collect($evolucionMensual)->sum('asignadas') }}</span>
                </div>
                <div class="bg-slate-50 rounded-2xl p-3 border border-line/50">
                    <span class="block text-[10px] font-bold uppercase text-muted truncate">Participaciones</span>
                    <span class="font-heading font-extrabold text-base text-indigo-700">{{ collect($evolucionMensual)->sum('participantes') }}</span>
                </div>
                <div class="bg-slate-50 rounded-2xl p-3 border border-line/50">
                    <span class="block text-[10px] font-bold uppercase text-muted truncate">Cursos Finalizados</span>
                    <span class="font-heading font-extrabold text-base text-emerald-600">{{ collect($evolucionMensual)->sum('completadas') }}</span>
                </div>
            </div>
        </section>

        <!-- 5 (Punto 6). CAPACITACIONES VENCIDAS O PRÓXIMAS A VENCER -->
        <section class="bg-white rounded-3xl border border-line/80 shadow-2xs p-5 sm:p-7 space-y-4 lg:col-span-5 xl:col-span-4 flex flex-col justify-between" aria-labelledby="t-vencimientos">
            <div class="space-y-4">
                <div class="flex items-start justify-between gap-2 border-b border-line/60 pb-3.5">
                    <div>
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-50 text-rose-700 text-xs font-bold mb-1">
                            <i class="fa-solid fa-hourglass-half"></i>
                            <span>Control de Plazos</span>
                        </div>
                        <h2 id="t-vencimientos" class="font-heading font-extrabold text-base sm:text-lg text-brand-dark">Capacitaciones vencidas o próximas</h2>
                        <p class="text-xs text-muted mt-0.5">Control de fechas límite de cumplimiento y plazos de renovación.</p>
                    </div>
                    <div class="w-10 h-10 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-lg shrink-0">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                </div>

                <!-- Resumen de contadores de vencimiento -->
                <div class="grid grid-cols-2 gap-2.5">
                    <a href="{{ route('admin.capacitaciones.index', ['estado' => 'vencidas']) }}"
                       class="block p-3 rounded-2xl {{ $vencimientos['vencidas_count'] > 0 ? 'bg-red-50 text-red-800 border border-red-200 hover:bg-red-100/90 hover:shadow-xs' : 'bg-slate-50 text-slate-600 border border-line/60' }} space-y-1 transition-all cursor-pointer group"
                       title="Ver capacitaciones vencidas en catálogo">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] uppercase font-bold tracking-wider">Vencidas</span>
                            <i class="fa-solid fa-arrow-up-right-from-square {{ $vencimientos['vencidas_count'] > 0 ? 'text-red-500 group-hover:translate-x-0.5' : 'text-slate-400' }} text-xs transition-transform"></i>
                        </div>
                        <span class="font-heading font-black text-2xl block">{{ $vencimientos['vencidas_count'] }}</span>
                        <span class="text-[10px] block opacity-80 leading-tight truncate">{{ $vencimientos['asignaciones_vencidas_count'] }} fuera de plazo</span>
                    </a>

                    <a href="{{ route('admin.capacitaciones.index', ['estado' => 'proximas']) }}"
                       class="block p-3 rounded-2xl {{ $vencimientos['proximas_count'] > 0 ? 'bg-amber-50 text-amber-800 border border-amber-200 hover:bg-amber-100/90 hover:shadow-xs' : 'bg-slate-50 text-slate-600 border border-line/60' }} space-y-1 transition-all cursor-pointer group"
                       title="Ver capacitaciones próximas a vencer en catálogo">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] uppercase font-bold tracking-wider">Próximas (30d)</span>
                            <i class="fa-solid fa-arrow-up-right-from-square {{ $vencimientos['proximas_count'] > 0 ? 'text-amber-500 group-hover:translate-x-0.5' : 'text-slate-400' }} text-xs transition-transform"></i>
                        </div>
                        <span class="font-heading font-black text-2xl block">{{ $vencimientos['proximas_count'] }}</span>
                        <span class="text-[10px] block opacity-80 leading-tight truncate">{{ $vencimientos['asignaciones_proximas_count'] }} en riesgo</span>
                    </a>
                </div>

                <!-- Listado de capacitaciones que requieren atención -->
                <div class="space-y-2 pt-1">
                    <span class="block text-[11px] font-bold text-slate-700 uppercase tracking-wide">Cursos con alerta de fecha:</span>
                    
                    @if($vencimientos['vencidas']->isNotEmpty() || $vencimientos['proximas']->isNotEmpty())
                        <div class="space-y-2 max-h-56 overflow-y-auto pr-1">
                            @foreach($vencimientos['vencidas'] as $capVenc)
                                <a href="{{ route('admin.capacitaciones.index', ['estado' => 'vencidas', 'q' => $capVenc->titulo]) }}"
                                   class="p-2.5 rounded-xl bg-red-50/70 hover:bg-red-100/80 border border-red-200/80 text-xs flex items-center justify-between gap-2 transition-colors block group"
                                   title="Filtrar esta capacitación vencida">
                                    <div class="min-w-0">
                                        <p class="font-bold text-red-950 truncate group-hover:text-red-700 transition-colors">{{ $capVenc->titulo }}</p>
                                        <span class="text-[10px] text-red-700 flex items-center gap-1 font-semibold">
                                            <i class="fa-regular fa-calendar-xmark text-[10px]"></i> Venció: {{ \Carbon\Carbon::parse($capVenc->fecha_limite)->format('d/m/Y') }}
                                        </span>
                                    </div>
                                    <span class="px-2 py-0.5 rounded-full bg-red-100 text-red-800 text-[10px] font-bold shrink-0">Vencida</span>
                                </a>
                            @endforeach

                            @foreach($vencimientos['proximas'] as $capProx)
                                <a href="{{ route('admin.capacitaciones.index', ['estado' => 'proximas', 'q' => $capProx->titulo]) }}"
                                   class="p-2.5 rounded-xl bg-amber-50/70 hover:bg-amber-100/80 border border-amber-200/80 text-xs flex items-center justify-between gap-2 transition-colors block group"
                                   title="Ver esta capacitación próxima a vencer">
                                    <div class="min-w-0">
                                        <p class="font-bold text-amber-950 truncate group-hover:text-amber-700 transition-colors">{{ $capProx->titulo }}</p>
                                        <span class="text-[10px] text-amber-700 flex items-center gap-1 font-semibold">
                                            <i class="fa-regular fa-clock text-[10px]"></i> Límite: {{ \Carbon\Carbon::parse($capProx->fecha_limite)->format('d/m/Y') }} (en {{ (int) \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($capProx->fecha_limite)) }} días)
                                        </span>
                                    </div>
                                    <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[10px] font-bold shrink-0">Por vencer</span>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="p-4 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200 text-center text-xs">
                            <i class="fa-solid fa-shield-check text-lg text-emerald-600 mb-1 block"></i>
                            No hay capacitaciones vencidas ni próximas a vencer. Todos los plazos están al día.
                        </div>
                    @endif
                </div>
            </div>

            <div class="pt-3 border-t border-line/60 flex flex-wrap items-center justify-between gap-2 text-xs">
                <span class="text-[11px] text-muted">
                    {{ $vencimientos['al_dia_count'] }} con plazo vigente · {{ $vencimientos['sin_limite_count'] }} sin límite
                </span>
                <div class="flex items-center gap-2">
                    @if($vencimientos['vencidas_count'] > 0)
                        <a href="{{ route('admin.capacitaciones.index', ['estado' => 'vencidas']) }}" 
                           class="font-bold text-rose-700 hover:text-rose-900 hover:underline text-[11px] inline-flex items-center gap-1">
                            <span>Ver vencidas ({{ $vencimientos['vencidas_count'] }})</span>
                            <i class="fa-solid fa-arrow-right text-[9px]"></i>
                        </a>
                    @endif
                    @if($vencimientos['proximas_count'] > 0)
                        <a href="{{ route('admin.capacitaciones.index', ['estado' => 'proximas']) }}" 
                           class="font-bold text-amber-700 hover:text-amber-900 hover:underline text-[11px] inline-flex items-center gap-1">
                            <span>Ver próximas ({{ $vencimientos['proximas_count'] }})</span>
                            <i class="fa-solid fa-arrow-right text-[9px]"></i>
                        </a>
                    @endif
                    @if($vencimientos['vencidas_count'] == 0 && $vencimientos['proximas_count'] == 0)
                        <a href="{{ route('admin.capacitaciones.index', ['estado' => '1']) }}" 
                           class="font-bold text-brand-blue hover:text-brand-deep hover:underline text-[11px] inline-flex items-center gap-1">
                            <span>Ver catálogo</span>
                            <i class="fa-solid fa-arrow-right text-[9px]"></i>
                        </a>
                    @endif
                </div>
            </div>
        </section>
    </div>

    <!-- ========================================================================= -->
    <!-- ESTRUCTURA POR SEDES: AVANCE Y ESTADÍSTICAS POR SEDES Y ÁREAS             -->
    <!-- ========================================================================= -->
    <section class="bg-white rounded-3xl border border-line/80 shadow-2xs p-5 sm:p-7 space-y-6 w-full" aria-labelledby="t-sedes-stats">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-line/60 pb-4">
            <div>
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 text-xs font-bold mb-1">
                    <i class="fa-solid fa-building-shield"></i>
                    <span>Estructura Institucional</span>
                </div>
                <h2 id="t-sedes-stats" class="font-heading font-extrabold text-lg sm:text-xl text-brand-dark">Avance y Estadísticas Institucionales</h2>
                <p class="text-xs text-muted mt-0.5">Seguimiento formativo y cumplimiento alternable entre sedes y departamentos para una lectura limpia.</p>
            </div>

            <!-- CONTROLES INTERACTIVOS: ALTERNAR VISTA DE SEDES O ÁREAS -->
            <div class="flex flex-wrap items-center gap-2.5">
                <div class="inline-flex p-1 bg-slate-100 rounded-2xl border border-line/60 shadow-2xs">
                    <button type="button" id="btn-vista-sedes" onclick="alternarVistaEstructura('sedes')"
                            class="px-3.5 py-1.5 rounded-xl text-xs font-heading font-bold transition-all flex items-center gap-2 bg-white text-brand-dark shadow-xs cursor-pointer">
                        <i class="fa-solid fa-building text-brand-blue"></i>
                        <span>Vista por Sedes</span>
                        <span class="px-1.5 py-0.5 rounded-md bg-brand-light text-brand-blue text-[10px] font-extrabold">{{ $porSede->count() }}</span>
                    </button>
                    <button type="button" id="btn-vista-areas" onclick="alternarVistaEstructura('areas')"
                            class="px-3.5 py-1.5 rounded-xl text-xs font-heading font-bold transition-all flex items-center gap-2 text-muted hover:text-slate-700 cursor-pointer">
                        <i class="fa-solid fa-sitemap text-indigo-500"></i>
                        <span>Vista por Áreas</span>
                        <span class="px-1.5 py-0.5 rounded-md bg-slate-200/70 text-slate-600 text-[10px] font-extrabold">{{ $porArea->count() }}</span>
                    </button>
                </div>

                <a href="{{ route('admin.sedes.index') }}" class="text-xs font-bold text-brand-blue hover:underline hidden xl:flex items-center gap-1 ml-1">
                    <span>Gestionar sedes</span>
                    <i class="fa-solid fa-chevron-right text-[10px]"></i>
                </a>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- VISTA A: TARJETAS COMPACTAS POR SEDES                                     -->
        <!-- ========================================================================= -->
        <div id="vista-contenedor-sedes" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-2 2xl:grid-cols-4 gap-6 items-stretch">
                @forelse ($porSede as $sedeItem)
                    <div class="bg-slate-50/70 rounded-3xl border border-line/80 hover:border-brand-blue/50 transition-all p-5 flex flex-col justify-between space-y-4 shadow-2xs hover:shadow-md">
                        <!-- Cabecera de la Sede -->
                        <div class="space-y-2">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-10 h-10 rounded-2xl bg-brand-light text-brand-blue flex items-center justify-center font-bold text-sm shrink-0">
                                        <i class="fa-solid fa-building"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-heading font-extrabold text-sm sm:text-base text-brand-dark leading-snug">{{ $sedeItem->nombre }}</h3>
                                        <span class="text-[11px] text-muted flex items-center gap-1 font-semibold">
                                            <i class="fa-solid fa-location-dot text-brand-blue/80 text-[10px]"></i> {{ $sedeItem->ciudad }}
                                        </span>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $sedeItem->estado ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $sedeItem->estado ? 'Activa' : 'Inactiva' }}
                                </span>
                            </div>

                            @if($sedeItem->direccion)
                                <p class="text-[11px] text-slate-500 truncate"><i class="fa-solid fa-map-pin text-[10px] text-slate-400 mr-1"></i>{{ $sedeItem->direccion }}</p>
                            @endif
                        </div>

                        <!-- Barra de Avance Promedio de la Sede -->
                        <div class="bg-white p-4 rounded-2xl border border-line/60 space-y-2">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-bold text-slate-700">Avance Global</span>
                                <span class="font-heading font-extrabold text-lg text-brand-blue">{{ $sedeItem->promedio }}%</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden">
                                <div class="bg-gradient-to-r from-brand-blue via-brand-sky to-cyan-400 h-3 rounded-full transition-all duration-700"
                                     style="width: {{ $sedeItem->promedio }}%"></div>
                            </div>
                            <div class="grid grid-cols-3 gap-1 pt-1 text-center">
                                <div class="p-1.5 rounded-xl bg-emerald-50 text-emerald-800">
                                    <span class="block font-heading font-extrabold text-xs leading-tight">{{ $sedeItem->completadas }}</span>
                                    <span class="text-[9px] uppercase font-bold text-emerald-700">Completas</span>
                                </div>
                                <div class="p-1.5 rounded-xl bg-blue-50 text-brand-blue">
                                    <span class="block font-heading font-extrabold text-xs leading-tight">{{ $sedeItem->en_progreso }}</span>
                                    <span class="text-[9px] uppercase font-bold text-brand-blue">En curso</span>
                                </div>
                                <div class="p-1.5 rounded-xl bg-slate-100 text-slate-700">
                                    <span class="block font-heading font-extrabold text-xs leading-tight">{{ $sedeItem->pendientes }}</span>
                                    <span class="text-[9px] uppercase font-bold text-slate-500">Pendientes</span>
                                </div>
                            </div>
                        </div>

                        <!-- Totales de Empleados y Certificados -->
                        <div class="flex items-center justify-between text-xs text-slate-600 px-1">
                            <span class="flex items-center gap-1.5">
                                <i class="fa-solid fa-users text-slate-400"></i>
                                <strong>{{ $sedeItem->colaboradores_count }}</strong> empleados
                            </span>
                            <span class="flex items-center gap-1.5">
                                <i class="fa-solid fa-award text-amber-500"></i>
                                <strong>{{ $sedeItem->certificados_count }}</strong> certificados
                            </span>
                        </div>

                        <!-- Acciones Rápidas de la Sede -->
                        <div class="pt-3 border-t border-line/60 flex items-center justify-between text-xs">
                            <button type="button" onclick="filtrarAreasYCambiarVista('{{ $sedeItem->id }}')" 
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-heading font-bold text-xs transition-colors cursor-pointer">
                                <i class="fa-solid fa-sitemap text-[10px]"></i>
                                <span>Ver {{ $sedeItem->areas_count }} áreas</span>
                            </button>
                            <a href="{{ route('admin.progreso.index', ['sede_id' => $sedeItem->id]) }}" 
                               class="inline-flex items-center gap-1 font-bold text-brand-blue hover:underline">
                                <span>Ver progreso</span>
                                <i class="fa-solid fa-arrow-right text-[9px]"></i>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full p-8 text-center bg-slate-50 rounded-2xl">
                        <i class="fa-solid fa-building-circle-exclamation text-3xl text-slate-300 mb-2"></i>
                        <p class="text-xs text-muted">No se registran sedes en la plataforma.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- VISTA B: LISTADO COMPLETO Y FILTRABLE DE ÁREAS                             -->
        <!-- ========================================================================= -->
        <div id="vista-contenedor-areas" class="space-y-4 hidden">
            <!-- Barra de filtro por sede -->
            <div class="flex flex-wrap items-center gap-2 p-3 bg-slate-50/80 rounded-2xl border border-line/60">
                <span class="text-xs font-bold text-slate-700 flex items-center gap-1.5 mr-1">
                    <i class="fa-solid fa-filter text-slate-400 text-[10px]"></i>
                    <span>Filtrar por sede:</span>
                </span>
                <button type="button" onclick="filtrarAreasPorSede('todas', this)" 
                        class="btn-filtro-sede active px-3 py-1 rounded-xl text-xs font-bold bg-brand-blue text-white shadow-2xs transition-colors cursor-pointer">
                    Todas ({{ $porArea->count() }})
                </button>
                @foreach ($porSede as $sItem)
                    <button type="button" onclick="filtrarAreasPorSede('{{ $sItem->id }}', this)" 
                            data-filtro-sede-id="{{ $sItem->id }}"
                            class="btn-filtro-sede px-3 py-1 rounded-xl text-xs font-semibold bg-white border border-line text-slate-700 hover:border-brand-blue hover:text-brand-blue transition-colors cursor-pointer">
                        {{ $sItem->nombre }} ({{ $sItem->areas_detalle->count() }})
                    </button>
                @endforeach
            </div>

            <!-- Grilla de Tarjetas de Áreas -->
            <div id="grilla-tarjetas-areas" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 items-stretch">
                @forelse ($porArea as $area)
                    <div class="tarjeta-area bg-slate-50/70 rounded-2xl border border-line/80 hover:border-brand-blue/40 hover:bg-white p-4 flex flex-col justify-between space-y-3 shadow-2xs hover:shadow-sm transition-all"
                         data-sede-id="{{ $area->sede_id ?? '' }}">
                        <div class="space-y-2">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-700 text-[10px] font-bold border border-indigo-100 mb-1">
                                        <i class="fa-solid fa-building text-[9px]"></i>
                                        {{ $area->sede_nombre }}
                                    </span>
                                    <h3 class="font-heading font-extrabold text-sm text-brand-dark truncate leading-tight" title="{{ $area->nombre }}">
                                        {{ $area->nombre }}
                                    </h3>
                                </div>
                                <span class="font-heading font-black text-sm shrink-0 {{ $area->promedio >= 80 ? 'text-emerald-600' : ($area->promedio >= 50 ? 'text-brand-blue' : 'text-amber-600') }}">
                                    {{ $area->promedio }}%
                                </span>
                            </div>

                            <div class="w-full bg-slate-200/70 rounded-full h-2 overflow-hidden">
                                <div class="h-2 rounded-full transition-all duration-500 {{ $area->promedio >= 80 ? 'bg-gradient-to-r from-emerald-500 to-teal-400' : ($area->promedio >= 50 ? 'bg-gradient-to-r from-brand-blue to-brand-sky' : 'bg-gradient-to-r from-amber-500 to-orange-400') }}"
                                     style="width: {{ $area->promedio }}%"></div>
                            </div>

                            <div class="flex items-center justify-between text-[11px] text-muted">
                                <span>{{ $area->empleados }} colaboradores</span>
                                <span>{{ $area->completadas }}/{{ $area->asignaciones }} cursos</span>
                            </div>
                        </div>

                        <div class="pt-2 border-t border-line/60 flex items-center justify-between text-xs">
                            <span class="text-[10px] text-slate-500 font-medium">Cumplimiento formativo</span>
                            <a href="{{ route('admin.progreso.index', ['sede_id' => $area->sede_id, 'area_id' => $area->id]) }}" 
                               class="inline-flex items-center gap-1 font-bold text-brand-blue hover:underline text-[11px]">
                                <span>Ver progreso</span>
                                <i class="fa-solid fa-arrow-right text-[8px]"></i>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full p-8 text-center bg-slate-50 rounded-2xl">
                        <i class="fa-solid fa-sitemap text-3xl text-slate-300 mb-2"></i>
                        <p class="text-xs text-muted">No hay áreas adscritas registradas.</p>
                    </div>
                @endforelse
            </div>
            <div id="areas-vacio-aviso" class="hidden p-8 text-center bg-slate-50 rounded-2xl border border-line/60">
                <i class="fa-solid fa-filter-circle-xmark text-3xl text-slate-300 mb-2"></i>
                <p class="text-xs text-muted font-semibold">No se encontraron áreas asociadas a la sede seleccionada.</p>
            </div>
        </div>
    </section>

    <!-- ========================================================================= -->
    <!-- ESTADÍSTICAS ORIGINALES: CAPACITACIONES CON MÁS PARTICIPANTES Y ACTIVIDAD -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-stretch">

        <!-- CAPACITACIONES CON MÁS PARTICIPACIÓN -->
        <section class="bg-white rounded-3xl border border-line/80 shadow-2xs overflow-hidden" aria-labelledby="t-caps">
            <div class="px-5 sm:px-6 py-4 border-b border-line/60 flex items-center justify-between">
                <h2 id="t-caps" class="font-heading font-bold text-base text-brand-dark">Capacitaciones con más participantes</h2>
                <a href="{{ route('admin.capacitaciones.index') }}" class="text-xs font-bold text-brand-blue hover:underline">Ver todas</a>
            </div>
            <ul class="divide-y divide-line/50">
                @forelse ($porCapacitacion as $fila)
                    <li class="px-5 sm:px-6 py-3.5 flex items-center gap-4 hover:bg-slate-50/60 transition-colors">
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-bold text-brand-dark truncate">{{ $fila->capacitacion->titulo }}</p>
                            <p class="text-[11px] text-muted">{{ $fila->participantes }} asignados · {{ $fila->completadas }} completadas</p>
                        </div>
                        <div class="w-28 shrink-0">
                            <div class="flex justify-end text-[11px] font-bold text-brand-blue mb-1">{{ $fila->promedio }}%</div>
                            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden"><div class="bg-brand-blue h-2 rounded-full" style="width: {{ $fila->promedio }}%"></div></div>
                        </div>
                    </li>
                @empty
                    <li class="px-6 py-8 text-center text-xs text-muted">Sin datos todavía.</li>
                @endforelse
            </ul>
        </section>

        <!-- ACTIVIDAD RECIENTE -->
        <section class="bg-white rounded-3xl border border-line/80 shadow-2xs overflow-hidden" aria-labelledby="t-act">
            <div class="px-5 sm:px-6 py-4 border-b border-line/60 flex items-center justify-between">
                <h2 id="t-act" class="font-heading font-bold text-base text-brand-dark">Actividad reciente</h2>
                <a href="{{ route('admin.certificados.index') }}" class="text-xs font-bold text-brand-blue hover:underline">Certificados</a>
            </div>
            <ul class="divide-y divide-line/50">
                @foreach ($ultimosIntentos as $intento)
                    <li class="px-5 sm:px-6 py-3 flex items-center gap-3 hover:bg-slate-50/60 transition-colors">
                        <span class="w-9 h-9 rounded-xl flex items-center justify-center text-sm shrink-0 {{ $intento->estado === 'APROBADO' ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-500' }}">
                            <i class="fa-solid {{ $intento->estado === 'APROBADO' ? 'fa-check' : 'fa-xmark' }}"></i>
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-bold text-brand-dark truncate">{{ $intento->usuario->nombre_completo ?? 'Usuario' }}</p>
                            <p class="text-[11px] text-muted truncate">{{ $intento->evaluacion->titulo ?? 'Evaluación' }} · {{ rtrim(rtrim(number_format((float) $intento->porcentaje, 1), '0'), '.') }}%</p>
                        </div>
                        <span class="text-[10px] text-muted shrink-0">{{ ($intento->fecha_finalizacion ?? $intento->fecha_inicio)?->diffForHumans() }}</span>
                    </li>
                @endforeach
                @foreach ($ultimosCertificados as $cert)
                    <li class="px-5 sm:px-6 py-3 flex items-center gap-3 hover:bg-slate-50/60 transition-colors">
                        <span class="w-9 h-9 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center text-sm shrink-0"><i class="fa-solid fa-award"></i></span>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-bold text-brand-dark truncate">{{ $cert->nombre_empleado }}</p>
                            <p class="text-[11px] text-muted truncate">Certificado · {{ $cert->nombre_capacitacion }}</p>
                        </div>
                        <span class="text-[10px] text-muted shrink-0">{{ $cert->fecha_emision?->diffForHumans() }}</span>
                    </li>
                @endforeach
                @if ($ultimosIntentos->isEmpty() && $ultimosCertificados->isEmpty())
                    <li class="px-6 py-8 text-center text-xs text-muted">Aún no hay actividad registrada.</li>
                @endif
            </ul>
        </section>
    </div>
</div>

<!-- ========================================================================= -->
<!-- SCRIPTS: CHART.JS & INTERACTIVIDAD DEL DASHBOARD                          -->
<!-- ========================================================================= -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>

    // 2. Alternar vista interactiva entre Sedes y Áreas
    function alternarVistaEstructura(vista) {
        const contSedes = document.getElementById('vista-contenedor-sedes');
        const contAreas = document.getElementById('vista-contenedor-areas');
        const btnSedes = document.getElementById('btn-vista-sedes');
        const btnAreas = document.getElementById('btn-vista-areas');

        if (!contSedes || !contAreas || !btnSedes || !btnAreas) return;

        if (vista === 'sedes') {
            contSedes.classList.remove('hidden');
            contAreas.classList.add('hidden');
            btnSedes.className = "px-3.5 py-1.5 rounded-xl text-xs font-heading font-bold transition-all flex items-center gap-2 bg-white text-brand-dark shadow-xs cursor-pointer";
            btnAreas.className = "px-3.5 py-1.5 rounded-xl text-xs font-heading font-bold transition-all flex items-center gap-2 text-muted hover:text-slate-700 cursor-pointer";
        } else {
            contSedes.classList.add('hidden');
            contAreas.classList.remove('hidden');
            btnAreas.className = "px-3.5 py-1.5 rounded-xl text-xs font-heading font-bold transition-all flex items-center gap-2 bg-white text-brand-dark shadow-xs cursor-pointer";
            btnSedes.className = "px-3.5 py-1.5 rounded-xl text-xs font-heading font-bold transition-all flex items-center gap-2 text-muted hover:text-slate-700 cursor-pointer";
        }
    }

    // 3. Filtrar tarjetas de áreas por sede
    function filtrarAreasPorSede(sedeId, btnElement) {
        const botones = document.querySelectorAll('.btn-filtro-sede');
        botones.forEach(b => {
            b.className = "btn-filtro-sede px-3 py-1 rounded-xl text-xs font-semibold bg-white border border-line text-slate-700 hover:border-brand-blue hover:text-brand-blue transition-colors cursor-pointer";
        });
        if (btnElement) {
            btnElement.className = "btn-filtro-sede active px-3 py-1 rounded-xl text-xs font-bold bg-brand-blue text-white shadow-2xs transition-colors cursor-pointer";
        }

        const tarjetas = document.querySelectorAll('.tarjeta-area');
        let visibles = 0;
        tarjetas.forEach(t => {
            const areaSedeId = t.getAttribute('data-sede-id');
            if (sedeId === 'todas' || areaSedeId == sedeId) {
                t.classList.remove('hidden');
                visibles++;
            } else {
                t.classList.add('hidden');
            }
        });

        const avisoVacio = document.getElementById('areas-vacio-aviso');
        if (avisoVacio) {
            if (visibles === 0) {
                avisoVacio.classList.remove('hidden');
            } else {
                avisoVacio.classList.add('hidden');
            }
        }
    }

    // 4. Acceso directo desde la tarjeta de Sede para cambiar de vista y filtrar
    function filtrarAreasYCambiarVista(sedeId) {
        alternarVistaEstructura('areas');
        const targetBtn = document.querySelector(`.btn-filtro-sede[data-filtro-sede-id="${sedeId}"]`);
        if (targetBtn) {
            filtrarAreasPorSede(sedeId, targetBtn);
        } else {
            filtrarAreasPorSede(sedeId, null);
        }
    }

    // 4. Renderizado de Gráfica Chart.js: Evolución Mensual
    document.addEventListener('DOMContentLoaded', function () {
        const canvas = document.getElementById('chartEvolucionMensual');
        if (!canvas) return;

        const datosEvolucion = @json($evolucionMensual);
        const etiquetas = datosEvolucion.map(d => d.mes);
        const asignadas = datosEvolucion.map(d => d.asignadas);
        const participantes = datosEvolucion.map(d => d.participantes);
        const completadas = datosEvolucion.map(d => d.completadas);

        new Chart(canvas, {
            type: 'bar',
            data: {
                labels: etiquetas,
                datasets: [
                    {
                        label: 'Capacitaciones asignadas',
                        data: asignadas,
                        backgroundColor: 'rgba(2, 132, 199, 0.85)', // brand-blue
                        borderColor: '#0284c7',
                        borderWidth: 1.5,
                        borderRadius: 6,
                    },
                    {
                        label: 'Empleados participantes',
                        data: participantes,
                        backgroundColor: 'rgba(79, 70, 229, 0.85)', // indigo
                        borderColor: '#4f46e5',
                        borderWidth: 1.5,
                        borderRadius: 6,
                    },
                    {
                        label: 'Capacitaciones completadas',
                        data: completadas,
                        backgroundColor: 'rgba(16, 185, 129, 0.85)', // emerald
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
                            font: { family: 'DM Sans, sans-serif', size: 11, weight: '600' },
                            padding: 16
                        }
                    },
                    tooltip: {
                        padding: 10,
                        boxPadding: 4,
                        cornerRadius: 8,
                        titleFont: { family: 'DM Sans, sans-serif', size: 12, weight: 'bold' },
                        bodyFont: { family: 'DM Sans, sans-serif', size: 11 }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'DM Sans, sans-serif', size: 11 } }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0, font: { family: 'DM Sans, sans-serif', size: 11 } },
                        grid: { color: 'rgba(226, 232, 240, 0.6)' }
                    }
                }
            }
        });
    });
</script>
@endsection
