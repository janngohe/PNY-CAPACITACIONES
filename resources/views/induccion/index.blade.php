@extends('layouts.induccion')

@section('title', 'Panel de Inducciones')

@section('content')
<div class="space-y-8">

    <!-- Encabezado y Acción Principal -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-xs relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-48 h-48 rounded-full bg-cyan-100/60 blur-3xl pointer-events-none"></div>

        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-cyan-50 text-cyan-800 border border-cyan-200">
                    <i class="fa-solid fa-chalkboard-user"></i> Sesiones en Vivo
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight font-serif">Módulo de Inducciones Presenciales</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-2xl leading-relaxed">
                Crea o selecciona una inducción institucional, proyéctala en pantalla con su <strong class="text-brand-blue">código QR dinámico</strong> y recolecta firmas de asistencia en tiempo real desde el celular de los colaboradores.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('induccion.create') }}"
               class="px-5 py-3 rounded-2xl bg-brand-blue hover:bg-brand-dark text-white font-bold text-xs uppercase tracking-wider shadow-md hover:shadow-lg transition-all flex items-center gap-2 cursor-pointer">
                <i class="fa-solid fa-plus-circle"></i>
                <span>Crear Nueva Inducción</span>
            </a>
        </div>
    </div>

    <!-- Catálogo de Inducciones Disponibles -->
    <div>
        <div class="flex items-center justify-between mb-5">
            <div>
                <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2 font-serif">
                    <i class="fa-solid fa-layer-group text-brand-blue"></i> Catálogo de Inducciones
                </h2>
                <p class="text-xs text-slate-500">Selecciona una inducción para iniciar su proyección y generar el QR de asistencia</p>
            </div>
            <span class="text-xs font-semibold px-3 py-1 rounded-full bg-slate-100 text-slate-600 border border-slate-200">
                {{ $inducciones->count() }} registradas
            </span>
        </div>

        @if($inducciones->isEmpty())
            <div class="text-center py-16 px-4 bg-white rounded-3xl border border-dashed border-slate-300 shadow-2xs">
                <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400 text-2xl mx-auto mb-4">
                    <i class="fa-solid fa-presentation-screen"></i>
                </div>
                <h3 class="text-base font-bold text-slate-800 mb-1">Aún no hay inducciones creadas</h3>
                <p class="text-xs text-slate-500 max-w-sm mx-auto mb-6">
                    Comienza creando la primera inducción institucional subiendo tus diapositivas o enlazando un video para reproducir en sala.
                </p>
                <a href="{{ route('induccion.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-brand-blue hover:bg-brand-dark text-white font-bold text-xs uppercase tracking-wider transition-all shadow-md">
                    <i class="fa-solid fa-plus"></i> Crear Primera Inducción
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($inducciones as $ind)
                    <div class="bg-white rounded-3xl border border-slate-200 hover:border-brand-blue/60 p-6 flex flex-col justify-between transition-all duration-200 shadow-xs hover:shadow-md group">
                        <div>
                            <!-- Header de tarjeta -->
                            <div class="flex items-start justify-between gap-3 mb-3">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider
                                    @if($ind->tipo === 'SST') bg-amber-50 text-amber-800 border border-amber-200
                                    @elseif($ind->tipo === 'AMBIENTAL') bg-emerald-50 text-emerald-800 border border-emerald-200
                                    @elseif($ind->tipo === 'PLANTA') bg-blue-50 text-blue-800 border border-blue-200
                                    @else bg-cyan-50 text-cyan-800 border border-cyan-200
                                    @endif">
                                    {{ $ind->tipo }}
                                </span>

                                <div class="flex items-center gap-1.5 text-xs text-slate-400">
                                    @if($ind->video_url)
                                        <span title="Incluye Video" class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center border border-rose-200">
                                            <i class="fa-brands fa-youtube text-xs"></i>
                                        </span>
                                    @endif
                                    @if($ind->archivo_presentacion)
                                        <span title="Incluye PDF de diapositivas" class="w-7 h-7 rounded-lg bg-red-50 text-red-600 flex items-center justify-center border border-red-200">
                                            <i class="fa-solid fa-file-pdf text-xs"></i>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Título y Descripción -->
                            <h3 class="text-base font-bold text-slate-900 group-hover:text-brand-blue transition-colors mb-2 leading-snug">
                                {{ $ind->titulo }}
                            </h3>

                            <p class="text-xs text-slate-500 line-clamp-3 mb-4 leading-relaxed">
                                {{ $ind->descripcion ?? 'Sin descripción adicional para esta inducción.' }}
                            </p>

                            <!-- Sedes asignadas -->
                            <div class="mb-5 pt-3 border-t border-slate-100 text-[11px] text-slate-500 flex items-center gap-2">
                                <i class="fa-solid fa-location-dot text-slate-400"></i>
                                <span class="truncate">
                                    @if($ind->sedes->isNotEmpty())
                                        {{ $ind->sedes->pluck('nombre')->join(', ') }}
                                    @else
                                        Todas las sedes institucionales
                                    @endif
                                </span>
                            </div>
                        </div>

                        <!-- Botón para Iniciar Sesión en Vivo -->
                        <div>
                            <button type="button"
                                    onclick="abrirModalIniciar({{ $ind->id }}, '{{ addslashes($ind->titulo) }}')"
                                    class="w-full py-3 px-4 rounded-2xl bg-slate-50 hover:bg-brand-blue text-slate-700 hover:text-white font-bold text-xs tracking-wider uppercase transition-all duration-200 border border-slate-200 hover:border-transparent flex items-center justify-center gap-2 cursor-pointer shadow-2xs group-hover:bg-brand-blue group-hover:text-white">
                                <i class="fa-solid fa-qrcode text-brand-blue group-hover:text-white transition-colors"></i>
                                <span>Iniciar Sesión en Vivo</span>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Sesiones Recientes en Vivo y Descarga de Actas -->
    <div class="pt-6 border-t border-slate-200">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2 font-serif">
                    <i class="fa-solid fa-clock-rotate-left text-brand-blue"></i> Historial de Sesiones Dictadas
                </h2>
                <p class="text-xs text-slate-500">Sesiones iniciadas, monitoreo y descarga de actas oficiales con firmas</p>
            </div>
        </div>

        @if($sesionesRecientes->isEmpty())
            <div class="p-8 text-center bg-white rounded-3xl border border-slate-200 text-xs text-slate-500 shadow-2xs">
                Aún no has iniciado ninguna sesión en vivo. Al proyectar una inducción aparecerá registrada aquí.
            </div>
        @else
            <div class="overflow-hidden bg-white border border-slate-200 rounded-3xl shadow-xs">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-200 text-[11px]">
                            <tr>
                                <th class="py-3.5 px-4">Código Sesión</th>
                                <th class="py-3.5 px-4">Inducción</th>
                                <th class="py-3.5 px-4">Facilitador</th>
                                <th class="py-3.5 px-4">Sede</th>
                                <th class="py-3.5 px-4 text-center">Asistentes</th>
                                <th class="py-3.5 px-4">Estado</th>
                                <th class="py-3.5 px-4 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @foreach($sesionesRecientes as $ses)
                                <tr class="hover:bg-slate-50/60 transition-colors">
                                    <td class="py-3.5 px-4 font-mono font-bold text-brand-blue">
                                        {{ $ses->codigo_sesion }}
                                    </td>
                                    <td class="py-3.5 px-4 font-semibold text-slate-900">
                                        {{ $ses->induccion->titulo }}
                                        <div class="text-[11px] text-slate-400 font-normal">
                                            {{ $ses->fecha_inicio ? $ses->fecha_inicio->format('d/m/Y h:i A') : '' }}
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-600">
                                        {{ $ses->facilitador->nombre_completo ?? 'N/A' }}
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-600">
                                        {{ $ses->sede->nombre ?? 'Sede Principal' }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-cyan-50 text-cyan-800 border border-cyan-200">
                                            <i class="fa-solid fa-users text-[10px]"></i> {{ $ses->asistencias_count }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        @if($ses->estaActiva())
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 animate-pulse">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> En Curso
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                                Finalizada
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('induccion.presentacion', ['codigo' => $ses->codigo_sesion]) }}"
                                               class="p-2 rounded-xl bg-slate-100 hover:bg-brand-blue hover:text-white text-slate-600 transition-colors shadow-2xs"
                                               title="Ver Presentación y QR">
                                                <i class="fa-solid fa-tv"></i>
                                            </a>
                                            <a href="{{ route('induccion.acta.pdf', ['codigo' => $ses->codigo_sesion]) }}"
                                               class="p-2 rounded-xl bg-slate-100 hover:bg-emerald-600 hover:text-white text-slate-600 transition-colors shadow-2xs"
                                               title="Descargar Acta de Asistencia PDF">
                                                <i class="fa-solid fa-file-pdf"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

