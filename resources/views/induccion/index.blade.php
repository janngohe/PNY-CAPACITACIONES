@extends('layouts.induccion')

@section('title', 'Panel de Inducciones')

@section('content')
<div class="space-y-8">

    <!-- Encabezado y Acción Principal -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-gradient-to-r from-slate-900 to-slate-800/80 p-6 sm:p-8 rounded-3xl border border-slate-800 shadow-xl relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-48 h-48 rounded-full bg-cyan-500/10 blur-3xl pointer-events-none"></div>

        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-cyan-500/10 text-cyan-400 border border-cyan-500/20">
                    <i class="fa-solid fa-chalkboard-user"></i> Sesiones en Vivo
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">Módulo de Inducciones Presenciales</h1>
            <p class="text-sm text-slate-400 mt-1 max-w-2xl">
                Crea o selecciona una inducción institucional, proyéctala en pantalla con su <strong class="text-cyan-400">código QR dinámico</strong> y recolecta firmas de asistencia en tiempo real desde el celular de los colaboradores.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('induccion.create') }}"
               class="px-5 py-3 rounded-2xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white font-bold text-sm tracking-wide shadow-lg shadow-cyan-500/20 hover:shadow-cyan-500/30 transition-all flex items-center gap-2 cursor-pointer">
                <i class="fa-solid fa-plus-circle"></i>
                <span>Crear Nueva Inducción</span>
            </a>
        </div>
    </div>

    <!-- Catálogo de Inducciones Disponibles -->
    <div>
        <div class="flex items-center justify-between mb-5">
            <div>
                <h2 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-layer-group text-cyan-400"></i> Catálogo de Inducciones
                </h2>
                <p class="text-xs text-slate-400">Selecciona una inducción para iniciar su proyección y generar el QR de asistencia</p>
            </div>
            <span class="text-xs font-semibold px-3 py-1 rounded-full bg-slate-800 text-slate-300 border border-slate-700">
                {{ $inducciones->count() }} registradas
            </span>
        </div>

        @if($inducciones->isEmpty())
            <div class="text-center py-16 px-4 bg-slate-900/60 rounded-3xl border border-dashed border-slate-800">
                <div class="w-16 h-16 rounded-2xl bg-slate-800 flex items-center justify-center text-slate-600 text-2xl mx-auto mb-4">
                    <i class="fa-solid fa-presentation-screen"></i>
                </div>
                <h3 class="text-base font-bold text-white mb-1">Aún no hay inducciones creadas</h3>
                <p class="text-xs text-slate-400 max-w-sm mx-auto mb-6">
                    Comienza creando la primera inducción institucional subiendo tus diapositivas o enlazando un video para reproducir en sala.
                </p>
                <a href="{{ route('induccion.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-bold text-xs tracking-wide transition-all shadow-md">
                    <i class="fa-solid fa-plus"></i> Crear Primera Inducción
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($inducciones as $ind)
                    <div class="bg-slate-900/90 rounded-3xl border border-slate-800 hover:border-cyan-500/40 p-6 flex flex-col justify-between transition-all duration-200 shadow-xl group hover:shadow-cyan-500/5">
                        <div>
                            <!-- Header de tarjeta -->
                            <div class="flex items-start justify-between gap-3 mb-3">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider
                                    @if($ind->tipo === 'SST') bg-amber-500/10 text-amber-400 border border-amber-500/20
                                    @elseif($ind->tipo === 'AMBIENTAL') bg-emerald-500/10 text-emerald-400 border border-emerald-500/20
                                    @elseif($ind->tipo === 'PLANTA') bg-blue-500/10 text-blue-400 border border-blue-500/20
                                    @else bg-cyan-500/10 text-cyan-400 border border-cyan-500/20
                                    @endif">
                                    {{ $ind->tipo }}
                                </span>

                                <div class="flex items-center gap-1.5 text-xs text-slate-400">
                                    @if($ind->video_url)
                                        <span title="Incluye Video" class="w-6 h-6 rounded-lg bg-rose-500/10 text-rose-400 flex items-center justify-center border border-rose-500/20">
                                            <i class="fa-brands fa-youtube text-[11px]"></i>
                                        </span>
                                    @endif
                                    @if($ind->archivo_presentacion)
                                        <span title="Incluye PDF de diapositivas" class="w-6 h-6 rounded-lg bg-red-500/10 text-red-400 flex items-center justify-center border border-red-500/20">
                                            <i class="fa-solid fa-file-pdf text-[11px]"></i>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Título y Descripción -->
                            <h3 class="text-base font-bold text-white group-hover:text-cyan-400 transition-colors mb-2 leading-snug">
                                {{ $ind->titulo }}
                            </h3>

                            <p class="text-xs text-slate-400 line-clamp-3 mb-4 leading-relaxed">
                                {{ $ind->descripcion ?? 'Sin descripción adicional para esta inducción.' }}
                            </p>

                            <!-- Sedes asignadas -->
                            <div class="mb-5 pt-3 border-t border-slate-800 text-[11px] text-slate-400 flex items-center gap-2">
                                <i class="fa-solid fa-location-dot text-slate-500"></i>
                                <span>
                                    @if($ind->sedes->isNotEmpty())
                                        {{ $ind->sedes->pluck('nombre')->join(', ') }}
                                    @else
                                        Todas las sedes
                                    @endif
                                </span>
                            </div>
                        </div>

                        <!-- Botón para Iniciar Sesión en Vivo -->
                        <div>
                            <button type="button"
                                    onclick="abrirModalIniciar({{ $ind->id }}, '{{ addslashes($ind->titulo) }}')"
                                    class="w-full py-3 px-4 rounded-2xl bg-slate-800 hover:bg-gradient-to-r hover:from-cyan-500 hover:to-blue-600 text-slate-200 hover:text-white font-bold text-xs tracking-wider uppercase transition-all duration-200 border border-slate-700 hover:border-transparent flex items-center justify-center gap-2 cursor-pointer shadow-md">
                                <i class="fa-solid fa-qrcode text-cyan-400 group-hover:text-white transition-colors"></i>
                                <span>Iniciar Sesión en Vivo</span>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Sesiones Recientes en Vivo y Descarga de Actas -->
    <div class="pt-6 border-t border-slate-800">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h2 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-cyan-400"></i> Historial de Sesiones Dictadas
                </h2>
                <p class="text-xs text-slate-400">Sesiones iniciadas, monitoreo y descarga de actas oficiales con firmas</p>
            </div>
        </div>

        @if($sesionesRecientes->isEmpty())
            <div class="p-8 text-center bg-slate-900/40 rounded-3xl border border-slate-800/80 text-xs text-slate-500">
                Aún no has iniciado ninguna sesión en vivo. Al proyectar una inducción aparecerá registrada aquí.
            </div>
        @else
            <div class="overflow-hidden bg-slate-900/80 border border-slate-800 rounded-3xl shadow-xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-800/60 text-slate-400 font-bold uppercase tracking-wider border-b border-slate-800 text-[11px]">
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
                        <tbody class="divide-y divide-slate-800 text-slate-300">
                            @foreach($sesionesRecientes as $ses)
                                <tr class="hover:bg-slate-800/40 transition-colors">
                                    <td class="py-3.5 px-4 font-mono font-bold text-cyan-400">
                                        {{ $ses->codigo_sesion }}
                                    </td>
                                    <td class="py-3.5 px-4 font-semibold text-white">
                                        {{ $ses->induccion->titulo }}
                                        <div class="text-[11px] text-slate-500 font-normal">
                                            {{ $ses->fecha_inicio ? $ses->fecha_inicio->format('d/m/Y h:i A') : '' }}
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-300">
                                        {{ $ses->facilitador->nombre_completo ?? 'N/A' }}
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-400">
                                        {{ $ses->sede->nombre ?? 'Sede Principal' }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-cyan-500/10 text-cyan-400 border border-cyan-500/20">
                                            <i class="fa-solid fa-users text-[10px]"></i> {{ $ses->asistencias_count }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        @if($ses->estaActiva())
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 animate-pulse">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> En Curso
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-slate-800 text-slate-400 border border-slate-700">
                                                Finalizada
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('induccion.presentacion', ['codigo' => $ses->codigo_sesion]) }}"
                                               class="p-2 rounded-xl bg-slate-800 hover:bg-cyan-500 hover:text-slate-950 text-slate-300 transition-colors"
                                               title="Ver Presentación y QR">
                                                <i class="fa-solid fa-tv"></i>
                                            </a>
                                            <a href="{{ route('induccion.acta.pdf', ['codigo' => $ses->codigo_sesion]) }}"
                                               class="p-2 rounded-xl bg-slate-800 hover:bg-emerald-500 hover:text-slate-950 text-slate-300 transition-colors"
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

