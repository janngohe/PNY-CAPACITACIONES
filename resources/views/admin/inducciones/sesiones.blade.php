@extends('layouts.admin')

@section('title', 'Historial de Sesiones de Inducción · Panel Administrador')

@section('content')
<div class="space-y-6">

    <!-- Encabezado de Página -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('admin.inducciones.index') }}" class="text-xs text-slate-500 hover:text-cyan-700 transition-colors flex items-center gap-1 font-medium">
                    <i class="fa-solid fa-arrow-left text-[11px]"></i> Volver a Inducciones
                </a>
            </div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight font-serif">Historial de Sesiones</h1>
            <p class="text-xs text-slate-500">Supervisa las sesiones de inducción dictadas con código QR, asistencias registradas y descarga de actas oficiales.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('admin.inducciones.index') }}"
               class="px-3.5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors flex items-center gap-1.5">
                <i class="fa-solid fa-shapes text-cyan-600"></i>
                <span>Ver Catálogo</span>
            </a>

            <a href="{{ route('induccion.index') }}"
               target="_blank"
               class="px-4 py-2.5 rounded-xl bg-cyan-700 hover:bg-cyan-800 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-md flex items-center gap-1.5">
                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                <span>Abrir Sala Facilitador</span>
            </a>
        </div>
    </div>

    <!-- Métricas del Historial -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs">
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Total Sesiones</div>
            <div class="text-2xl font-black text-slate-800 mt-1">{{ $metricas['total_sesiones'] }}</div>
            <div class="text-[11px] text-cyan-600 font-semibold mt-0.5">Histórico general</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs">
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Sesiones En Curso</div>
            <div class="text-2xl font-black text-emerald-600 mt-1 flex items-center gap-2">
                <span>{{ $metricas['en_curso'] }}</span>
                @if($metricas['en_curso'] > 0)
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                @endif
            </div>
            <div class="text-[11px] text-emerald-700 font-semibold mt-0.5">Transmitiendo en vivo</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs">
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Sesiones Finalizadas</div>
            <div class="text-2xl font-black text-slate-700 mt-1">{{ $metricas['finalizadas'] }}</div>
            <div class="text-[11px] text-slate-500 mt-0.5">Actas generadas</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs">
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Total Asistentes</div>
            <div class="text-2xl font-black text-cyan-800 mt-1">{{ $metricas['total_asistencias'] }}</div>
            <div class="text-[11px] text-slate-500 mt-0.5">{{ $metricas['asistencias_externos'] }} externos registrados</div>
        </div>
    </div>

    <!-- SECCIÓN PRINCIPAL: HISTORIAL Y FILTROS EN TIEMPO REAL -->
    <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-2xs">

        <!-- Barra de Control y Buscador en Tiempo Real -->
        <div class="p-5 border-b border-slate-200 space-y-4">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                <div>
                    <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-file-signature text-cyan-600"></i> Registro de Sesiones Realizadas
                    </h3>
                    <p class="text-xs text-slate-500">Filtra por estado (en curso / finalizadas) o busca en tiempo real por facilitador, sede o código de sesión</p>
                </div>

                <!-- Pestañas Rápidas de Estado en Tiempo Real -->
                <div class="flex items-center gap-1.5 p-1 bg-slate-100 rounded-2xl self-start md:self-center">
                    <button type="button"
                            onclick="cambiarFiltroEstado('todos')"
                            id="btnFiltroTodos"
                            class="filtro-btn px-3 py-1.5 rounded-xl text-xs font-bold transition-all bg-white text-slate-800 shadow-2xs cursor-pointer">
                        Todas ({{ $metricas['total_sesiones'] }})
                    </button>
                    <button type="button"
                            onclick="cambiarFiltroEstado('en_curso')"
                            id="btnFiltroEnCurso"
                            class="filtro-btn px-3 py-1.5 rounded-xl text-xs font-bold transition-all text-slate-600 hover:text-emerald-700 cursor-pointer flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 {{ $metricas['en_curso'] > 0 ? 'animate-pulse' : '' }}"></span>
                        <span>En Curso ({{ $metricas['en_curso'] }})</span>
                    </button>
                    <button type="button"
                            onclick="cambiarFiltroEstado('finalizada')"
                            id="btnFiltroFinalizada"
                            class="filtro-btn px-3 py-1.5 rounded-xl text-xs font-bold transition-all text-slate-600 hover:text-slate-900 cursor-pointer">
                        Finalizadas ({{ $metricas['finalizadas'] }})
                    </button>
                </div>
            </div>

            <!-- Caja del Buscador en Tiempo Real -->
            <div class="relative w-full">
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text"
                       id="buscadorSesiones"
                       oninput="aplicarFiltrosEnVivo()"
                       placeholder="Buscar por código (ej: IND-...), título de inducción, facilitador o sede..."
                       class="w-full pl-10 pr-10 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 outline-none transition-all">
                <button type="button"
                        id="btnLimpiarBuscador"
                        onclick="limpiarBuscadorSesiones()"
                        class="hidden absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-sm cursor-pointer p-1">
                    <i class="fa-solid fa-circle-xmark"></i>
                </button>
            </div>
        </div>

        @if($sesiones->isEmpty())
            <div class="p-12 text-center space-y-2">
                <i class="fa-solid fa-file-circle-question text-3xl text-slate-300"></i>
                <h4 class="text-sm font-bold text-slate-700">No se han registrado sesiones aún</h4>
                <p class="text-xs text-slate-400">Cuando un facilitador ingrese al portal con el PIN e inicie una charla, aparecerá en este historial.</p>
            </div>
        @else

            <!-- VISTA EN MÓVIL: TARJETAS EN COLUMNA (CERO SCROLL HORIZONTAL) -->
            <div class="block md:hidden p-4 space-y-3.5" id="contenedorMovilSesiones">
                @foreach($sesiones as $s)
                    @php
                        $esActiva = $s->estaActiva();
                        $estadoSlug = $esActiva ? 'en_curso' : 'finalizada';
                        $searchData = strtolower(
                            $s->codigo_sesion . ' ' .
                            ($s->induccion->titulo ?? '') . ' ' .
                            ($s->facilitador->nombre_completo ?? '') . ' ' .
                            ($s->sede->nombre ?? '') . ' ' .
                            ($esActiva ? 'en curso viva activa' : 'finalizada cerrada')
                        );
                    @endphp
                    <div class="item-sesion-card bg-white rounded-2xl border border-slate-200 shadow-2xs p-4 space-y-3"
                         data-estado="{{ $estadoSlug }}"
                         data-search="{{ $searchData }}">

                        <!-- Fila 1: Código + Estado -->
                        <div class="flex items-center justify-between gap-2 border-b border-slate-100 pb-2.5">
                            <span class="font-mono text-xs font-black px-2.5 py-1 rounded-lg bg-cyan-50 text-cyan-800 border border-cyan-200 tracking-wider">
                                {{ $s->codigo_sesion }}
                            </span>

                            @if($esActiva)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span>En Curso</span>
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                    Finalizada
                                </span>
                            @endif
                        </div>

                        <!-- Fila 2: Inducción -->
                        <div>
                            <h4 class="text-sm font-bold text-slate-900 leading-snug">
                                {{ $s->induccion->titulo ?? 'Inducción sin título' }}
                            </h4>
                        </div>

                        <!-- Fila 3: Facilitador, Sede y Fecha en Columna -->
                        <div class="bg-slate-50/80 rounded-xl p-2.5 space-y-1.5 text-xs text-slate-600 border border-slate-100">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-user-tie text-slate-400 w-3.5 text-center"></i>
                                <span class="truncate">Facilitador: <strong class="text-slate-800 font-semibold">{{ $s->facilitador->nombre_completo ?? 'N/A' }}</strong></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-location-dot text-slate-400 w-3.5 text-center"></i>
                                <span class="truncate">Sede: <strong class="text-slate-800 font-semibold">{{ $s->sede->nombre ?? 'Sede Principal' }}</strong></span>
                            </div>
                            <div class="flex items-center gap-2 font-mono text-[11px] text-slate-500">
                                <i class="fa-regular fa-calendar text-slate-400 w-3.5 text-center"></i>
                                <span>{{ $s->fecha_inicio ? $s->fecha_inicio->format('d/m/Y h:i A') : 'N/A' }}</span>
                            </div>
                        </div>

                        <!-- Fila 4: Asistentes y Botón de Acta PDF -->
                        <div class="flex items-center justify-between gap-2 pt-1">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-cyan-50 text-cyan-800 border border-cyan-200">
                                <i class="fa-solid fa-users text-cyan-600"></i>
                                <span>{{ $s->asistencias_count }} participantes</span>
                            </span>

                            <a href="{{ route('admin.inducciones.acta_pdf', $s->id) }}"
                               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-emerald-600 text-white font-bold text-xs transition-colors shadow-2xs">
                                <i class="fa-solid fa-file-pdf text-red-400"></i>
                                <span>Acta PDF</span>
                            </a>
                        </div>

                    </div>
                @endforeach
            </div>

            <!-- VISTA EN ESCRITORIO: TABLA COMPLETA -->
            <div class="hidden md:block overflow-hidden">
                <table class="w-full text-left text-xs" id="tablaEscritorioSesiones">
                    <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider text-[11px] border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">Código</th>
                            <th class="py-3 px-4">Inducción</th>
                            <th class="py-3 px-4">Facilitador</th>
                            <th class="py-3 px-4">Sede</th>
                            <th class="py-3 px-4">Fecha y Hora</th>
                            <th class="py-3 px-4 text-center">Asistentes</th>
                            <th class="py-3 px-4 text-center">Estado</th>
                            <th class="py-3 px-4 text-right">Acta Oficial</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-600">
                        @foreach($sesiones as $s)
                            @php
                                $esActiva = $s->estaActiva();
                                $estadoSlug = $esActiva ? 'en_curso' : 'finalizada';
                                $searchData = strtolower(
                                    $s->codigo_sesion . ' ' .
                                    ($s->induccion->titulo ?? '') . ' ' .
                                    ($s->facilitador->nombre_completo ?? '') . ' ' .
                                    ($s->sede->nombre ?? '') . ' ' .
                                    ($esActiva ? 'en curso viva activa' : 'finalizada cerrada')
                                );
                            @endphp
                            <tr class="item-sesion-row hover:bg-slate-50/70 transition-colors"
                                data-estado="{{ $estadoSlug }}"
                                data-search="{{ $searchData }}">
                                <td class="py-3.5 px-4 font-mono font-bold text-cyan-800">
                                    {{ $s->codigo_sesion }}
                                </td>
                                <td class="py-3.5 px-4 font-bold text-slate-800">
                                    {{ $s->induccion->titulo ?? 'N/A' }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-700 font-medium">
                                    {{ $s->facilitador->nombre_completo ?? 'N/A' }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-600">
                                    {{ $s->sede->nombre ?? 'Sede Principal' }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-500 font-mono text-[11px]">
                                    {{ $s->fecha_inicio ? $s->fecha_inicio->format('d/m/Y h:i A') : 'N/A' }}
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-cyan-50 text-cyan-800 border border-cyan-200">
                                        <i class="fa-solid fa-users text-[10px]"></i> {{ $s->asistencias_count }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if($esActiva)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> En Curso
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                            Finalizada
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <a href="{{ route('admin.inducciones.acta_pdf', $s->id) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-emerald-600 hover:text-white text-slate-700 font-bold text-xs transition-colors shadow-2xs"
                                       title="Descargar Acta Oficial en PDF">
                                        <i class="fa-solid fa-file-pdf text-red-500"></i>
                                        <span>PDF</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mensaje cuando la búsqueda no arroja resultados -->
            <div id="sinResultadosFiltro" class="hidden p-12 text-center space-y-2">
                <i class="fa-solid fa-magnifying-glass text-2xl text-slate-300 mb-1"></i>
                <h4 class="text-xs font-bold text-slate-700">No se encontraron sesiones con ese criterio</h4>
                <p class="text-[11px] text-slate-400">Intenta buscar con otra palabra clave o cambia la pestaña de estado a "Todas".</p>
            </div>

            <!-- Paginación -->
            <div class="p-4 border-t border-slate-200">
                {{ $sesiones->links() }}
            </div>
        @endif
    </div>

</div>

<script>
    let estadoFiltroActivo = 'todos'; // 'todos' | 'en_curso' | 'finalizada'

    function cambiarFiltroEstado(nuevoEstado) {
        estadoFiltroActivo = nuevoEstado;

        // Actualizar estilos de botones
        const botones = {
            'todos': document.getElementById('btnFiltroTodos'),
            'en_curso': document.getElementById('btnFiltroEnCurso'),
            'finalizada': document.getElementById('btnFiltroFinalizada')
        };

        Object.keys(botones).forEach(k => {
            const btn = botones[k];
            if (!btn) return;
            if (k === nuevoEstado) {
                btn.className = 'filtro-btn px-3 py-1.5 rounded-xl text-xs font-bold transition-all bg-white text-slate-900 shadow-2xs cursor-pointer flex items-center gap-1.5';
            } else {
                btn.className = 'filtro-btn px-3 py-1.5 rounded-xl text-xs font-bold transition-all text-slate-600 hover:text-slate-900 cursor-pointer flex items-center gap-1.5';
            }
        });

        aplicarFiltrosEnVivo();
    }

    function aplicarFiltrosEnVivo() {
        const input = document.getElementById('buscadorSesiones');
        const q = (input.value || '').toLowerCase().trim();
        const btnClear = document.getElementById('btnLimpiarBuscador');

        if (btnClear) {
            btnClear.classList.toggle('hidden', q.length === 0);
        }

        let visiblesContador = 0;

        // Filtrar tarjetas móvil
        document.querySelectorAll('.item-sesion-card').forEach(el => {
            const estadoItem = el.getAttribute('data-estado');
            const searchData = el.getAttribute('data-search') || '';

            const coincideEstado = (estadoFiltroActivo === 'todos') || (estadoItem === estadoFiltroActivo);
            const coincideTexto = (q.length === 0) || searchData.includes(q);

            const visible = coincideEstado && coincideTexto;
            el.style.display = visible ? '' : 'none';
            if (visible) visiblesContador++;
        });

        // Filtrar filas escritorio
        document.querySelectorAll('.item-sesion-row').forEach(el => {
            const estadoItem = el.getAttribute('data-estado');
            const searchData = el.getAttribute('data-search') || '';

            const coincideEstado = (estadoFiltroActivo === 'todos') || (estadoItem === estadoFiltroActivo);
            const coincideTexto = (q.length === 0) || searchData.includes(q);

            const visible = coincideEstado && coincideTexto;
            el.style.display = visible ? '' : 'none';
        });

        // Mostrar u ocultar mensaje vacío
        const sinResultados = document.getElementById('sinResultadosFiltro');
        if (sinResultados) {
            sinResultados.classList.toggle('hidden', visiblesContador > 0);
        }
    }

    function limpiarBuscadorSesiones() {
        const input = document.getElementById('buscadorSesiones');
        input.value = '';
        aplicarFiltrosEnVivo();
        input.focus();
    }
</script>
@endsection