</div>

<!-- Modal para Configurar e Iniciar Sesión en Vivo (Tema Claro) -->
<div id="modalIniciarSesion" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white border border-slate-200 rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl relative animate-fade-in">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2 text-brand-blue text-xs font-bold uppercase tracking-wider">
                <i class="fa-solid fa-satellite-dish"></i> Iniciar Proyección en Vivo
            </div>
            <button type="button" onclick="cerrarModalIniciar()" class="text-slate-400 hover:text-slate-600 text-lg cursor-pointer">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <h3 id="modalInduccionTitulo" class="text-lg font-bold text-slate-900 mb-2 leading-snug font-serif"></h3>
        <p class="text-xs text-slate-500 mb-6 leading-relaxed">
            Selecciona la sede donde se dictará la inducción para generar el código QR dinámico y registrar las asistencias.
        </p>

        <form id="formIniciarSesion" method="POST" action="" class="space-y-5">
            @csrf

            <!-- Selección de Sede -->
            <div>
                <label for="sede_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                    <i class="fa-solid fa-location-dot text-brand-blue mr-1.5"></i> Sede de la Inducción <span class="text-rose-500">*</span>
                </label>
                <select name="sede_id" id="sede_id" required
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-2xl text-slate-800 text-sm focus:outline-none focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20">
                    @foreach($sedes as $s)
                        <option value="{{ $s->id }}">{{ $s->nombre }} ({{ $s->ciudad }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Botones -->
            <div class="flex items-center justify-end gap-3 pt-3">
                <button type="button" onclick="cerrarModalIniciar()"
                        class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors cursor-pointer">
                    Cancelar
                </button>
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl bg-brand-blue hover:bg-brand-dark text-white text-xs font-bold tracking-wider uppercase shadow-md hover:shadow-lg transition-all flex items-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-play"></i> Iniciar y Proyectar QR
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function abrirModalIniciar(induccionId, titulo) {
        document.getElementById('modalInduccionTitulo').textContent = titulo;
        document.getElementById('formIniciarSesion').action = `/induccion/${induccionId}/iniciar`;
        document.getElementById('modalIniciarSesion').classList.remove('hidden');
    }

    function cerrarModalIniciar() {
        document.getElementById('modalIniciarSesion').classList.add('hidden');
    }
</script>
@endpush
@endsection
