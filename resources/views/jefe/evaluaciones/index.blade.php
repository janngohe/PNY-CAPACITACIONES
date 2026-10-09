@extends('layouts.jefe')

@section('title', 'Mis Evaluaciones')
@section('page_title', 'Mis Evaluaciones')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="font-heading font-extrabold text-xl sm:text-2xl text-brand-dark">Mis Evaluaciones</h1>
            <p class="text-xs text-muted">Cuestionarios de las capacitaciones que publicaste. Se pueden editar o desactivar, nunca eliminar.</p>
        </div>
        <a href="{{ route('jefe.evaluaciones.create') }}" id="btn-nueva-evaluacion"
           class="self-start sm:self-auto inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-blue hover:bg-brand-deep text-white font-heading font-bold text-xs sm:text-sm shadow-xs transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
            Nueva evaluación
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        @forelse ($evaluaciones as $evaluacion)
            @php $activa = (bool) $evaluacion->estado; @endphp
            <article id="evaluacion-{{ $evaluacion->id }}" class="relative bg-white rounded-3xl border border-line/80 shadow-xs hover:shadow-md transition-all overflow-hidden {{ $activa ? '' : 'opacity-90' }}">
                <div class="absolute top-0 left-0 w-full h-1.5 {{ $activa ? 'bg-gradient-to-r from-brand-dark via-brand-blue to-brand-sky' : 'bg-slate-300' }}"></div>
                <div class="p-5 sm:p-6 space-y-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-[10px] uppercase font-bold tracking-wider text-brand-sky truncate">{{ $evaluacion->capacitacion->titulo }}</p>
                            <h2 class="font-heading font-extrabold text-base sm:text-lg text-brand-dark leading-tight mt-0.5">{{ $evaluacion->titulo }}</h2>
                        </div>
                        <span class="shrink-0 inline-flex px-2.5 py-1 rounded-full text-[11px] font-bold {{ $activa ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600' }}">
                            {{ $activa ? 'Activa' : 'Desactivada' }}
                        </span>
                    </div>

                    <div class="grid grid-cols-3 gap-2 text-center">
                        <div class="bg-slate-50 rounded-xl p-2.5">
                            <span class="block text-lg font-heading font-extrabold text-brand-dark">{{ $evaluacion->preguntas_count }}</span>
                            <span class="text-[10px] text-muted">Preguntas</span>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-2.5">
                            <span class="block text-lg font-heading font-extrabold text-brand-dark">{{ rtrim(rtrim(number_format((float) $evaluacion->porcentaje_aprobacion, 2), '0'), '.') }}%</span>
                            <span class="text-[10px] text-muted">Para aprobar</span>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-2.5">
                            <span class="block text-lg font-heading font-extrabold text-brand-dark">{{ $evaluacion->intentos_count }}</span>
                            <span class="text-[10px] text-muted">Intentos hechos</span>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2 pt-3 border-t border-line/60">
                        <a href="{{ route('jefe.resultados.show', $evaluacion) }}"
                           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-brand-blue hover:bg-brand-deep text-white font-heading font-bold text-xs shadow-xs transition-colors">
                            Resultados
                        </a>
                        <a href="{{ route('jefe.evaluaciones.edit', $evaluacion) }}"
                           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-brand-light hover:text-brand-blue text-slate-700 font-heading font-bold text-xs transition-colors">
                            Editar
                        </a>
                        <form method="POST" action="{{ route('jefe.evaluaciones.estado', $evaluacion) }}" class="ml-auto m-0"
                              onsubmit="return confirm('{{ $activa ? '¿Desactivar esta evaluación? Los resultados se conservan.' : '¿Activar nuevamente esta evaluación?' }}');">
                            @csrf
                            @method('PATCH')
                            <button type="submit" id="btn-estado-evaluacion-{{ $evaluacion->id }}"
                                    class="inline-flex items-center px-3.5 py-2 rounded-xl text-xs font-heading font-bold border transition-colors cursor-pointer {{ $activa ? 'border-amber-300 text-amber-700 bg-amber-50 hover:bg-amber-100' : 'border-emerald-300 text-emerald-700 bg-emerald-50 hover:bg-emerald-100' }}">
                                {{ $activa ? 'Desactivar' : 'Activar' }}
                            </button>
                        </form>
                    </div>
                </div>
            </article>
        @empty
            <div class="col-span-full bg-white rounded-3xl border border-line/80 p-10 text-center space-y-3">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-blue-50 text-brand-blue flex items-center justify-center">
                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z" /></svg>
                </div>
                <h3 class="font-heading font-extrabold text-base text-brand-dark">Aún no has creado evaluaciones</h3>
                <p class="text-xs text-muted max-w-sm mx-auto">Crea un cuestionario para medir lo aprendido en tus capacitaciones.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
