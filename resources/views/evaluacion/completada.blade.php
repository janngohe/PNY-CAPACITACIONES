@extends('layouts.evaluacion')

@section('title', 'Evaluación Completada')
@section('paso_activo', '4')

@section('content')
<div class="max-w-md mx-auto text-center py-6 sm:py-10">

    <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 shadow-xs space-y-5">
        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-3xl sm:text-4xl mx-auto border border-emerald-100 shadow-2xs">
            <i class="fa-solid fa-circle-check"></i>
        </div>

        <div>
            <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-700 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-200">
                Prueba Finalizada Exitosamente
            </span>
            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 font-serif mt-3">
                ¡Gracias por tu Participación!
            </h2>
            <p class="text-xs sm:text-sm text-slate-600 mt-2 leading-relaxed">
                Tus respuestas para el cuestionario psicotécnico <strong>PAC-A</strong> han sido registradas y almacenadas de forma segura y confidencial en el sistema de <strong>C.I. Piscícola New York S.A.</strong>
            </p>
        </div>

        <!-- Resumen del Registro -->
        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-left text-xs space-y-2">
            <div class="flex justify-between border-b border-slate-200/80 pb-1.5">
                <span class="text-slate-400">Persona evaluada:</span>
                <strong class="text-slate-800 font-bold">{{ $candidato->nombre_completo }}</strong>
            </div>
            <div class="flex justify-between border-b border-slate-200/80 pb-1.5">
                <span class="text-slate-400">Identificación:</span>
                <strong class="text-slate-800 font-mono">C.C. {{ $candidato->numero_identificacion }}</strong>
            </div>
            <div class="flex justify-between border-b border-slate-200/80 pb-1.5">
                <span class="text-slate-400">Prueba realizada:</span>
                <strong class="text-brand-blue font-bold">PAC-A (40 afirmaciones)</strong>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-400">Fecha y hora de envío:</span>
                <strong class="text-slate-800">{{ $aplicacion->hora_finalizacion ? $aplicacion->hora_finalizacion->format('d/m/Y h:i A') : now()->format('d/m/Y h:i A') }}</strong>
            </div>
        </div>

        <div class="p-3.5 rounded-2xl bg-blue-50 border border-blue-100 text-slate-700 text-xs text-left leading-relaxed flex items-start gap-2.5">
            <i class="fa-solid fa-lock text-brand-blue mt-0.5 shrink-0"></i>
            <p class="text-[11px]">
                Conforme a la cláusula de confidencialidad institucional suscrita en el <strong>Consentimiento Informado (GH-FO-025)</strong>, los resultados de esta prueba son reservados y serán valorados exclusivamente por la profesional en Psicología responsable del proceso de selección.
            </p>
        </div>

        <div class="pt-2">
            <p class="text-xs text-slate-500 mb-3">
                Puedes cerrar esta ventana de forma segura o descargar tu copia del consentimiento informado.
            </p>

            <a href="{{ route('evaluacion.consentimiento.pdf', ['token' => $candidato->token_acceso]) }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs transition-colors shadow-2xs">
                <i class="fa-solid fa-file-arrow-down text-brand-blue"></i>
                Descargar Copia de Consentimiento (PDF)
            </a>
        </div>
    </div>

</div>
@endsection
