@extends('layouts.' . ($panel ?? 'jefe'))

@section('title', 'Resultados · ' . $evaluacion->titulo)
@section('page_title', 'Resultados de Evaluación')

@php
    $estados = [
        'APROBADO' => ['Aprobado', 'bg-emerald-100 text-emerald-700'],
        'NO_APROBADO' => ['No aprobado', 'bg-red-100 text-red-700'],
        'SIN_PRESENTAR' => ['Sin presentar', 'bg-slate-100 text-slate-600'],
    ];
    $filtros = ['' => 'Todos', 'APROBADO' => 'Aprobados', 'NO_APROBADO' => 'No aprobados', 'SIN_PRESENTAR' => 'Sin presentar'];
    $formatoNota = fn ($n) => $n === null ? '—' : rtrim(rtrim(number_format((float) $n, 1), '0'), '.') . '%';
@endphp

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <div class="relative rounded-3xl bg-gradient-to-r from-brand-dark via-brand-deep to-brand-blue text-white p-6 sm:p-8 overflow-hidden shadow-md">
        <div class="absolute -right-10 -top-10 w-56 h-56 rounded-full bg-brand-sky/15 blur-2xl pointer-events-none"></div>
        <div class="relative z-10">
            <a href="{{ route(($panel ?? 'jefe') . '.resultados.index') }}" class="inline-flex items-center gap-1.5 text-[11px] font-bold text-brand-sky hover:text-white transition-colors mb-2">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
                Volver a resultados
            </a>
            <p class="text-[11px] uppercase font-bold tracking-wider text-brand-sky">{{ $evaluacion->capacitacion->titulo }}</p>
            <h1 class="font-heading font-extrabold text-xl sm:text-2xl md:text-3xl leading-tight">{{ $evaluacion->titulo }}</h1>
            <p class="mt-1 text-xs sm:text-sm text-slate-200/90">
                Aprobación mínima {{ rtrim(rtrim(number_format((float) $evaluacion->porcentaje_aprobacion, 2), '0'), '.') }}% · {{ $evaluacion->intentos_permitidos }} {{ $evaluacion->intentos_permitidos === 1 ? 'intento permitido' : 'intentos permitidos' }}
            </p>
        </div>
        <div class="absolute bottom-0 left-0 w-full overflow-hidden leading-none pointer-events-none opacity-30">
            <svg class="block w-full h-4 text-white" viewBox="0 0 1200 40" preserveAspectRatio="none"><path d="M0,0 C150,35 350,10 500,25 C650,40 850,5 1000,20 C1100,30 1160,15 1200,25 L1200,40 L0,40 Z" fill="currentColor"/></svg>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-5 gap-3 sm:gap-4">
        @foreach ([
            ['Empleados', $resumen['empleados'], 'text-brand-dark'],
            ['Presentaron', $resumen['presentaron'], 'text-brand-blue'],
            ['Aprobaron', $resumen['aprobaron'], 'text-emerald-600'],
            ['No aprobaron', $resumen['no_aprobaron'], 'text-red-600'],
            ['Nota promedio', $formatoNota($resumen['promedio']), 'text-amber-600'],
        ] as [$titulo, $valor, $color])
            <div class="bg-white p-4 sm:p-5 rounded-2xl border border-line/70 shadow-2xs">
                <span class="text-xs font-semibold text-muted">{{ $titulo }}</span>
                <p class="mt-2 text-2xl font-heading font-extrabold {{ $color }}">{{ $valor }}</p>
            </div>
        @endforeach
    </div>

    <section class="bg-white rounded-3xl border border-line/80 shadow-2xs overflow-hidden" aria-labelledby="titulo-detalle">
        <div class="px-5 sm:px-6 py-4 border-b border-line/60 flex flex-col md:flex-row md:items-center justify-between gap-3">
            <h2 id="titulo-detalle" class="font-heading font-bold text-base text-brand-dark">Detalle por empleado</h2>
            <div class="flex items-center gap-1.5 overflow-x-auto text-xs font-semibold">
                @foreach ($filtros as $clave => $etiqueta)
                    <a href="{{ $clave === '' ? route(($panel ?? 'jefe') . '.resultados.show', $evaluacion) : route(($panel ?? 'jefe') . '.resultados.show', [$evaluacion, 'estado' => $clave]) }}"
                       class="px-3 py-1.5 rounded-lg whitespace-nowrap transition-colors {{ ($filtro ?? '') === $clave ? 'bg-brand-blue text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-brand-light hover:text-brand-blue' }}">
                        {{ $etiqueta }}
                    </a>
                @endforeach
            </div>
        </div>

        @if ($filas->isEmpty())
            <div class="p-10 text-center text-xs text-muted">No hay empleados para mostrar con este filtro.</div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-muted">
                        <tr>
                            <th class="px-5 sm:px-6 py-3 font-bold">Empleado</th>
                            <th class="px-4 py-3 font-bold text-center">Intentos</th>
                            <th class="px-4 py-3 font-bold text-center">Mejor nota</th>
                            <th class="px-4 py-3 font-bold text-center">Último intento</th>
                            <th class="px-4 py-3 font-bold text-center">Resultado</th>
                            <th class="px-4 py-3 font-bold">Fecha</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line/50">
                        @foreach ($filas as $f)
                            @php [$etiqueta, $clase] = $estados[$f->estado]; @endphp
                            <tr class="hover:bg-brand-light/40 transition-colors">
                                <td class="px-5 sm:px-6 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-brand-light text-brand-blue font-heading font-extrabold text-xs flex items-center justify-center shrink-0">
                                            {{ strtoupper(substr($f->usuario->nombre_completo, 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-bold text-brand-dark truncate">{{ $f->usuario->nombre_completo }}</p>
                                            <p class="text-[11px] text-muted">C.C. {{ $f->usuario->identificacion }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    <span class="font-semibold text-brand-dark">{{ $f->intentos }}</span>
                                    <span class="text-muted">/ {{ $evaluacion->intentos_permitidos }}</span>
                                </td>
                                <td class="px-4 py-3.5 text-center font-bold text-brand-dark">{{ $formatoNota($f->mejor) }}</td>
                                <td class="px-4 py-3.5 text-center">{{ $formatoNota($f->ultimo) }}</td>
                                <td class="px-4 py-3.5 text-center">
                                    <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] font-bold {{ $clase }}">{{ $etiqueta }}</span>
                                </td>
                                <td class="px-4 py-3.5 text-muted">{{ $f->fecha ? \Illuminate\Support\Carbon::parse($f->fecha)->format('d/m/Y H:i') : '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</div>
@endsection
