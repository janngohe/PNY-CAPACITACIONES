@extends('layouts.empleado')

@section('title', 'Mis Certificados')
@section('page_title', 'Certificados Emitidos')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    
    <!-- Encabezado de la Sección -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-line/70 shadow-2xs">
        <div>
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-sky-50 text-brand-blue text-xs font-bold mb-2">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Acreditación Institucional</span>
            </div>
            <h1 class="font-heading font-extrabold text-xl sm:text-2xl text-brand-dark">
                Certificados Oficiales de Capacitación
            </h1>
            <p class="text-xs sm:text-sm text-muted mt-0.5">
                Certificados emitidos a nombre de <strong class="text-brand-dark">{{ $usuario->nombre_completo ?? 'Colaborador' }}</strong> con código único de validación.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <span class="text-xs text-muted font-medium">Total obtenidos:</span>
            <span class="px-3 py-1 bg-brand-light text-brand-blue font-bold rounded-xl text-sm border border-brand-blue/20">
                {{ count($certificados) }} Certificados
            </span>
        </div>
    </div>

    <!-- Grid de Certificados -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 sm:gap-6">
        @forelse($certificados as $cert)
        @php
            $codigo = $cert->codigo ?? ($cert['codigo'] ?? 'PNY-CERT');
            $titulo = $cert->capacitacion->titulo ?? ($cert->nombre_capacitacion ?? ($cert['capacitacion_titulo'] ?? 'Certificado'));
            $porcentaje = $cert->porcentaje ?? ($cert['porcentaje'] ?? 80);
            $fecha = is_object($cert->fecha_emision ?? null) ? $cert->fecha_emision->format('d M Y') : ($cert->fecha_emision ?? ($cert['fecha_emision'] ?? 'Reciente'));
            $firma = $cert->plantillaCertificado->firma_1_nombre ?? ($cert['instructor'] ?? 'C.I. Piscícola New York');
        @endphp
        <div class="bg-white rounded-3xl border border-line/80 shadow-xs hover:shadow-md transition-all duration-200 p-6 flex flex-col justify-between relative overflow-hidden group">
            
            <!-- Cinta decorativa de verificación -->
            <div class="absolute top-0 right-0 w-28 h-28 overflow-hidden pointer-events-none">
                <div class="absolute transform rotate-45 bg-emerald-500 text-white font-bold text-[9px] py-1 right-[-35px] top-[18px] w-[120px] text-center shadow-xs uppercase tracking-wider">
                    Válido
                </div>
            </div>

            <div>
                <!-- Encabezado de la tarjeta con icono de diploma -->
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-brand-deep to-brand-blue text-white flex items-center justify-center shrink-0 shadow-xs">
                        <svg class="w-6 h-6 text-brand-sky" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.504-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.004 0H9.496m5.004 0a3 3 0 002.996-2.67V5.625A2.625 2.625 0 0014.875 3h-5.75A2.625 2.625 0 006.5 5.625v7.08a3 3 0 002.996 2.67" />
                        </svg>
                    </div>
                    <div class="pr-12">
                        <span class="text-[10px] font-bold text-muted uppercase tracking-wider font-mono">
                            {{ $codigo }}
                        </span>
                        <h3 class="font-heading font-extrabold text-base sm:text-lg text-brand-dark leading-snug mt-0.5">
                            {{ $titulo }}
                        </h3>
                    </div>
                </div>

                <!-- Detalles de aprobación -->
                <div class="mt-5 p-4 rounded-2xl bg-slate-50 border border-line/60 space-y-2 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-muted">Acreditado a:</span>
                        <span class="font-bold text-brand-dark">{{ $usuario->nombre_completo ?? 'Colaborador PNY' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-muted">Calificación obtenida:</span>
                        <span class="font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200">
                            {{ $porcentaje }}%
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-muted">Fecha de expedición:</span>
                        <span class="font-semibold text-slate-700">{{ $fecha }}</span>
                    </div>
                    <div class="flex items-center justify-between pt-1 border-t border-line/40">
                        <span class="text-muted">Firma / Emisor:</span>
                        <span class="text-[11px] font-medium text-slate-600 truncate max-w-[200px]" title="{{ $firma }}">
                            {{ $firma }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="mt-5 pt-4 border-t border-line/70 flex items-center gap-3">
                <button type="button" 
                        class="flex-1 py-2.5 px-4 rounded-xl bg-brand-blue hover:bg-brand-deep text-white text-xs font-bold font-heading transition-colors flex items-center justify-center gap-2 shadow-xs cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    <span>Descargar Certificado (PDF)</span>
                </button>
            </div>

        </div>
        @empty
        <div class="col-span-full bg-white rounded-3xl border border-line/80 p-10 text-center space-y-3">
            <div class="w-14 h-14 mx-auto rounded-2xl bg-sky-50 text-brand-sky flex items-center justify-center">
                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.504-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.004 0H9.496m5.004 0a3 3 0 002.996-2.67V5.625A2.625 2.625 0 0014.875 3h-5.75A2.625 2.625 0 006.5 5.625v7.08a3 3 0 002.996 2.67" />
                </svg>
            </div>
            <h3 class="font-heading font-extrabold text-base text-brand-dark">Aún no tienes certificados emitidos</h3>
            <p class="text-xs text-muted max-w-sm mx-auto">
                Completa tus capacitaciones activas y aprueba las evaluaciones correspondientes para obtener tus certificaciones.
            </p>
        </div>
        @endforelse
    </div>

</div>
@endsection
