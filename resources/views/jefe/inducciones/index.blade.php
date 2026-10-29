@extends('layouts.jefe')

@section('title', 'Inducciones y Código PIN')

@section('content')
<div class="p-6 lg:p-8 max-w-7xl mx-auto space-y-6">

    <!-- Encabezado -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-line">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-cyan-50 text-cyan-800 border border-cyan-200 uppercase tracking-wider">
                    <i class="fa-solid fa-chalkboard-user mr-1"></i> Módulo de Inducciones
                </span>
            </div>
            <h1 class="text-2xl font-black text-brand-dark tracking-tight font-serif">Inducciones Presenciales y Código PIN</h1>
            <p class="text-xs text-slate-500">Consulta el PIN de acceso vigente para coordinar inducciones en tu área o proyectarlas en sala.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('induccion.index') }}"
               target="_blank"
               class="px-4 py-2.5 rounded-xl bg-brand-blue hover:bg-brand-dark text-white font-bold text-xs tracking-wider uppercase transition-all shadow-md flex items-center gap-2">
                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                <span>Abrir Portal Facilitador</span>
            </a>
        </div>
    </div>

    <!-- Tarjeta Destacada con el PIN de 4 Dígitos -->
    <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-48 h-48 rounded-full bg-cyan-500/10 blur-3xl pointer-events-none"></div>

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
            <div class="max-w-xl">
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 mb-3">
                    <i class="fa-solid fa-key"></i> Clave de Facilitador
                </div>
                <h2 class="text-xl sm:text-2xl font-black text-white tracking-tight">PIN Vigente para Inducciones</h2>
                <p class="text-xs text-slate-300 mt-1.5 leading-relaxed">
                    Comparte este código de 4 dígitos con cualquier colaborador o facilitador de tu equipo que vaya a dictar una charla o inducción.
                    Solo necesitarán ingresar su <strong>cédula</strong> y este <strong>PIN</strong> en la pantalla de inducción.
                </p>
            </div>

            <!-- Caja Visual del PIN -->
            <div class="flex items-center gap-4 bg-slate-950/70 p-4 sm:p-5 rounded-2xl border border-slate-700/80">
                <div>
                    <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">PIN Oficial Actual</div>
                    <div class="font-mono text-3xl font-black tracking-widest text-cyan-400 select-all mt-1">
                        {{ $pinActual }}
                    </div>
                </div>

                <div class="border-l border-slate-800 pl-4">
                    <button type="button" onclick="copiarPin('{{ $pinActual }}')"
                            id="btnCopiarPin"
                            class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold transition-colors flex items-center gap-1.5 cursor-pointer">
                        <i class="fa-regular fa-copy"></i>
                        <span>Copiar</span>
                    </button>
                    <div class="text-[9px] text-slate-500 mt-1 text-center">4 dígitos</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Catálogo de Inducciones Aplicables a tu Área -->
    <div class="bg-white rounded-3xl border border-line p-6 shadow-2xs space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-layer-group text-brand-blue"></i> Inducciones Institucionales Disponibles
                </h3>
                <p class="text-xs text-slate-500">Charlas y presentaciones oficiales disponibles para proyectar con código QR</p>
            </div>
            <span class="text-xs font-bold px-3 py-1 rounded-full bg-slate-100 text-slate-600">
                {{ $inducciones->count() }} activas
            </span>
        </div>

        @if($inducciones->isEmpty())
            <div class="p-8 text-center text-xs text-slate-400">
                No hay inducciones disponibles en este momento.
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($inducciones as $ind)
                    <div class="p-4 rounded-2xl border border-slate-200 hover:border-cyan-500/50 bg-slate-50/50 transition-all flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-cyan-100 text-cyan-800">
                                    Inducción
                                </span>
                                <div class="flex items-center gap-1 text-xs text-slate-400">
                                    @if($ind->video_url)
                                        <i class="fa-brands fa-youtube text-red-500" title="Contiene video"></i>
                                    @endif
                                    @if($ind->archivo_presentacion)
                                        <i class="fa-solid fa-file-pdf text-red-600" title="Contiene PDF"></i>
                                    @endif
                                </div>
                            </div>
                            <h4 class="font-bold text-slate-800 text-sm mb-1">{{ $ind->titulo }}</h4>
                            <p class="text-xs text-slate-500 line-clamp-2 mb-3">{{ $ind->descripcion }}</p>
                        </div>

                        <div class="pt-3 border-t border-slate-200 flex items-center justify-between text-xs">
                            <span class="text-[11px] text-slate-400">
                                Disponible para todas las sedes
                            </span>
                            <a href="{{ route('induccion.index') }}"
                               target="_blank"
                               class="text-xs font-bold text-brand-blue hover:text-brand-dark flex items-center gap-1">
                                <span>Dictar</span>
                                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Sesiones de Inducción con Participantes de tu Área -->
    <div class="bg-white rounded-3xl border border-line p-4 sm:p-6 shadow-2xs space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-brand-blue border border-blue-200 uppercase tracking-wider">
                        <i class="fa-solid fa-users-viewfinder mr-1"></i> Área: {{ $areaNombre ?? 'Tu Área' }}
                    </span>
                </div>
                <h3 class="text-base sm:text-lg font-bold text-slate-900 flex items-center gap-2 font-serif">
                    <i class="fa-solid fa-file-signature text-brand-blue"></i> Inducciones con Personal de tu Área
                </h3>
                <p class="text-xs text-slate-500">
                    Historial de inducciones donde han asistido colaboradores de tu área. Puedes consultar los participantes y sus firmas digitales.
                </p>
            </div>
            <span class="text-xs font-bold px-3 py-1 rounded-full bg-slate-100 text-slate-700 self-start sm:self-center border border-slate-200 shrink-0">
                {{ $sesionesArea->count() }} sesiones registradas
            </span>
        </div>

        @if($sesionesArea->isEmpty())
            <div class="py-12 px-4 text-center space-y-3 bg-slate-50/60 rounded-2xl border border-dashed border-slate-200">
                <div class="w-12 h-12 rounded-2xl bg-cyan-50 text-brand-blue flex items-center justify-center text-xl mx-auto shadow-2xs">
                    <i class="fa-solid fa-user-clock"></i>
                </div>
                <h4 class="font-bold text-sm text-slate-800">Aún no hay inducciones registradas para tu área</h4>
                <p class="text-xs text-slate-500 max-w-md mx-auto leading-relaxed">
                    Cuando los colaboradores de tu área asistan y firmen digitalmente una sesión de inducción, aparecerán aquí para que puedas revisar sus asistencias y descargar las actas oficiales.
                </p>
            </div>
        @else
            <!-- VISTA MÓVIL: TARJETAS EN COLUMNA (block md:hidden) -->
            <div class="block md:hidden space-y-3">
                @foreach($sesionesArea as $ses)
                    <div class="bg-slate-50/70 rounded-2xl border border-slate-200 p-4 space-y-3 transition-all hover:border-brand-blue/30">
                        <!-- Cabecera Móvil: Código y Estado -->
                        <div class="flex items-center justify-between gap-2 border-b border-slate-200/60 pb-2.5">
                            <div class="flex items-center gap-2">
                                <span class="w-7 h-7 rounded-lg bg-cyan-100/70 border border-cyan-200 text-cyan-800 flex items-center justify-center text-xs font-bold font-mono">
                                    <i class="fa-solid fa-qrcode text-[11px]"></i>
                                </span>
                                <div>
                                    <span class="font-mono font-bold text-cyan-800 text-xs tracking-wider block">
                                        {{ $ses->codigo_sesion }}
                                    </span>
                                </div>
                            </div>
                            <div>
                                @if($ses->estaActiva())
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 animate-pulse">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> En curso
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                        <i class="fa-solid fa-check text-[9px] text-emerald-600"></i> Finalizada
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Título de la Inducción y Facilitador -->
                        <div>
                            <h4 class="font-bold text-slate-900 text-sm leading-snug">
                                {{ $ses->induccion->titulo }}
                            </h4>
                            <div class="text-xs text-slate-500 mt-1 flex flex-wrap items-center gap-x-3 gap-y-1">
                                <span class="flex items-center gap-1">
                                    <i class="fa-solid fa-user-tie text-slate-400 text-[11px]"></i>
                                    <span>{{ $ses->facilitador->nombre_completo ?? 'N/A' }}</span>
                                </span>
                                <span class="flex items-center gap-1">
                                    <i class="fa-solid fa-location-dot text-rose-500 text-[10px]"></i>
                                    <span>{{ $ses->sede->nombre ?? 'Sede Principal' }}</span>
                                </span>
                            </div>
                        </div>

                        <!-- Metadatos: Fecha y Asistentes de tu Área -->
                        <div class="grid grid-cols-2 gap-2 text-xs bg-white p-2.5 rounded-xl border border-slate-200/70">
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-0.5">Fecha</span>
                                <div class="font-medium text-slate-700 text-xs flex items-center gap-1">
                                    <i class="fa-regular fa-calendar text-slate-400 text-[10px]"></i>
                                    <span>{{ $ses->fecha_inicio ? $ses->fecha_inicio->format('d/m/Y h:i A') : 'Sin fecha' }}</span>
                                </div>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-0.5 text-right">De tu Área</span>
                                <div class="flex justify-end items-center gap-1">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-bold bg-blue-50 text-brand-blue border border-blue-200">
                                        <i class="fa-solid fa-users text-[10px]"></i> {{ $ses->asistencias_area_count }}
                                    </span>
                                    @if($ses->total_asistencias_count > $ses->asistencias_area_count)
                                        <span class="text-[10px] text-slate-400 font-medium">({{ $ses->total_asistencias_count }} tot.)</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Acciones Móvil -->
                        <div class="pt-1 flex items-center gap-2">
                            <button type="button"
                                    onclick="abrirModalParticipantes({{ $ses->id }})"
                                    class="flex-1 py-2 px-3 rounded-xl bg-cyan-700 hover:bg-cyan-800 text-white font-bold text-xs flex items-center justify-center gap-1.5 transition-colors shadow-2xs cursor-pointer">
                                <i class="fa-solid fa-signature"></i>
                                <span>Ver Participantes</span>
                            </button>
                            <a href="{{ route('induccion.acta.pdf', $ses->codigo_sesion) }}"
                               class="py-2 px-3 rounded-xl bg-white hover:bg-emerald-50 hover:text-emerald-700 text-slate-700 font-bold text-xs flex items-center justify-center gap-1.5 border border-slate-200 transition-colors shadow-2xs shrink-0"
                               title="Descargar Acta Oficial en PDF">
                                <i class="fa-solid fa-file-pdf text-red-500"></i>
                                <span>Acta</span>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- VISTA ESCRITORIO: TABLA (hidden md:block) -->
            <div class="hidden md:block overflow-x-auto rounded-2xl border border-slate-200">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-600 uppercase font-bold text-[10px] border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-3.5">Código</th>
                            <th class="py-3 px-3.5">Inducción</th>
                            <th class="py-3 px-3.5">Facilitador</th>
                            <th class="py-3 px-3.5">Sede</th>
                            <th class="py-3 px-3.5">Fecha</th>
                            <th class="py-3 px-3.5 text-center">De tu Área</th>
                            <th class="py-3 px-3.5 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700 bg-white">
                        @foreach($sesionesArea as $ses)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-3 px-3.5 font-mono font-bold text-cyan-800 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-cyan-50 border border-cyan-200">
                                        <i class="fa-solid fa-qrcode text-[10px] text-cyan-600"></i>
                                        {{ $ses->codigo_sesion }}
                                    </span>
                                </td>
                                <td class="py-3 px-3.5">
                                    <div class="font-bold text-slate-900 leading-snug">{{ $ses->induccion->titulo }}</div>
                                    @if($ses->estaActiva())
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 mt-0.5">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Sesión en curso
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-3.5 text-slate-600 whitespace-nowrap">
                                    {{ $ses->facilitador->nombre_completo ?? 'N/A' }}
                                </td>
                                <td class="py-3 px-3.5 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-rose-50 text-rose-700 text-[11px] font-semibold border border-rose-100">
                                        <i class="fa-solid fa-location-dot text-[9px]"></i>
                                        {{ $ses->sede->nombre ?? 'Sede Principal' }}
                                    </span>
                                </td>
                                <td class="py-3 px-3.5 text-slate-500 font-mono whitespace-nowrap">
                                    {{ $ses->fecha_inicio ? $ses->fecha_inicio->format('d/m/Y h:i A') : '-' }}
                                </td>
                                <td class="py-3 px-3.5 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-50 text-brand-blue border border-blue-200">
                                        <i class="fa-solid fa-users text-[10px]"></i>
                                        {{ $ses->asistencias_area_count }}
                                    </span>
                                    @if($ses->total_asistencias_count > $ses->asistencias_area_count)
                                        <div class="text-[10px] text-slate-400 mt-0.5">de {{ $ses->total_asistencias_count }} en sala</div>
                                    @endif
                                </td>
                                <td class="py-3 px-3.5 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5">
                                        <button type="button"
                                                onclick="abrirModalParticipantes({{ $ses->id }})"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-cyan-700 hover:bg-cyan-800 text-white font-bold text-xs transition-colors shadow-2xs cursor-pointer"
                                                title="Ver lista de participantes y sus firmas digitales">
                                            <i class="fa-solid fa-signature"></i>
                                            <span>Firmas</span>
                                        </button>
                                        <a href="{{ route('induccion.acta.pdf', $ses->codigo_sesion) }}"
                                           class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-emerald-50 hover:text-emerald-700 text-slate-700 font-bold transition-colors border border-slate-200"
                                           title="Descargar Acta Oficial en PDF">
                                            <i class="fa-solid fa-file-pdf text-red-500"></i>
                                            <span>PDF</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>

