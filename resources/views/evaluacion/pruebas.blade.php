@extends('layouts.evaluacion')

@section('title', 'Selección de Pruebas Psicotécnicas')
@section('paso_activo', '3')

@section('content')
<div class="space-y-6">

    <!-- Tarjeta de Bienvenida y Confirmación de Consentimiento -->
    <div class="bg-white border border-slate-200 rounded-3xl p-5 sm:p-7 shadow-xs">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-100">
                <i class="fa-solid fa-file-circle-check text-lg"></i>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Aspirante Validado</span>
                <h2 class="text-sm sm:text-base font-extrabold text-slate-900 font-serif">
                    {{ $candidato->nombre_completo }}
                </h2>
                <p class="text-xs text-slate-500">
                    C.C. {{ $candidato->numero_identificacion }} · Consentimiento GH-FO-025 otorgado
                </p>
            </div>
        </div>
    </div>

    <!-- Cuestionarios Disponibles -->
    <div class="space-y-4">
        <div class="text-xs font-bold uppercase tracking-wider text-slate-500">
            Pruebas Asignadas para tu Proceso
        </div>

        <!-- 1. PRUEBA PAC-A (ACTIVA) -->
        <div class="bg-white border-2 border-brand-blue/30 hover:border-brand-blue rounded-3xl p-5 sm:p-7 shadow-xs transition-all relative overflow-hidden">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="space-y-2">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <i class="fa-solid fa-circle-check mr-1"></i> Disponible Ahora
                        </span>
                        <span class="text-xs font-mono font-bold text-brand-blue">
                            Código: PAC-A
                        </span>
                    </div>

                    <h3 class="text-base sm:text-lg font-extrabold text-slate-900 font-serif">
                        Cuestionario PAC-A (40 Afirmaciones)
                    </h3>

                    <p class="text-xs text-slate-600 leading-relaxed max-w-xl">
                        Evaluación psicotécnica de estilos de comportamiento y hábitos de trabajo. Consta de 40 afirmaciones sencillas con 4 alternativas de respuesta: <em>Siempre, Frecuentemente, Rara vez</em> o <em>Nunca</em>.
                    </p>

                    <div class="flex flex-wrap items-center gap-3 text-[11px] text-slate-500 pt-1">
                        <span class="flex items-center gap-1">
                            <i class="fa-solid fa-clock text-slate-400"></i> Aprox. 10 a 15 minutos
                        </span>
                        <span class="flex items-center gap-1">
                            <i class="fa-solid fa-list-ol text-slate-400"></i> 40 preguntas obligatorias
                        </span>
                        @if($aplicacionPaca)
                            <span class="flex items-center gap-1 font-bold text-amber-600">
                                <i class="fa-solid fa-spinner fa-spin"></i> Progreso actual: {{ round($aplicacionPaca->progreso_porcentaje) }}%
                            </span>
                        @endif
                    </div>
                </div>

                <div class="sm:shrink-0 pt-2 sm:pt-0">
                    @if($aplicacionPaca && $aplicacionPaca->estaFinalizada())
                        <div class="text-center sm:text-right">
                            <span class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-2xl bg-emerald-50 text-emerald-800 font-bold text-xs border border-emerald-200">
                                <i class="fa-solid fa-circle-check text-emerald-600"></i> Prueba Finalizada
                            </span>
                            <div class="mt-2">
                                <a href="{{ route('evaluacion.paca.completada', ['token' => $candidato->token_acceso, 'appToken' => $aplicacionPaca->token_sesion]) }}"
                                   class="text-[11px] font-bold text-brand-blue underline">
                                    Ver comprobante
                                </a>
                            </div>
                        </div>
                    @else
                        <form action="{{ route('evaluacion.paca.iniciar') }}" method="POST">
                            @csrf
                            <input type="hidden" name="token" value="{{ $candidato->token_acceso }}">
                            <button type="submit"
                                    class="w-full sm:w-auto py-3.5 px-6 rounded-2xl bg-brand-blue hover:bg-brand-dark text-white font-bold text-xs uppercase tracking-wider transition-all shadow-md active:scale-98 flex items-center justify-center gap-2 cursor-pointer">
                                <span>{{ $aplicacionPaca ? 'Continuar PAC-A' : 'Iniciar Cuestionario PAC-A' }}</span>
                                <i class="fa-solid fa-arrow-right text-xs"></i>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- 2. PRUEBA VALANTI (BLOQUEADA INSTITUCIONALMENTE) -->
        <div class="bg-slate-50 border border-slate-200 rounded-3xl p-5 sm:p-7 opacity-80 relative">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="space-y-1.5">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-200 text-slate-700">
                            <i class="fa-solid fa-clock-rotate-left mr-1"></i> En Preparación
                        </span>
                        <span class="text-xs font-mono font-bold text-slate-500">
                            Código: VALANTI
                        </span>
                    </div>

                    <h3 class="text-base font-bold text-slate-700 font-serif">
                        Cuestionario de Valores Valanti
                    </h3>

                    <p class="text-xs text-slate-500 max-w-xl">
                        Este instrumento se encuentra en fase de validación técnica institucional. No está habilitado en este proceso.
                    </p>
                </div>

                <div class="sm:shrink-0 pt-2 sm:pt-0">
                    <span class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-2xl bg-slate-200/80 text-slate-500 font-bold text-xs cursor-not-allowed">
                        <i class="fa-solid fa-lock text-xs"></i> No Habilitada
                    </span>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
