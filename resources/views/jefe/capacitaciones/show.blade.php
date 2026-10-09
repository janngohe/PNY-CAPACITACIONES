@extends('layouts.jefe')

@section('title', 'Participantes · ' . $capacitacion->titulo)
@section('page_title', 'Participantes')

@php
    $estados = [
        'PENDIENTE' => ['Sin iniciar', 'bg-slate-100 text-slate-600'],
        'EN_PROGRESO' => ['En progreso', 'bg-amber-100 text-amber-700'],
        'MODULOS_COMPLETOS' => ['Módulos completos', 'bg-sky-100 text-sky-700'],
        'COMPLETADA' => ['Aprobada', 'bg-emerald-100 text-emerald-700'],
        'NO_APROBADA' => ['No aprobada', 'bg-red-100 text-red-700'],
    ];
    $activa = (bool) $capacitacion->estado;
@endphp

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <!-- Encabezado -->
    <div class="relative rounded-3xl bg-gradient-to-r from-brand-dark via-brand-deep to-brand-blue text-white p-6 sm:p-8 overflow-hidden shadow-md">
        <div class="absolute -right-10 -top-10 w-56 h-56 rounded-full bg-brand-sky/15 blur-2xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="min-w-0">
                <a href="{{ route('jefe.capacitaciones.index') }}" class="inline-flex items-center gap-1.5 text-[11px] font-bold text-brand-sky hover:text-white transition-colors mb-2">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
                    Volver a capacitaciones
                </a>
                <div class="flex flex-wrap items-center gap-2 mb-1.5">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $activa ? 'bg-emerald-500/90' : 'bg-slate-600/90' }}">
                        {{ $activa ? 'Activa' : 'Desactivada' }}
                    </span>
                    @foreach ($capacitacion->areas as $area)
                        <span class="px-2.5 py-0.5 rounded-full bg-white/10 border border-white/20 text-[10px] font-bold text-brand-sky">{{ $area->nombre }}</span>
                    @endforeach
                </div>
                <h1 class="font-heading font-extrabold text-xl sm:text-2xl md:text-3xl leading-tight">{{ $capacitacion->titulo }}</h1>
                <p class="mt-1 text-xs sm:text-sm text-slate-200/90">{{ $modulos->count() }} {{ $modulos->count() === 1 ? 'módulo activo' : 'módulos activos' }} · progreso de los empleados del área</p>
            </div>
            <a href="{{ route('jefe.capacitaciones.edit', $capacitacion) }}"
               class="self-start md:self-auto inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white text-brand-blue hover:bg-brand-light font-heading font-bold text-xs sm:text-sm shadow-xs transition-colors">
                Editar capacitación
            </a>
        </div>
        <div class="absolute bottom-0 left-0 w-full overflow-hidden leading-none pointer-events-none opacity-30">
            <svg class="block w-full h-4 text-white" viewBox="0 0 1200 40" preserveAspectRatio="none"><path d="M0,0 C150,35 350,10 500,25 C650,40 850,5 1000,20 C1100,30 1160,15 1200,25 L1200,40 L0,40 Z" fill="currentColor"/></svg>
        </div>
    </div>

    <!-- KPIs -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
        @foreach ([
            ['Empleados del área', $resumen['empleados'], 'text-brand-dark', 'Con acceso'],
            ['Han iniciado', $resumen['iniciaron'], 'text-amber-600', 'Con avance registrado'],
            ['Aprobaron', $resumen['completaron'], 'text-emerald-600', 'Completaron la capacitación'],
            ['Progreso promedio', $resumen['promedio'] . '%', 'text-brand-blue', 'Del área'],
        ] as [$titulo, $valor, $color, $nota])
            <div class="bg-white p-4 sm:p-5 rounded-2xl border border-line/70 shadow-2xs">
                <span class="text-xs font-semibold text-muted">{{ $titulo }}</span>
                <p class="mt-2 text-2xl font-heading font-extrabold {{ $color }}">{{ $valor }}</p>
                <p class="text-[11px] text-muted mt-0.5">{{ $nota }}</p>
            </div>
        @endforeach
    </div>

    <!-- Tabla de participantes -->
    <section class="bg-white rounded-3xl border border-line/80 shadow-2xs overflow-hidden" aria-labelledby="titulo-participantes">
        <div class="px-5 sm:px-6 py-4 border-b border-line/60">
            <h2 id="titulo-participantes" class="font-heading font-bold text-base text-brand-dark">Participantes y progreso</h2>
            <p class="text-xs text-muted">Solo aparecen los empleados activos del área para la cual publicaste esta capacitación.</p>
        </div>

        @if ($participantes->isEmpty())
            <div class="p-10 text-center text-xs text-muted">No hay empleados activos registrados en el área de esta capacitación.</div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-muted">
                        <tr>
                            <th class="px-5 sm:px-6 py-3 font-bold">Empleado</th>
                            <th class="px-4 py-3 font-bold min-w-[180px]">Progreso</th>
                            <th class="px-4 py-3 font-bold">Estado</th>
                            <th class="px-4 py-3 font-bold text-center">Mejor nota</th>
                            <th class="px-4 py-3 font-bold text-center">Certificado</th>
                            <th class="px-4 py-3 font-bold">Última actividad</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line/50">
                        @foreach ($participantes as $p)
                            @php [$etiqueta, $clase] = $estados[$p->estado] ?? [$p->estado, 'bg-slate-100 text-slate-600']; @endphp
                            <tr class="hover:bg-brand-light/40 transition-colors">
                                <td class="px-5 sm:px-6 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-brand-light text-brand-blue font-heading font-extrabold text-xs flex items-center justify-center shrink-0">
                                            {{ strtoupper(substr($p->usuario->nombre_completo, 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-bold text-brand-dark truncate">{{ $p->usuario->nombre_completo }}</p>
                                            <p class="text-[11px] text-muted">C.C. {{ $p->usuario->identificacion }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-2.5">
                                        <div class="flex-1 bg-slate-100 rounded-full h-2 overflow-hidden">
                                            <div class="bg-brand-blue h-2 rounded-full" style="width: {{ $p->porcentaje }}%"></div>
                                        </div>
                                        <span class="font-bold text-brand-blue w-9 text-right">{{ $p->porcentaje }}%</span>
                                    </div>
                                    <p class="text-[10px] text-muted mt-1">{{ $p->completados }} de {{ $modulos->count() }} módulos</p>
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] font-bold {{ $clase }}">{{ $etiqueta }}</span>
                                </td>
                                <td class="px-4 py-3.5 text-center font-bold text-brand-dark">
                                    {{ $p->mejor_nota !== null ? rtrim(rtrim(number_format((float) $p->mejor_nota, 1), '0'), '.') . '%' : '—' }}
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    @if ($p->certificado)
                                        <span class="inline-flex px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-[11px] font-bold">Emitido</span>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 text-muted">
                                    {{ $p->ultima_actividad ? \Illuminate\Support\Carbon::parse($p->ultima_actividad)->format('d/m/Y H:i') : '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</div>
@endsection
