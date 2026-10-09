@extends('layouts.admin')

@section('title', 'Resumen General')
@section('page_title', 'Resumen General')

@section('content')
@php
    $usuario = $usuario ?? auth()->user();
    $tarjetas = [
        ['Usuarios activos', $usuarios['activos'], 'de ' . $usuarios['total'] . ' registrados', 'fa-users', 'bg-blue-50 text-brand-blue'],
        ['Capacitaciones activas', $kpis['capacitaciones'], 'de ' . $kpis['capacitaciones_total'] . ' creadas', 'fa-book-open', 'bg-cyan-50 text-cyan-700'],
        ['Avance global', $kpis['avance_global'] . '%', $kpis['completadas'] . ' completadas', 'fa-chart-line', 'bg-emerald-50 text-emerald-700'],
        ['Aprobación en evaluaciones', $kpis['tasa_aprobacion'] . '%', $kpis['intentos'] . ' intentos', 'fa-clipboard-check', 'bg-amber-50 text-amber-700'],
        ['Certificados emitidos', $kpis['certificados'], 'en toda la plataforma', 'fa-award', 'bg-violet-50 text-violet-700'],
        ['Áreas activas', $kpis['areas'], $kpis['evaluaciones'] . ' evaluaciones activas', 'fa-sitemap', 'bg-rose-50 text-rose-700'],
    ];
@endphp
<div class="space-y-6 sm:space-y-8 max-w-7xl mx-auto">

    @include('admin._banner', [
        'insignia' => 'Administrador del sistema',
        'titulo' => '¡Bienvenido, ' . $usuario->nombre_completo . '!',
        'descripcion' => 'Visión general de la plataforma de inducción y capacitación: usuarios, formación, evaluaciones y certificados en un solo lugar.',
        'icono' => 'fa-fish-fins',
    ])

    <!-- ACCESOS RÁPIDOS -->
    <div class="flex flex-wrap gap-2.5">
        <a href="{{ route('admin.capacitaciones.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-brand-blue hover:bg-brand-deep text-white font-heading font-bold text-xs shadow-xs transition-colors">
            <i class="fa-solid fa-plus"></i> Nueva capacitación
        </a>
        <a href="{{ route('admin.usuarios.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-line hover:border-brand-blue hover:text-brand-blue text-slate-700 font-heading font-bold text-xs transition-colors">
            <i class="fa-solid fa-user-plus"></i> Registrar usuario
        </a>
        <a href="{{ route('admin.evaluaciones.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-line hover:border-brand-blue hover:text-brand-blue text-slate-700 font-heading font-bold text-xs transition-colors">
            <i class="fa-solid fa-clipboard-question"></i> Nueva evaluación
        </a>
        <a href="{{ route('admin.reportes.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-line hover:border-brand-blue hover:text-brand-blue text-slate-700 font-heading font-bold text-xs transition-colors">
            <i class="fa-solid fa-file-arrow-down"></i> Generar reportes
        </a>
    </div>

    <!-- INDICADORES -->
    <section aria-label="Indicadores generales" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
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

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        <!-- ESTADO DE LAS ASIGNACIONES -->
        <section class="bg-white rounded-3xl border border-line/80 shadow-2xs p-5 sm:p-6 space-y-4" aria-labelledby="t-estado">
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
            <div class="pt-3 border-t border-line/60 grid grid-cols-3 gap-2 text-center">
                <div class="bg-slate-50 rounded-xl p-2.5"><span class="block font-heading font-extrabold text-lg text-brand-dark">{{ $usuarios['empleados'] }}</span><span class="text-[10px] text-muted">Empleados</span></div>
                <div class="bg-slate-50 rounded-xl p-2.5"><span class="block font-heading font-extrabold text-lg text-brand-dark">{{ $usuarios['jefes'] }}</span><span class="text-[10px] text-muted">Jefes</span></div>
                <div class="bg-slate-50 rounded-xl p-2.5"><span class="block font-heading font-extrabold text-lg text-brand-dark">{{ $usuarios['admins'] }}</span><span class="text-[10px] text-muted">Admins</span></div>
            </div>
        </section>

        <!-- AVANCE POR ÁREA -->
        <section class="bg-white rounded-3xl border border-line/80 shadow-2xs p-5 sm:p-6 space-y-4 xl:col-span-2" aria-labelledby="t-areas">
            <div class="flex items-center justify-between">
                <h2 id="t-areas" class="font-heading font-bold text-base text-brand-dark">Avance promedio por área</h2>
                <a href="{{ route('admin.progreso.index') }}" class="text-xs font-bold text-brand-blue hover:underline">Ver detalle</a>
            </div>
            @forelse ($porArea as $area)
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-semibold text-slate-700">{{ $area->nombre }} <span class="text-muted font-normal">· {{ $area->completadas }}/{{ $area->asignaciones }} completadas</span></span>
                        <span class="font-bold text-brand-blue">{{ $area->promedio }}%</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                        <div class="bg-gradient-to-r from-brand-blue to-brand-sky h-2.5 rounded-full transition-all duration-500" style="width: {{ $area->promedio }}%"></div>
                    </div>
                </div>
            @empty
                <p class="text-xs text-muted py-6 text-center">Aún no hay capacitaciones asignadas a áreas con empleados activos.</p>
            @endforelse
        </section>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

        <!-- CAPACITACIONES CON MÁS PARTICIPACIÓN -->
        <section class="bg-white rounded-3xl border border-line/80 shadow-2xs overflow-hidden" aria-labelledby="t-caps">
            <div class="px-5 sm:px-6 py-4 border-b border-line/60 flex items-center justify-between">
                <h2 id="t-caps" class="font-heading font-bold text-base text-brand-dark">Capacitaciones con más participantes</h2>
                <a href="{{ route('admin.capacitaciones.index') }}" class="text-xs font-bold text-brand-blue hover:underline">Ver todas</a>
            </div>
            <ul class="divide-y divide-line/50">
                @forelse ($porCapacitacion as $fila)
                    <li class="px-5 sm:px-6 py-3.5 flex items-center gap-4">
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
            <div class="px-5 sm:px-6 py-4 border-b border-line/60">
                <h2 id="t-act" class="font-heading font-bold text-base text-brand-dark">Actividad reciente</h2>
            </div>
            <ul class="divide-y divide-line/50">
                @foreach ($ultimosIntentos as $intento)
                    <li class="px-5 sm:px-6 py-3 flex items-center gap-3">
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
                    <li class="px-5 sm:px-6 py-3 flex items-center gap-3">
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
@endsection
