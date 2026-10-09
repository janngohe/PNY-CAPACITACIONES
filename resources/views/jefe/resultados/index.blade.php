@extends('layouts.jefe')

@section('title', 'Consultar Resultados')
@section('page_title', 'Consultar Resultados')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <div class="relative rounded-3xl bg-gradient-to-r from-brand-dark via-brand-deep to-brand-blue text-white p-6 sm:p-8 overflow-hidden shadow-md">
        <div class="absolute -right-10 -top-10 w-56 h-56 rounded-full bg-brand-sky/15 blur-2xl pointer-events-none"></div>
        <div class="relative z-10">
            <h1 class="font-heading font-extrabold text-xl sm:text-2xl md:text-3xl leading-tight">Consultar Resultados</h1>
            <p class="mt-1.5 text-xs sm:text-sm text-slate-200/90 max-w-2xl">
                Revisa cómo le va a tu equipo en cada evaluación: quién la presentó, cuántos intentos usó y si aprobó.
            </p>
        </div>
        <div class="absolute bottom-0 left-0 w-full overflow-hidden leading-none pointer-events-none opacity-30">
            <svg class="block w-full h-4 text-white" viewBox="0 0 1200 40" preserveAspectRatio="none"><path d="M0,0 C150,35 350,10 500,25 C650,40 850,5 1000,20 C1100,30 1160,15 1200,25 L1200,40 L0,40 Z" fill="currentColor"/></svg>
        </div>
    </div>

    <section class="bg-white rounded-3xl border border-line/80 shadow-2xs overflow-hidden" aria-labelledby="titulo-resultados">
        <div class="px-5 sm:px-6 py-4 border-b border-line/60">
            <h2 id="titulo-resultados" class="font-heading font-bold text-base text-brand-dark">Evaluaciones de mis capacitaciones</h2>
            <p class="text-xs text-muted">Selecciona una evaluación para ver el detalle por empleado.</p>
        </div>

        @if ($evaluaciones->isEmpty())
            <div class="p-10 text-center space-y-2">
                <p class="text-xs text-muted">Todavía no tienes evaluaciones creadas.</p>
                <a href="{{ route('jefe.evaluaciones.create') }}" class="inline-block text-xs font-bold text-brand-blue hover:underline">Crear evaluación</a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-muted">
                        <tr>
                            <th class="px-5 sm:px-6 py-3 font-bold">Evaluación</th>
                            <th class="px-4 py-3 font-bold text-center">Participaron</th>
                            <th class="px-4 py-3 font-bold text-center">Intentos</th>
                            <th class="px-4 py-3 font-bold text-center">Aprobados</th>
                            <th class="px-4 py-3 font-bold text-center">Promedio</th>
                            <th class="px-4 py-3 font-bold text-center">Estado</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line/50">
                        @foreach ($evaluaciones as $evaluacion)
                            <tr class="hover:bg-brand-light/40 transition-colors">
                                <td class="px-5 sm:px-6 py-3.5">
                                    <p class="font-bold text-brand-dark">{{ $evaluacion->titulo }}</p>
                                    <p class="text-[11px] text-muted truncate max-w-xs">{{ $evaluacion->capacitacion->titulo }}</p>
                                </td>
                                <td class="px-4 py-3.5 text-center font-semibold text-brand-dark">{{ $evaluacion->participantes }} / {{ $empleadosArea }}</td>
                                <td class="px-4 py-3.5 text-center">{{ $evaluacion->intentos_total }}</td>
                                <td class="px-4 py-3.5 text-center font-semibold text-emerald-700">{{ $evaluacion->aprobados_total }}</td>
                                <td class="px-4 py-3.5 text-center font-bold text-brand-blue">{{ $evaluacion->promedio !== null ? $evaluacion->promedio . '%' : '—' }}</td>
                                <td class="px-4 py-3.5 text-center">
                                    <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] font-bold {{ $evaluacion->estado ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600' }}">
                                        {{ $evaluacion->estado ? 'Activa' : 'Desactivada' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-right">
                                    <a href="{{ route('jefe.resultados.show', $evaluacion) }}"
                                       class="inline-flex px-3.5 py-2 rounded-xl bg-brand-blue hover:bg-brand-deep text-white font-heading font-bold text-xs shadow-xs transition-colors">
                                        Ver detalle
                                    </a>
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