<!-- MODAL PARA VISUALIZAR PARTICIPANTES Y FIRMAS DIGITALES -->
<div id="modalParticipantesSesion" onclick="if (event.target === this) cerrarModalParticipantes();" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 overflow-hidden">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-4xl max-h-[92vh] flex flex-col overflow-hidden border border-slate-200 animate-fade-in">
        
        <!-- Modal Header -->
        <div class="p-4 sm:p-5 border-b border-slate-200 bg-slate-50/90 shrink-0">
            <div class="flex items-start justify-between gap-3">
                <div class="space-y-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <span id="modalPartCodigo" class="font-mono text-xs font-black px-2.5 py-0.5 rounded-lg bg-cyan-100 text-cyan-900 border border-cyan-200 tracking-wider"></span>
                        <span id="modalPartEstado" class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider"></span>
                        <span id="modalPartFecha" class="text-[11px] text-slate-500 font-mono flex items-center gap-1">
                            <i class="fa-regular fa-calendar text-[10px]"></i>
                            <span></span>
                        </span>
                    </div>
                    <h3 id="modalPartTitulo" class="font-heading font-black text-base sm:text-lg text-slate-900 leading-snug"></h3>
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-600 pt-0.5">
                        <span class="flex items-center gap-1.5">
                            <i class="fa-solid fa-user-tie text-slate-400"></i>
                            <span>Facilitador: <strong id="modalPartFacilitador" class="text-slate-800 font-semibold"></strong></span>
                        </span>
                        <span class="flex items-center gap-1.5">
                            <i class="fa-solid fa-location-dot text-rose-500"></i>
                            <span>Sede: <strong id="modalPartSede" class="text-slate-800 font-semibold"></strong></span>
                        </span>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <a id="modalPartDescargarPdf"
                       href="#"
                       target="_blank"
                       class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-slate-900 hover:bg-emerald-600 text-white font-bold text-xs transition-colors shadow-2xs">
                        <i class="fa-solid fa-file-pdf text-red-400"></i>
                        <span class="hidden sm:inline">Descargar</span>
                        <span>Acta PDF</span>
                    </a>
                    <button type="button" onclick="cerrarModalParticipantes()" class="w-8 h-8 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-200/60 flex items-center justify-center transition cursor-pointer">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Barra de búsqueda interna y filtro de asistentes -->
        <div class="p-3 sm:p-4 border-b border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3 bg-white shrink-0">
            <div class="relative w-full sm:w-80">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text"
                       id="modalPartBusqueda"
                       oninput="filtrarParticipantesModal()"
                       placeholder="Buscar por colaborador, cédula, cargo..."
                       class="w-full pl-8 pr-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-hidden focus:border-cyan-600 bg-slate-50 text-slate-800">
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto justify-between sm:justify-end">
                <div class="flex items-center gap-1 bg-slate-100 p-0.5 rounded-xl text-[11px] font-bold">
                    <button type="button" onclick="cambiarFiltroTipoPart('mi_area')" id="btnPartTipoMiArea" class="px-2.5 py-1 rounded-lg bg-white text-brand-blue shadow-2xs">Mi Área</button>
                    <button type="button" onclick="cambiarFiltroTipoPart('todos')" id="btnPartTipoTodos" class="px-2.5 py-1 rounded-lg text-slate-600 hover:text-slate-900">Todos</button>
                    <button type="button" onclick="cambiarFiltroTipoPart('empleados')" id="btnPartTipoEmpleados" class="px-2.5 py-1 rounded-lg text-slate-600 hover:text-slate-900">Nómina</button>
                    <button type="button" onclick="cambiarFiltroTipoPart('externos')" id="btnPartTipoExternos" class="px-2.5 py-1 rounded-lg text-slate-600 hover:text-slate-900">Externos</button>
                </div>
                <span id="modalPartContador" class="px-3 py-1 rounded-full bg-cyan-50 text-cyan-800 font-bold text-xs border border-cyan-100 shrink-0">
                    0 participantes
                </span>
            </div>
        </div>

        <!-- Modal Body (Dual: Tabla en Escritorio / Tarjetas en Columna en Móvil sin scroll horizontal) -->
        <div class="overflow-y-auto overflow-x-hidden flex-1 p-3 sm:p-5">
            <!-- Spinner de carga -->
            <div id="modalPartCargando" class="py-12 text-center space-y-3">
                <i class="fa-solid fa-circle-notch fa-spin text-3xl text-cyan-600"></i>
                <p class="text-xs font-bold text-slate-600">Cargando participantes y firmas digitales...</p>
            </div>

            <!-- VISTA ESCRITORIO: TABLA DE PARTICIPANTES Y FIRMAS -->
            <div id="modalPartTablaContainer" class="hidden md:block overflow-x-auto rounded-2xl border border-slate-200">
                <table class="w-full text-left text-xs text-slate-800">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-bold border-b border-slate-200 sticky top-0">
                        <tr>
                            <th class="py-2.5 px-3 text-center">#</th>
                            <th class="py-2.5 px-3">Colaborador / Participante</th>
                            <th class="py-2.5 px-3">Sede</th>
                            <th class="py-2.5 px-3">Área</th>
                            <th class="py-2.5 px-3">Fecha y Hora</th>
                            <th class="py-2.5 px-3 text-center">Firma Digital</th>
                        </tr>
                    </thead>
                    <tbody id="modalPartTbody" class="divide-y divide-slate-100 bg-white">
                        <!-- Rellenado dinámicamente -->
                    </tbody>
                </table>
            </div>

            <!-- VISTA MÓVIL: EN COLUMNA VERTICAL -->
            <div id="modalPartCardsContainer" class="block md:hidden space-y-3 w-full">
                <!-- Rellenado dinámicamente con tarjetas -->
            </div>

            <!-- Estado Vacío -->
            <div id="modalPartVacio" class="hidden py-10 text-center space-y-2 bg-slate-50/60 rounded-2xl border border-dashed border-slate-200">
                <div class="w-12 h-12 rounded-2xl bg-cyan-50 text-cyan-600 flex items-center justify-center text-xl mx-auto">
                    <i class="fa-solid fa-users-slash"></i>
                </div>
                <h4 class="font-heading font-bold text-sm text-slate-800" id="modalPartVacioTitulo">Sin participantes registrados</h4>
                <p class="text-xs text-slate-500 max-w-sm mx-auto" id="modalPartVacioDesc">No se encontraron asistentes para el filtro seleccionado.</p>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="p-3 sm:p-4 border-t border-slate-200 bg-slate-50/80 flex items-center justify-between shrink-0">
            <span class="text-xs text-slate-500 flex items-center gap-1.5">
                <i class="fa-solid fa-shield-halved text-emerald-600"></i>
                <span>Firmas digitales registradas con trazabilidad y fecha oficial.</span>
            </span>
            <button type="button" onclick="cerrarModalParticipantes()" class="py-2 px-5 bg-slate-800 hover:bg-slate-900 text-white rounded-xl font-bold text-xs transition shadow-2xs cursor-pointer">
                Cerrar
            </button>
        </div>
    </div>