<!-- Modal para Configurar e Iniciar Sesión en Vivo -->
<div id="modalIniciarSesion" class="fixed inset-0 z-50 hidden bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-slate-900 border border-slate-800 rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl relative">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2 text-cyan-400 text-sm font-bold uppercase tracking-wider">
                <i class="fa-solid fa-satellite-dish"></i> Iniciar Proyección en Vivo
            </div>
            <button type="button" onclick="cerrarModalIniciar()" class="text-slate-400 hover:text-white text-lg">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <h3 id="modalInduccionTitulo" class="text-lg font-bold text-white mb-2 leading-snug"></h3>
        <p class="text-xs text-slate-400 mb-6">
            Selecciona la sede donde se dictará la inducción para generar el código QR dinámico y convocar a los asistentes.
        </p>

        <form id="formIniciarSesion" method="POST" action="" class="space-y-5">
            @csrf

            <!-- Selección de Sede -->
            <div>
                <label for="sede_id" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                    <i class="fa-solid fa-location-dot text-cyan-400 mr-1.5"></i> Sede de la Inducción <span class="text-rose-400">*</span>
                </label>
                <select name="sede_id" id="sede_id" required
                        class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-2xl text-white text-sm focus:outline-none focus:border-cyan-400">
                    @foreach($sedes as $s)
                        <option value="{{ $s->id }}">{{ $s->nombre }} ({{ $s->ciudad }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Botones -->
            <div class="flex items-center justify-end gap-3 pt-3">
                <button type="button" onclick="cerrarModalIniciar()"
                        class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold transition-colors cursor-pointer">
                    Cancelar
                </button>
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white text-xs font-bold tracking-wider uppercase shadow-lg shadow-cyan-500/20 transition-all flex items-center gap-2 cursor-pointer">
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
