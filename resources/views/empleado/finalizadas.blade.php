@extends('layouts.empleado')

@section('title', 'Capacitaciones Finalizadas')
@section('page_title', 'Historial de Capacitaciones')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    
    <!-- Encabezado de la Sección -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-line/70 shadow-2xs">
        <div>
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold mb-2">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Cursos Aprobados</span>
            </div>
            <h1 class="font-heading font-extrabold text-xl sm:text-2xl text-brand-dark">
                Capacitaciones e Inducciones Finalizadas
            </h1>
            <p class="text-xs sm:text-sm text-muted mt-0.5">
                Registro histórico de cursos culminados satisfactoriamente por <strong class="text-brand-dark">{{ $usuario->nombre_completo ?? 'Colaborador' }}</strong>.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <span class="text-xs text-muted font-medium">Completadas:</span>
            <span class="px-3 py-1 bg-emerald-50 text-emerald-700 font-bold rounded-xl text-sm border border-emerald-200">
                {{ count($finalizadas) }} Aprobadas
            </span>
        </div>
    </div>

    <!-- Tabla / Listado de Historial -->
    <div class="bg-white rounded-3xl border border-line/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-line/70 text-[11px] font-bold uppercase tracking-wider text-muted">
                        <th class="py-4 px-6">Capacitación</th>
                        <th class="py-4 px-4">Fecha Finalización</th>
                        <th class="py-4 px-4 text-center">Horas</th>
                        <th class="py-4 px-4 text-center">Estado</th>
                        <th class="py-4 px-6 text-right">Certificado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line/60 text-xs text-slate-700">
                    @forelse($finalizadas as $item)
                    @php
                        $titulo = $item->titulo ?? ($item['titulo'] ?? 'Capacitación');
                        $fecha = is_object($item->pivot->fecha_finalizacion ?? null) ? $item->pivot->fecha_finalizacion->format('d M Y') : ($item['fecha_finalizacion'] ?? 'Finalizado');
                        $horas = $item->duracion_estimada ? $item->duracion_estimada . ' hrs' : ($item['horas'] ?? '4 hrs');
                        $certObj = isset($item->certificados) ? $item->certificados->first() : null;
                    @endphp
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="py-4 px-6 font-semibold text-brand-dark max-w-xs">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                                    <i class="fa-solid fa-circle-check text-sm"></i>
                                </div>
                                <span class="leading-snug">{{ $titulo }}</span>
                            </div>
                        </td>
                        <td class="py-4 px-4 text-muted whitespace-nowrap">
                            {{ $fecha }}
                        </td>
                        <td class="py-4 px-4 text-center font-medium whitespace-nowrap">
                            {{ $horas }}
                        </td>
                        <td class="py-4 px-4 text-center whitespace-nowrap">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                APROBADO
                            </span>
                        </td>
                        <td class="py-4 px-6 text-right whitespace-nowrap">
                            @if($certObj)
                                <a href="{{ route('empleado.certificados.ver', $certObj) }}" 
                                   class="inline-flex items-center gap-1.5 text-xs font-bold text-brand-blue hover:text-brand-deep hover:underline">
                                    <i class="fa-solid fa-graduation-cap text-sm"></i>
                                    <span>Ver Certificado</span>
                                </a>
                            @else
                                <a href="{{ route('empleado.certificados') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-brand-blue hover:underline">
                                    <i class="fa-solid fa-award text-sm"></i> Mis Certificados
                                </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-10 text-center text-muted text-xs">
                            Aún no has completado capacitaciones. Las capacitaciones que culmines aparecerán registradas aquí.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