</div>

<!-- MODAL PARA AMPLIAR FIRMA DIGITAL (ZOOM) -->
<div id="modalZoomFirma" onclick="if (event.target === this) cerrarModalZoomFirma();" class="fixed inset-0 z-60 hidden bg-slate-900/70 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl border border-slate-200 space-y-4 animate-fade-in text-center">
        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
            <div class="text-left">
                <span class="text-[10px] uppercase font-bold text-cyan-700 tracking-wider">Firma Digital Registrada</span>
                <h4 id="zoomFirmaNombre" class="font-bold text-sm text-slate-800"></h4>
            </div>
            <button type="button" onclick="cerrarModalZoomFirma()" class="w-7 h-7 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 flex items-center justify-center cursor-pointer">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 flex items-center justify-center min-h-[160px]">
            <img id="zoomFirmaImg" src="" alt="Firma Ampliada" class="max-h-48 max-w-full object-contain filter contrast-125" />
        </div>
        <button type="button" onclick="cerrarModalZoomFirma()" class="w-full py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition">
            Volver a la lista
        </button>
    </div>
</div>

<script>
    function copiarPin(pin) {
        navigator.clipboard.writeText(pin).then(() => {
            const btn = document.getElementById('btnCopiarPin');
            btn.innerHTML = '<i class="fa-solid fa-check text-emerald-400"></i> <span>Copiado</span>';
            setTimeout(() => {
                btn.innerHTML = '<i class="fa-regular fa-copy"></i> <span>Copiar</span>';
            }, 2000);
        });
    }

    // ==========================================
    // LÓGICA DEL MODAL DE PARTICIPANTES Y FIRMAS
    // ==========================================
    let participantesActuales = [];
    let tipoParticipanteFiltro = 'mi_area'; // 'mi_area' | 'todos' | 'empleados' | 'externos'

    async function abrirModalParticipantes(sesionId) {
        const modal = document.getElementById('modalParticipantesSesion');
        const cargando = document.getElementById('modalPartCargando');
        const tabla = document.getElementById('modalPartTablaContainer');
        const cards = document.getElementById('modalPartCardsContainer');
        const vacio = document.getElementById('modalPartVacio');

        modal.classList.remove('hidden');
        cargando.classList.remove('hidden');
        tabla.classList.add('hidden');
        cards.classList.add('hidden');
        vacio.classList.add('hidden');

        document.getElementById('modalPartBusqueda').value = '';
        tipoParticipanteFiltro = 'mi_area';
        actualizarBotonesTipoPart('mi_area');

        try {
            const res = await fetch(`/jefe/inducciones/sesiones/${sesionId}/participantes-json`);
            const data = await res.json();

            if (!data.success) {
                alert(data.message || 'Error al cargar los participantes.');
                cerrarModalParticipantes();
                return;
            }

            const sesion = data.sesion;
            participantesActuales = data.participantes || [];

            // Llenar encabezado del modal
            document.getElementById('modalPartCodigo').textContent = sesion.codigo;
            document.getElementById('modalPartTitulo').textContent = sesion.induccion_titulo;
            document.getElementById('modalPartFacilitador').textContent = sesion.facilitador;
            document.getElementById('modalPartSede').textContent = sesion.sede;
            document.getElementById('modalPartFecha').querySelector('span').textContent = sesion.fecha_inicio;
            document.getElementById('modalPartDescargarPdf').href = sesion.acta_pdf_url;

            const estadoBadge = document.getElementById('modalPartEstado');
            if (sesion.es_activa) {
                estadoBadge.className = 'inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800';
                estadoBadge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse mr-1"></span> En Curso';
            } else {
                estadoBadge.className = 'inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-200 text-slate-700';
                estadoBadge.textContent = 'Finalizada';
            }

            cargando.classList.add('hidden');
            tabla.classList.remove('hidden');
            cards.classList.remove('hidden');

            // Si no hay colaboradores de su área en esta sesión, cambiar por defecto a 'todos'
            if (sesion.total_mi_area === 0 && participantesActuales.length > 0) {
                tipoParticipanteFiltro = 'todos';
                actualizarBotonesTipoPart('todos');
            }

            filtrarParticipantesModal();

        } catch (err) {
            console.error('Error al consultar participantes:', err);
            alert('Ocurrió un error de conexión al cargar las asistencias.');
            cerrarModalParticipantes();
        }
    }

    function renderizarParticipantesModal(lista) {
        const tbody = document.getElementById('modalPartTbody');
        const cardsCont = document.getElementById('modalPartCardsContainer');
        const vacio = document.getElementById('modalPartVacio');
        const contador = document.getElementById('modalPartContador');

        tbody.innerHTML = '';
        cardsCont.innerHTML = '';
        contador.textContent = `${lista.length} participante(s)`;

        if (lista.length === 0) {
            vacio.classList.remove('hidden');
            return;
        }

        vacio.classList.add('hidden');

        lista.forEach((p, idx) => {
            const num = idx + 1;
            const badgeMiArea = p.es_mi_area
                ? '<span class="inline-flex items-center px-1.5 py-0.5 rounded bg-blue-100 text-brand-blue text-[9px] font-extrabold uppercase border border-blue-200">De Tu Área</span>'
                : '';

            const badgeTipo = p.es_externo
                ? '<span class="inline-flex items-center px-1.5 py-0.5 rounded bg-amber-100 text-amber-800 text-[9px] font-bold uppercase">Externo</span>'
                : '<span class="inline-flex items-center px-1.5 py-0.5 rounded bg-slate-100 text-slate-700 text-[9px] font-bold uppercase">Nómina</span>';

            const firmaHtml = p.tiene_firma && p.firma_url
                ? `<div onclick="ampliarFirma('${p.firma_url}', '${p.nombre_completo.replace(/'/g, "\\'")}')"
                        class="bg-white p-1 rounded-xl border border-slate-200 shadow-2xs inline-flex items-center justify-center cursor-pointer hover:border-cyan-500 transition hover:scale-105 group"
                        title="Clic para ampliar firma">
                       <img src="${p.firma_url}" alt="Firma de ${p.nombre_completo}" class="h-9 max-w-[130px] object-contain" />
                       <i class="fa-solid fa-magnifying-glass-plus text-slate-300 group-hover:text-cyan-600 text-[10px] ml-1"></i>
                   </div>`
                : `<span class="text-[11px] text-slate-400 italic">Sin firma</span>`;

            // 1. Fila de tabla para escritorio
            const tr = document.createElement('tr');
            tr.className = `hover:bg-slate-50/70 transition-colors ${p.es_mi_area ? 'bg-blue-50/20' : ''}`;
            tr.innerHTML = `
                <td class="py-2.5 px-3 text-center font-mono text-[11px] text-slate-400 font-bold">${num}</td>
                <td class="py-2.5 px-3">
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <span class="font-bold text-slate-900 text-xs">${p.nombre_completo}</span>
                        ${badgeMiArea}
                        ${badgeTipo}
                    </div>
                    <div class="text-[10px] text-slate-500 flex items-center gap-1.5 mt-0.5">
                        <span>CC: <strong>${p.identificacion}</strong></span>
                        <span>&bull;</span>
                        <span>${p.cargo}</span>
                    </div>
                </td>
                <td class="py-2.5 px-3 whitespace-nowrap">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-rose-50 text-rose-700 text-[10px] font-semibold border border-rose-100">
                        <i class="fa-solid fa-location-dot text-[8px]"></i>
                        <span>${p.sede}</span>
                    </span>
                </td>
                <td class="py-2.5 px-3 whitespace-nowrap">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md ${p.es_mi_area ? 'bg-blue-100/80 text-brand-blue font-bold border border-blue-200' : 'bg-slate-100 text-slate-700 text-[10px]'}">
                        <i class="fa-solid fa-sitemap text-[8px]"></i>
                        <span>${p.area}</span>
                    </span>
                </td>
                <td class="py-2.5 px-3 text-slate-500 text-[11px] font-mono whitespace-nowrap">
                    ${p.fecha_registro}
                </td>
                <td class="py-2 px-3 text-center whitespace-nowrap">
                    ${firmaHtml}
                </td>
            `;
            tbody.appendChild(tr);

            // 2. Tarjeta para vista móvil
            const card = document.createElement('div');
            card.className = `p-3.5 rounded-2xl border ${p.es_mi_area ? 'border-blue-200 bg-blue-50/30' : 'border-slate-200 bg-slate-50/60'} space-y-2.5`;
            card.innerHTML = `
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span class="font-bold text-slate-900 text-xs">${p.nombre_completo}</span>
                            ${badgeMiArea}
                            ${badgeTipo}
                        </div>
                        <div class="text-[10px] text-slate-500 mt-0.5">
                            CC: <strong>${p.identificacion}</strong> &bull; ${p.cargo}
                        </div>
                    </div>
                    <span class="font-mono text-[10px] font-bold text-slate-400">#${num}</span>
                </div>

                <div class="flex flex-wrap items-center gap-1.5 text-[10px]">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-rose-50 text-rose-700 font-semibold border border-rose-100">
                        <i class="fa-solid fa-location-dot text-[8px]"></i> ${p.sede}
                    </span>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded ${p.es_mi_area ? 'bg-blue-100 text-brand-blue font-bold' : 'bg-slate-100 text-slate-600'}">
                        <i class="fa-solid fa-sitemap text-[8px]"></i> ${p.area}
                    </span>
                    <span class="text-slate-400 font-mono ml-auto">${p.fecha_registro}</span>
                </div>

                <div class="pt-2 border-t border-slate-200/60 flex items-center justify-between">
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Firma Digital:</span>
                    ${firmaHtml}
                </div>
            `;
            cardsCont.appendChild(card);
        });
    }

    function filtrarParticipantesModal() {
        const query = (document.getElementById('modalPartBusqueda')?.value || '').toLowerCase().trim();

        const filtrados = participantesActuales.filter(p => {
            // Filtro por tipo
            if (tipoParticipanteFiltro === 'mi_area' && !p.es_mi_area) return false;
            if (tipoParticipanteFiltro === 'empleados' && p.es_externo) return false;
            if (tipoParticipanteFiltro === 'externos' && !p.es_externo) return false;

            // Filtro por texto
            if (!query) return true;

            return (p.nombre_completo && p.nombre_completo.toLowerCase().includes(query)) ||
                   (p.identificacion && p.identificacion.toLowerCase().includes(query)) ||
                   (p.cargo && p.cargo.toLowerCase().includes(query)) ||
                   (p.area && p.area.toLowerCase().includes(query)) ||
                   (p.sede && p.sede.toLowerCase().includes(query));
        });

        renderizarParticipantesModal(filtrados);
    }

    function cambiarFiltroTipoPart(tipo) {
        tipoParticipanteFiltro = tipo;
        actualizarBotonesTipoPart(tipo);
        filtrarParticipantesModal();
    }

    function actualizarBotonesTipoPart(tipo) {
        const btnMiArea = document.getElementById('btnPartTipoMiArea');
        const btnTodos = document.getElementById('btnPartTipoTodos');
        const btnEmp = document.getElementById('btnPartTipoEmpleados');
        const btnExt = document.getElementById('btnPartTipoExternos');

        const activeClass = 'px-2.5 py-1 rounded-lg bg-white text-brand-blue shadow-2xs font-bold';
        const inactiveClass = 'px-2.5 py-1 rounded-lg text-slate-600 hover:text-slate-900';

        if (btnMiArea) btnMiArea.className = tipo === 'mi_area' ? activeClass : inactiveClass;
        if (btnTodos) btnTodos.className = tipo === 'todos' ? activeClass : inactiveClass;
        if (btnEmp) btnEmp.className = tipo === 'empleados' ? activeClass : inactiveClass;
        if (btnExt) btnExt.className = tipo === 'externos' ? activeClass : inactiveClass;
    }

    function cerrarModalParticipantes() {
        document.getElementById('modalParticipantesSesion').classList.add('hidden');
        participantesActuales = [];
    }

    // Modal Zoom Firma
    function ampliarFirma(url, nombre) {
        document.getElementById('zoomFirmaImg').src = url;
        document.getElementById('zoomFirmaNombre').textContent = nombre;
        document.getElementById('modalZoomFirma').classList.remove('hidden');
    }

    function cerrarModalZoomFirma() {
        document.getElementById('modalZoomFirma').classList.add('hidden');
    }
</script>
@endsection
