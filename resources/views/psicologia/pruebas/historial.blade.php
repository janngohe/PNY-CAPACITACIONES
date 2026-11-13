@extends(request()->routeIs('admin.*') ? 'layouts.admin' : 'layouts.psicologia')

@section('title', 'Historial de Pruebas')
@section('page_title', 'Pruebas Psicotécnicas')
@section('page_subtitle', 'Historial General y Custodia de Evaluaciones')

@section('content')
<div class="space-y-6">

    <!-- Encabezado Principal y Pestañas Activas / Papelera -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            @if($enPapelera)
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-rose-100 text-rose-800 text-[11px] font-bold">
                        <i class="fa-solid fa-trash-can text-rose-600"></i> Papelera de Reciclaje
                    </span>
                    <span class="text-xs text-slate-400 font-medium">{{ $totalEnPapelera }} registro(s)</span>
                </div>
                <h1 class="text-xl font-bold text-slate-900 mt-1">Papelera de Evaluaciones</h1>
                <p class="text-xs text-slate-500 mt-0.5">Registros eliminados para liberar la cédula. Puedes restablecerlos o vaciar la papelera de forma definitiva.</p>
            @else
                <h1 class="text-xl font-bold text-slate-900">Historial de Aplicaciones y Consentimientos</h1>
                <p class="text-xs text-slate-500 mt-1">Registro de evaluaciones psicotécnicas, custodia de consentimientos GH-FO-025 y resultados</p>
            @endif
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            @if($enPapelera)
                <!-- Volver al Historial Activo -->
                <a href="{{ request()->routeIs('admin.*') ? route('admin.psicologia.historial') : route('psicologia.pruebas.historial') }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 px-3.5 py-2 text-xs font-bold text-slate-700 shadow-2xs transition-colors">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                    <span>Volver al Historial Activo</span>
                </a>

                @if($totalEnPapelera > 0)
                    <!-- Vaciar Papelera -->
                    <button type="button"
                            onclick="abrirModalVaciarPapelera()"
                            class="inline-flex items-center gap-2 rounded-xl bg-rose-600 hover:bg-rose-700 active:scale-95 px-3.5 py-2 text-xs font-bold text-white shadow-2xs transition-all cursor-pointer">
                        <i class="fa-solid fa-trash text-xs"></i>
                        <span>Vaciar Papelera ({{ $totalEnPapelera }})</span>
                    </button>
                @endif
            @else
                <!-- Botón a la Papelera -->
                <a href="{{ request()->routeIs('admin.*') ? route('admin.psicologia.historial', ['papelera' => 1]) : route('psicologia.pruebas.historial', ['papelera' => 1]) }}"
                   class="inline-flex items-center gap-2 rounded-xl {{ $totalEnPapelera > 0 ? 'bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }} px-3.5 py-2 text-xs font-bold transition-all shadow-2xs">
                    <i class="fa-solid fa-trash-can text-xs {{ $totalEnPapelera > 0 ? 'text-rose-600' : 'text-slate-500' }}"></i>
                    <span>Papelera</span>
                    @if($totalEnPapelera > 0)
                        <span class="inline-flex items-center justify-center px-1.5 py-0.5 rounded-full bg-rose-600 text-white font-mono text-[10px]">
                            {{ $totalEnPapelera }}
                        </span>
                    @endif
                </a>

                @if(!request()->routeIs('admin.*'))
                    <a href="{{ route('psicologia.pruebas.realizar') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 px-4 py-2 text-xs font-bold text-white shadow-2xs transition-all hover:scale-[1.02] shrink-0">
                        <i class="fa-solid fa-qrcode text-xs"></i>
                        Proyectar Código QR
                    </a>
                @endif
            @endif
        </div>
    </div>

    <!-- Barra de Filtros en Vivo (Sin botón de filtrar) -->
    <div class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-2xs">
        <div class="flex flex-col md:flex-row md:items-center gap-3">
            
            <!-- Buscador en Vivo por Texto -->
            <div class="relative flex-1">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text"
                       id="inputBuscarVivo"
                       name="buscar"
                       value="{{ request('buscar') }}"
                       autocomplete="off"
                       placeholder="Escribe para filtrar en vivo por evaluado, cédula o cargo..."
                       class="w-full rounded-xl border border-slate-200 bg-slate-50/50 pl-9 pr-8 py-2 text-xs text-slate-800 placeholder-slate-400 focus:border-brand-blue focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-blue/20">
                <span id="spinnerFiltroVivo" class="hidden absolute right-3 top-1/2 -translate-y-1/2 text-brand-blue text-xs">
                    <i class="fa-solid fa-circle-notch fa-spin"></i>
                </span>
            </div>

            <div class="flex flex-wrap sm:flex-nowrap items-center gap-2">
                <!-- Selector de Estado en Vivo -->
                <div class="w-full sm:w-48">
                    <select id="selectEstadoVivo"
                            name="estado"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3 py-2 text-xs text-slate-800 focus:border-brand-blue focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-blue/20 cursor-pointer">
                        <option value="">-- Todos los estados --</option>
                        <option value="FINALIZADA" {{ request('estado') === 'FINALIZADA' ? 'selected' : '' }}>Finalizadas (2/2)</option>
                        <option value="EN_CURSO" {{ request('estado') === 'EN_CURSO' ? 'selected' : '' }}>En Curso</option>
                        <option value="BORRADOR" {{ request('estado') === 'BORRADOR' ? 'selected' : '' }}>Sin iniciar</option>
                    </select>
                </div>

                <!-- Selector de Fecha en Vivo -->
                <div class="w-full sm:w-40">
                    <input type="date"
                           id="inputFechaVivo"
                           name="fecha"
                           value="{{ request('fecha') }}"
                           title="Filtrar por fecha de aplicación"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-2.5 py-2 text-xs text-slate-800 focus:border-brand-blue focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-blue/20 cursor-pointer">
                </div>

                <!-- Botón Limpiar Filtros (solo visible cuando hay algún filtro escrito/seleccionado) -->
                <button type="button"
                        id="btnLimpiarFiltrosVivo"
                        onclick="limpiarFiltrosEnVivo()"
                        title="Limpiar filtros"
                        class="{{ $filtrosActivos ? 'inline-flex' : 'hidden' }} items-center justify-center gap-1.5 rounded-xl border border-slate-200 bg-white hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200 px-3 py-2 text-xs font-bold text-slate-600 transition-colors cursor-pointer shrink-0">
                    <i class="fa-solid fa-xmark text-xs"></i>
                    <span>Limpiar</span>
                </button>
            </div>

        </div>
    </div>

    <!-- Contenedor Principal de Registros actualizable en vivo -->
    <div id="contenedorResultadosHistorial" class="space-y-4">
        @if($aplicaciones->isEmpty())
            <div class="rounded-2xl border border-slate-200/80 bg-white p-12 text-center shadow-2xs">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-slate-50 text-slate-400 mb-3">
                    <i class="fa-solid fa-clipboard-question text-2xl"></i>
                </div>
                <h3 class="text-sm font-bold text-slate-700">No se encontraron evaluaciones</h3>
                <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">No hay registros que coincidan con los criterios de búsqueda.</p>
                @if($filtrosActivos)
                    <div class="mt-4">
                        <button type="button" onclick="limpiarFiltrosEnVivo()" class="inline-flex items-center gap-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 px-4 py-2 text-xs font-bold text-slate-700 transition-colors cursor-pointer">
                            <i class="fa-solid fa-rotate-left text-xs"></i> Restablecer filtros
                        </button>
                    </div>
                @endif
            </div>
        @else

            <!-- 1. VISTA MÓVIL: Tarjetas Adaptativas con Estilo Ejecutivo (< md) -->
            <div class="block md:hidden space-y-3.5">
                @foreach($aplicaciones as $app)
                    @php
                        $badge = $app->badgeEstadoPaca();
                        $urlPdfInline = !$enPapelera ? (route('psicologia.consentimientos.pdf', $app) . '?inline=1') : '#';
                        $urlPdfDescarga = !$enPapelera ? route('psicologia.consentimientos.pdf', $app) : '#';
                        $fechaIso = ($app->fecha_hora_aceptacion ?? $app->created_at)->format('Y-m-d');
                        $estadoFiltro = ($app->pacaFinalizada() && $app->valantiFinalizada())
                            ? 'FINALIZADA'
                            : (($app->totalRespondidasPaca() > 0 || $app->pacaFinalizada()) ? 'EN_CURSO' : 'BORRADOR');
                        $textoBusqueda = mb_strtolower($app->nombre_completo . ' ' . $app->identificacion . ' ' . ($app->cargo_aspira ?? '') . ' ' . ($app->sede?->nombre ?? ''), 'UTF-8');
                    @endphp
                    <div class="item-historial-vivo rounded-2xl border border-slate-300/80 bg-white p-4 shadow-sm space-y-3 transition-all ring-1 ring-slate-900/5"
                         data-search="{{ $textoBusqueda }}"
                         data-estado="{{ $estadoFiltro }}"
                         data-fecha="{{ $fechaIso }}">
                        
                        <!-- Fila Superior: Logo de Usuario + Nombre + Estado -->
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="h-10 w-10 rounded-2xl bg-indigo-50 border border-indigo-200/80 text-indigo-600 flex items-center justify-center shadow-2xs shrink-0 select-none">
                                    <i class="fa-solid fa-circle-user text-2xl"></i>
                                </div>
                                <div class="min-w-0">
                                    <span class="font-bold text-slate-900 text-sm block leading-snug truncate">
                                        {{ $app->nombre_completo }}
                                    </span>
                                    <span class="font-mono bg-slate-100 text-slate-700 px-1.5 py-0.5 rounded font-bold text-[10px] inline-block mt-0.5 whitespace-nowrap">
                                        CC {{ $app->identificacion }}
                                    </span>
                                </div>
                            </div>
                            <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-[10px] font-bold border shadow-2xs {{ $badge['clase'] }} shrink-0 whitespace-nowrap">
                                <i class="fa-solid {{ $badge['icono'] }} text-[9px]"></i>
                                {{ $badge['texto'] }}
                            </span>
                        </div>

                        <!-- Fila de Sede a lo largo (Horizontal completa sin saltos de línea) -->
                        @if($app->sede)
                            <div class="flex items-center gap-2 text-xs text-sky-800 bg-sky-50/90 border border-sky-200/90 px-3 py-1.5 rounded-xl shadow-2xs">
                                <i class="fa-solid fa-location-dot text-sky-600 text-xs shrink-0"></i>
                                <span class="text-[11px] font-semibold whitespace-nowrap truncate">Sede: <strong class="font-bold text-sky-950">{{ $app->sede->nombre }}</strong></span>
                            </div>
                        @endif

                        <!-- Fila Central: Datos de Cargo, Pruebas y Fecha -->
                        <div class="grid grid-cols-2 gap-2 p-3 rounded-xl bg-slate-50 text-[11px] border border-slate-200/70">
                            <div>
                                <span class="text-slate-400 block text-[10px] uppercase font-bold tracking-wider">Área / Cargo</span>
                                <span class="font-bold text-slate-800 truncate block">{{ $app->cargo_aspira ?: '—' }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[10px] uppercase font-bold tracking-wider">Aplicación</span>
                                <span class="font-bold text-slate-800">
                                    {{ ($app->fecha_hora_aceptacion ?? $app->created_at)->format('d/m/Y') }}
                                    @if($app->edad) · {{ $app->edad }} años @endif
                                </span>
                            </div>
                        </div>

                        <!-- Fila Inferior: Botones de Acción -->
                        <div class="flex items-center justify-between gap-2 pt-2 border-t border-slate-100 flex-wrap">
                            @if($enPapelera)
                                <div class="text-[11px] text-slate-500">
                                    <i class="fa-solid fa-clock-rotate-left text-rose-500"></i>
                                    Eliminado: <strong class="text-slate-700">{{ $app->deleted_at->format('d/m/Y H:i') }}</strong>
                                </div>
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <!-- Restablecer -->
                                    <form method="POST" action="{{ request()->routeIs('admin.*') ? route('admin.psicologia.pruebas.restablecer', $app->id) : route('psicologia.pruebas.restablecer', $app->id) }}">
                                        @csrf
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 rounded-xl bg-emerald-50 text-emerald-800 hover:bg-emerald-100 border border-emerald-200 px-2.5 py-1.5 text-xs font-bold transition-colors cursor-pointer shadow-2xs">
                                            <i class="fa-solid fa-rotate-left text-xs text-emerald-600"></i>
                                            <span>Restablecer</span>
                                        </button>
                                    </form>
                                    <!-- Destruir Definitivo -->
                                    <button type="button"
                                            onclick="abrirModalDestruirDefinitivo('{{ request()->routeIs('admin.*') ? route('admin.psicologia.pruebas.destruir', $app->id) : route('psicologia.pruebas.destruir', $app->id) }}', '{{ addslashes($app->nombre_completo) }}', '{{ $app->identificacion }}')"
                                            class="inline-flex items-center gap-1 rounded-xl bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 px-2.5 py-1.5 text-xs font-bold transition-colors cursor-pointer shadow-2xs"
                                            title="Eliminar definitivamente">
                                        <i class="fa-solid fa-trash-can text-xs text-rose-600"></i>
                                        <span>Destruir</span>
                                    </button>
                                </div>
                            @else
                                <!-- Ver Consentimiento -->
                                <div class="flex items-center gap-1.5">
                                    <button type="button"
                                            onclick="abrirModalPdf('{{ $urlPdfInline }}', '{{ $urlPdfDescarga }}', '{{ addslashes($app->nombre_completo) }}')"
                                            class="inline-flex items-center gap-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 px-3 py-1.5 text-xs font-bold transition-colors cursor-pointer shadow-2xs">
                                        <i class="fa-solid fa-file-pdf text-xs text-rose-600"></i>
                                        <span>Ver Consentimiento</span>
                                    </button>
                                </div>

                                <!-- Botones para Entrar a VALANTI y PAC-A -->
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    @if($app->valantiFinalizada())
                                        <a href="{{ request()->routeIs('admin.*') ? route('admin.psicologia.valanti.resultado', $app) : route('psicologia.valanti.resultado', $app) }}"
                                           class="inline-flex items-center gap-1.5 rounded-xl bg-sky-50 text-sky-800 hover:bg-sky-100 border border-sky-200 px-3 py-1.5 text-xs font-bold transition-colors shadow-2xs">
                                            <i class="fa-solid fa-scale-balanced text-xs text-sky-600"></i>
                                            <span>VALANTI</span>
                                        </a>
                                    @endif
                                    @if($app->pacaFinalizada())
                                        <a href="{{ request()->routeIs('admin.*') ? route('admin.psicologia.paca.resultado', $app) : route('psicologia.paca.resultado', $app) }}"
                                           class="inline-flex items-center gap-1.5 rounded-xl bg-indigo-50 text-indigo-800 hover:bg-indigo-100 border border-indigo-200 px-3 py-1.5 text-xs font-bold transition-colors shadow-2xs">
                                            <i class="fa-solid fa-chart-simple text-xs text-indigo-600"></i>
                                            <span>PAC-A</span>
                                        </a>
                                    @endif

                                    @if(!$app->valantiFinalizada())
                                        @php
                                            $pathCont = route('psicologia.evaluacion.paso2', ['codigo' => $app->codigo_sesion ?: 'PSI-PNY', 'evaluacion' => $app->id], false);
                                        @endphp
                                        <button type="button"
                                                onclick="abrirModalQrContinuar('{{ $pathCont }}', '{{ addslashes($app->nombre_completo) }}', 'CC {{ $app->identificacion }}', 'Prueba 1: Cuestionario VALANTI')"
                                                class="inline-flex items-center gap-1.5 rounded-xl bg-amber-50 text-amber-800 border border-amber-200 hover:bg-amber-100 px-3 py-1.5 text-xs font-bold transition-colors cursor-pointer shadow-2xs">
                                            <i class="fa-solid fa-qrcode text-xs text-amber-600"></i>
                                            <span>QR P1</span>
                                        </button>
                                    @elseif(!$app->pacaFinalizada())
                                        @php
                                            $pathCont = route('psicologia.evaluacion.paso3', ['codigo' => $app->codigo_sesion ?: 'PSI-PNY', 'evaluacion' => $app->id], false);
                                        @endphp
                                        <button type="button"
                                                onclick="abrirModalQrContinuar('{{ $pathCont }}', '{{ addslashes($app->nombre_completo) }}', 'CC {{ $app->identificacion }}', 'Prueba 2: Cuestionario PAC-A')"
                                                class="inline-flex items-center gap-1.5 rounded-xl bg-amber-50 text-amber-800 border border-amber-200 hover:bg-amber-100 px-3 py-1.5 text-xs font-bold transition-colors cursor-pointer shadow-2xs">
                                            <i class="fa-solid fa-qrcode text-xs text-amber-600"></i>
                                            <span>QR P2</span>
                                        </button>
                                    @endif

                                    <!-- Botón Eliminar Persona / Enviar a Papelera -->
                                    <button type="button"
                                            onclick="abrirModalEliminar('{{ request()->routeIs('admin.*') ? route('admin.psicologia.pruebas.eliminar', $app) : route('psicologia.pruebas.eliminar', $app) }}', '{{ addslashes($app->nombre_completo) }}', '{{ $app->identificacion }}')"
                                            class="inline-flex items-center gap-1 rounded-xl bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 px-2.5 py-1.5 text-xs font-bold transition-colors cursor-pointer shadow-2xs"
                                            title="Enviar a papelera y liberar cédula">
                                        <i class="fa-solid fa-trash-can text-xs text-rose-600"></i>
                                        <span>Eliminar</span>
                                    </button>
                                </div>
                            @endif
                        </div>

                    </div>
                @endforeach
            </div>

            <!-- 2. VISTA ESCRITORIO: Panel Ejecutivo con Cabecera Oscura y Logo de Usuario (>= md) -->
            <div class="hidden md:block rounded-2xl border border-slate-300/80 bg-white shadow-sm ring-1 ring-slate-900/5 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <!-- Cabecera Ejecutiva Navy / Indigo -->
                        <thead class="bg-gradient-to-r from-slate-900 via-slate-800 to-indigo-950 text-white">
                            <tr class="text-[11px] font-extrabold uppercase tracking-wider text-slate-200">
                                <th class="py-4 px-5">
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-user text-indigo-400"></i>
                                        <span>Persona Evaluada</span>
                                    </div>
                                </th>
                                <th class="py-4 px-4">
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-file-signature text-rose-400"></i>
                                        <span>Consentimiento GH-FO-025</span>
                                    </div>
                                </th>
                                <th class="py-4 px-4">
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-calendar-check text-sky-400"></i>
                                        <span>Pruebas / Fecha</span>
                                    </div>
                                </th>
                                <th class="py-4 px-4">
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-bars-progress text-emerald-400"></i>
                                        <span>Estado</span>
                                    </div>
                                </th>
                                <th class="py-4 px-5 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        @if($enPapelera)
                                            <i class="fa-solid fa-recycle text-amber-400"></i>
                                            <span>Acciones de Papelera</span>
                                        @else
                                            <i class="fa-solid fa-arrow-right-to-bracket text-amber-400"></i>
                                            <span>Entrar a Pruebas</span>
                                        @endif
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs">
                            @foreach($aplicaciones as $app)
                                @php
                                    $badge = $app->badgeEstadoPaca();
                                    $urlPdfInline = !$enPapelera ? (route('psicologia.consentimientos.pdf', $app) . '?inline=1') : '#';
                                    $urlPdfDescarga = !$enPapelera ? route('psicologia.consentimientos.pdf', $app) : '#';
                                    $fechaIso = ($app->fecha_hora_aceptacion ?? $app->created_at)->format('Y-m-d');
                                    $estadoFiltro = ($app->pacaFinalizada() && $app->valantiFinalizada())
                                        ? 'FINALIZADA'
                                        : (($app->totalRespondidasPaca() > 0 || $app->pacaFinalizada()) ? 'EN_CURSO' : 'BORRADOR');
                                    $textoBusqueda = mb_strtolower($app->nombre_completo . ' ' . $app->identificacion . ' ' . ($app->cargo_aspira ?? '') . ' ' . ($app->sede?->nombre ?? ''), 'UTF-8');
                                @endphp
                                <tr class="item-historial-vivo odd:bg-white even:bg-slate-50/70 hover:bg-indigo-50/40 transition-colors duration-150"
                                    data-search="{{ $textoBusqueda }}"
                                    data-estado="{{ $estadoFiltro }}"
                                    data-fecha="{{ $fechaIso }}">

                                    <!-- 1. Persona Evaluada (Con Logo de Usuario) -->
                                    <td class="py-3.5 px-5">
                                        <div class="flex items-center gap-3">
                                            <div class="h-10 w-10 rounded-2xl bg-indigo-50 border border-indigo-200/80 text-indigo-600 flex items-center justify-center shadow-2xs shrink-0 select-none">
                                                <i class="fa-solid fa-circle-user text-2xl"></i>
                                            </div>
                                            <div class="min-w-0">
                                                <span class="font-bold text-slate-900 block leading-tight text-sm truncate max-w-[240px]">
                                                    {{ $app->nombre_completo }}
                                                </span>
                                                <div class="flex items-center gap-1.5 mt-1 text-[11px] flex-wrap">
                                                    <span class="font-mono bg-slate-100 text-slate-700 px-1.5 py-0.5 rounded font-bold text-[10px]">CC {{ $app->identificacion }}</span>
                                                    @if($app->sede)
                                                        <span class="inline-flex items-center gap-1 text-[10px] font-medium bg-sky-50 text-sky-700 border border-sky-100 px-2 py-0.5 rounded-full whitespace-nowrap">
                                                            <i class="fa-solid fa-location-dot text-[9px] text-sky-600"></i>
                                                            <span class="whitespace-nowrap">{{ $app->sede->nombre }}</span>
                                                        </span>
                                                    @endif
                                                    @if($app->cargo_aspira)
                                                        <span class="text-slate-500 font-semibold truncate max-w-[150px]" title="{{ $app->cargo_aspira }}">
                                                            • {{ $app->cargo_aspira }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- 2. CONSENTIMIENTO: Solo disponible en Historial Activo -->
                                    <td class="py-3.5 px-4">
                                        @if($enPapelera)
                                            <div class="text-[11px] text-slate-400 italic flex items-center gap-1.5 select-none">
                                                <i class="fa-solid fa-file-circle-xmark text-slate-300 text-sm"></i>
                                                <span>No disponible</span>
                                            </div>
                                        @else
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                <!-- Botón Previsualizar PDF en Modal -->
                                                <button type="button"
                                                        onclick="abrirModalPdf('{{ $urlPdfInline }}', '{{ $urlPdfDescarga }}', '{{ addslashes($app->nombre_completo) }}')"
                                                        title="Previsualizar consentimiento en pantalla"
                                                        class="inline-flex items-center gap-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 px-2.5 py-1 text-[11px] font-bold transition-all shadow-2xs hover:scale-[1.02] cursor-pointer">
                                                    <i class="fa-solid fa-file-pdf text-rose-600 text-xs"></i>
                                                    <span>Ver Consentimiento</span>
                                                </button>
                                            </div>
                                            <div class="text-[10.5px] text-emerald-700 font-semibold mt-1 flex items-center gap-1">
                                                <i class="fa-solid fa-circle-check text-[9px]"></i>
                                                <span>Firmado digitalmente</span>
                                            </div>
                                        @endif
                                    </td>

                                    <!-- 3. Pruebas / Fecha en Cápsula Estilizada -->
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        @if($enPapelera)
                                            <div class="inline-block p-2 rounded-xl bg-rose-50/80 border border-rose-200/60 text-xs">
                                                <span class="text-rose-600 block font-bold text-[10px] uppercase">Eliminado</span>
                                                <strong class="text-slate-900 block font-mono text-[11px]">{{ $app->deleted_at->format('d/m/Y H:i') }}</strong>
                                                <span class="text-[10px] text-slate-500 font-medium">{{ $app->deleted_at->diffForHumans() }}</span>
                                            </div>
                                        @else
                                            <div class="inline-block p-2 rounded-xl bg-slate-100/80 border border-slate-200/60 text-xs">
                                                <strong class="text-slate-900 block font-bold text-[11px]">VALANTI · PAC-A</strong>
                                                <span class="text-[10.5px] text-slate-500 font-medium">
                                                    {{ ($app->fecha_hora_aceptacion ?? $app->created_at)->format('d/m/Y') }}
                                                    @if($app->edad) · {{ $app->edad }} años @endif
                                                </span>
                                            </div>
                                        @endif
                                    </td>

                                    <!-- 4. Estado con Píldora Definida -->
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-[10.5px] font-bold border shadow-2xs {{ $badge['clase'] }}">
                                            <i class="fa-solid {{ $badge['icono'] }} text-[9.5px]"></i>
                                            {{ $badge['texto'] }}
                                        </span>
                                    </td>

                                    <!-- 5. Acciones -->
                                    <td class="py-3.5 px-5 text-right whitespace-nowrap">
                                        <div class="inline-flex items-center justify-end gap-2">
                                            @if($enPapelera)
                                                <!-- Restablecer -->
                                                <form method="POST" action="{{ request()->routeIs('admin.*') ? route('admin.psicologia.pruebas.restablecer', $app->id) : route('psicologia.pruebas.restablecer', $app->id) }}">
                                                    @csrf
                                                    <button type="submit"
                                                            class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-50 text-emerald-800 hover:bg-emerald-100 border border-emerald-200 px-3 py-1.5 text-xs font-bold transition-all shadow-2xs hover:scale-[1.02] cursor-pointer"
                                                            title="Restablecer evaluación al historial activo">
                                                        <i class="fa-solid fa-rotate-left text-xs text-emerald-600"></i>
                                                        <span>Restablecer</span>
                                                    </button>
                                                </form>

                                                <!-- Destruir Definitivo -->
                                                <button type="button"
                                                        onclick="abrirModalDestruirDefinitivo('{{ request()->routeIs('admin.*') ? route('admin.psicologia.pruebas.destruir', $app->id) : route('psicologia.pruebas.destruir', $app->id) }}', '{{ addslashes($app->nombre_completo) }}', '{{ $app->identificacion }}')"
                                                        class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 hover:text-rose-800 border border-rose-200 transition-all shadow-2xs hover:scale-105 cursor-pointer ml-1"
                                                        title="Eliminar permanentemente del sistema">
                                                    <i class="fa-solid fa-trash-can text-xs"></i>
                                                </button>
                                            @else
                                                @if($app->valantiFinalizada())
                                                    <a href="{{ request()->routeIs('admin.*') ? route('admin.psicologia.valanti.resultado', $app) : route('psicologia.valanti.resultado', $app) }}"
                                                       class="inline-flex items-center gap-1.5 rounded-xl bg-sky-50 text-sky-800 hover:bg-sky-100 border border-sky-200/90 px-3 py-1.5 text-xs font-bold transition-all shadow-2xs hover:scale-[1.02]"
                                                       title="Entrar a los Resultados de VALANTI">
                                                        <i class="fa-solid fa-scale-balanced text-xs text-sky-600"></i>
                                                        <span>VALANTI</span>
                                                    </a>
                                                @endif

                                                @if($app->pacaFinalizada())
                                                    <a href="{{ request()->routeIs('admin.*') ? route('admin.psicologia.paca.resultado', $app) : route('psicologia.paca.resultado', $app) }}"
                                                       class="inline-flex items-center gap-1.5 rounded-xl bg-indigo-50 text-indigo-800 hover:bg-indigo-100 border border-indigo-200/90 px-3 py-1.5 text-xs font-bold transition-all shadow-2xs hover:scale-[1.02]"
                                                       title="Entrar a los Resultados de PAC-A">
                                                        <i class="fa-solid fa-chart-simple text-xs text-indigo-600"></i>
                                                        <span>PAC-A</span>
                                                    </a>
                                                @endif

                                                @if(!$app->valantiFinalizada())
                                                    @php
                                                        $pathCont = route('psicologia.evaluacion.paso2', ['codigo' => $app->codigo_sesion ?: 'PSI-PNY', 'evaluacion' => $app->id], false);
                                                    @endphp
                                                    <button type="button"
                                                            onclick="abrirModalQrContinuar('{{ $pathCont }}', '{{ addslashes($app->nombre_completo) }}', 'CC {{ $app->identificacion }}', 'Prueba 1: Cuestionario VALANTI')"
                                                            title="Mostrar Código QR para continuar Prueba 1"
                                                            class="inline-flex items-center gap-1.5 rounded-xl bg-amber-50 text-amber-800 border border-amber-200 hover:bg-amber-100 px-3 py-1.5 text-xs font-bold transition-colors cursor-pointer shadow-2xs">
                                                        <i class="fa-solid fa-qrcode text-xs text-amber-600"></i>
                                                        <span>QR Prueba 1</span>
                                                    </button>
                                                @elseif(!$app->pacaFinalizada())
                                                    @php
                                                        $pathCont = route('psicologia.evaluacion.paso3', ['codigo' => $app->codigo_sesion ?: 'PSI-PNY', 'evaluacion' => $app->id], false);
                                                    @endphp
                                                    <button type="button"
                                                            onclick="abrirModalQrContinuar('{{ $pathCont }}', '{{ addslashes($app->nombre_completo) }}', 'CC {{ $app->identificacion }}', 'Prueba 2: Cuestionario PAC-A')"
                                                            title="Mostrar Código QR para continuar Prueba 2"
                                                            class="inline-flex items-center gap-1.5 rounded-xl bg-amber-50 text-amber-800 border border-amber-200 hover:bg-amber-100 px-3 py-1.5 text-xs font-bold transition-colors cursor-pointer shadow-2xs">
                                                        <i class="fa-solid fa-qrcode text-xs text-amber-600"></i>
                                                        <span>QR Prueba 2</span>
                                                    </button>
                                                @endif

                                                <!-- Botón Eliminar Persona / Enviar a Papelera -->
                                                <button type="button"
                                                        onclick="abrirModalEliminar('{{ request()->routeIs('admin.*') ? route('admin.psicologia.pruebas.eliminar', $app) : route('psicologia.pruebas.eliminar', $app) }}', '{{ addslashes($app->nombre_completo) }}', '{{ $app->identificacion }}')"
                                                        class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 hover:text-rose-800 border border-rose-200 transition-all shadow-2xs hover:scale-105 cursor-pointer ml-1"
                                                        title="Enviar a papelera y liberar cédula">
                                                    <i class="fa-solid fa-trash-can text-xs"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Mensaje cuando el filtro rápido oculta todas las filas visibles -->
            <div id="mensajeSinResultadosVivo" class="hidden rounded-2xl border border-slate-200/80 bg-white p-10 text-center shadow-2xs">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-50 text-slate-400 mb-2">
                    <i class="fa-solid fa-magnifying-glass text-lg"></i>
                </div>
                <h3 class="text-sm font-bold text-slate-700">Sin coincidencias para tu búsqueda</h3>
                <p class="text-xs text-slate-400 mt-1">Intenta con otro nombre, número de cédula, cargo o estado.</p>
            </div>

            <!-- Paginación -->
            @if($aplicaciones->hasPages())
                <div id="paginacionHistorial" class="rounded-2xl border border-slate-200/80 bg-white px-5 py-4 shadow-2xs">
                    {{ $aplicaciones->links() }}
                </div>
            @endif

        @endif
    </div>

</div>

<!-- MODAL DE PREVISUALIZACIÓN DE PDF INTERACTIVO -->
<div id="modalVisorPdf" class="fixed inset-0 z-50 hidden bg-slate-950/80 backdrop-blur-xs flex items-center justify-center p-3 sm:p-6 transition-all duration-200" aria-modal="true" role="dialog">
    <div class="bg-white rounded-3xl max-w-4xl w-full h-[88vh] flex flex-col overflow-hidden shadow-2xl border border-slate-200 animate-in fade-in zoom-in-95 duration-200">
        
        <!-- Encabezado del Visor -->
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-200 bg-slate-50 shrink-0">
            <div class="min-w-0 pr-4">
                <span class="text-[10px] font-bold uppercase tracking-wider text-brand-blue block">Formato Oficial GH-FO-025</span>
                <h3 id="tituloModalPdf" class="text-sm font-bold text-slate-900 truncate">Consentimiento Informado</h3>
            </div>
            
            <div class="flex items-center gap-2 shrink-0">
                <!-- Botón Descargar PDF desde el Modal -->
                <a id="btnDescargarPdfModal"
                   href="#"
                   class="inline-flex items-center gap-1.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white px-3 py-1.5 text-xs font-bold transition-colors shadow-2xs">
                    <i class="fa-solid fa-download text-[11px]"></i>
                    <span class="hidden sm:inline">Descargar PDF</span>
                </a>

                <!-- Abrir en Pestaña Completa -->
                <a id="btnPestanaModal"
                   href="#"
                   target="_blank"
                   title="Abrir en pestaña completa"
                   class="inline-flex items-center justify-center h-8 w-8 rounded-xl bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 text-xs transition-colors">
                    <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                </a>

                <!-- Botón Cerrar (X) -->
                <button type="button"
                        onclick="cerrarModalPdf()"
                        class="inline-flex items-center justify-center h-8 w-8 rounded-xl bg-white hover:bg-rose-50 hover:text-rose-600 text-slate-500 border border-slate-200 text-xs transition-colors cursor-pointer">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>
        </div>

        <!-- Contenedor del PDF (Iframe con Spinner de Carga) -->
        <div class="relative flex-1 bg-slate-100 overflow-hidden flex flex-col">
            <!-- Spinner indicador de carga -->
            <div id="spinnerCargaPdf" class="absolute inset-0 flex flex-col items-center justify-center bg-slate-50 z-10 transition-opacity">
                <i class="fa-solid fa-circle-notch fa-spin text-3xl text-brand-blue mb-3"></i>
                <span class="text-xs font-semibold text-slate-600">Cargando documento PDF institucional...</span>
            </div>

            <!-- Iframe renderizador -->
            <iframe id="iframeVisorPdf"
                    src="about:blank"
                    class="w-full h-full border-0"
                    onload="document.getElementById('spinnerCargaPdf').classList.add('hidden')">
            </iframe>

            <!-- Mensaje de respaldo para dispositivos móviles -->
            <div class="p-2 text-center bg-slate-100 border-t border-slate-200 text-[11px] text-slate-500 sm:hidden">
                ¿Problemas visualizando en tu celular? <a id="btnFallbackMobile" href="#" target="_blank" class="text-brand-blue font-bold underline">Toca aquí para abrirlo</a>.
            </div>
        </div>

    </div>
</div>

<!-- MODAL DE CÓDIGO QR PARA CONTINUAR EVALUACIÓN EN EL CELULAR DEL CANDIDATO -->
<div id="modalQrContinuar" class="fixed inset-0 z-50 hidden bg-slate-950/80 backdrop-blur-xs flex items-center justify-center p-4 sm:p-6 transition-all duration-200" aria-modal="true" role="dialog">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl flex flex-col items-center text-center relative border border-slate-200">
        <!-- Botón Cerrar -->
        <button type="button"
                onclick="cerrarModalQrContinuar()"
                class="absolute top-4 right-4 h-8 w-8 rounded-xl bg-slate-50 hover:bg-rose-50 text-slate-400 hover:text-rose-600 border border-slate-200 flex items-center justify-center cursor-pointer transition-colors">
            <i class="fa-solid fa-xmark text-sm"></i>
        </button>

        <!-- Badge de Etapa -->
        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 text-amber-800 border border-amber-200 text-[11px] font-bold mb-2">
            <i class="fa-solid fa-mobile-screen-button text-amber-600"></i>
            <span id="qrContEtapa">Continuar Prueba en Celular</span>
        </div>

        <h3 id="qrContNombre" class="font-heading font-extrabold text-lg text-slate-900 leading-snug">
            Candidato
        </h3>
        <p id="qrContCedula" class="text-xs font-mono text-slate-500 mt-0.5"></p>
        <p class="text-xs text-slate-500 mt-2 max-w-xs">
            Pídele al candidato que escanee este código QR con la cámara de su celular para retomar su prueba exactamente donde quedó.
        </p>

        <!-- Marco del Código QR -->
        <div class="my-5 p-5 bg-white rounded-3xl border-2 border-slate-200 shadow-sm flex items-center justify-center">
            <div id="contenedorCanvasQrContinuar" class="flex items-center justify-center min-w-[230px] min-h-[230px]"></div>
        </div>

        <!-- Enlace directo copiable -->
        <div class="w-full mb-4">
            <div class="flex items-center gap-1.5 p-1.5 bg-slate-50 border border-slate-200 rounded-xl">
                <input type="text"
                       id="inputUrlQrContinuar"
                       readonly
                       class="flex-1 bg-transparent px-2 text-[11px] font-mono text-slate-600 truncate focus:outline-none select-all"
                       value="">
                <button type="button"
                        onclick="copiarEnlaceQrContinuar()"
                        class="px-2.5 py-1.5 rounded-lg bg-white hover:bg-brand-light text-slate-700 hover:text-brand-blue border border-slate-200 text-xs font-bold transition shrink-0 cursor-pointer flex items-center gap-1">
                    <i class="fa-regular fa-copy text-[11px]"></i>
                    <span id="txtBtnCopiarQrCont">Copiar</span>
                </button>
            </div>
        </div>

        <button type="button"
                onclick="cerrarModalQrContinuar()"
                class="w-full py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition cursor-pointer">
            Cerrar
        </button>
    </div>
</div>

<!-- MODAL 1: ENVIAR EVALUACIÓN A LA PAPELERA (SoftDelete) -->
<div id="modalEliminarEvaluacion" class="fixed inset-0 z-50 hidden bg-slate-950/80 backdrop-blur-xs flex items-center justify-center p-4 sm:p-6 transition-all duration-200" aria-modal="true" role="dialog">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl flex flex-col text-left relative border border-slate-200">
        <!-- Encabezado con Icono -->
        <div class="flex items-start gap-3.5 mb-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-trash-can-arrow-up"></i>
            </div>
            <div class="min-w-0 flex-1">
                <span class="text-[10px] font-bold uppercase tracking-wider text-amber-600 block">Enviar a la Papelera</span>
                <h3 class="text-base font-extrabold text-slate-900 font-heading leading-tight">¿Enviar evaluación a la papelera?</h3>
                <p class="text-xs text-slate-500 mt-0.5">La cédula quedará liberada para que la persona pueda presentar nuevamente la prueba desde cero.</p>
            </div>
        </div>

        <!-- Tarjeta Informativa del Candidato -->
        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-1.5 mb-3.5">
            <div class="flex items-center justify-between gap-2">
                <span class="text-slate-500 font-medium shrink-0">Persona evaluada:</span>
                <span id="txtModalEliminarNombre" class="font-bold text-slate-900 truncate text-right"></span>
            </div>
            <div class="flex items-center justify-between gap-2">
                <span class="text-slate-500 font-medium shrink-0">Documento:</span>
                <span id="txtModalEliminarCedula" class="font-mono font-bold text-slate-800 text-right"></span>
            </div>
        </div>

        <!-- Alerta Informativa -->
        <div class="text-[11px] text-blue-900 font-medium bg-blue-50 p-3 rounded-2xl border border-blue-200/80 flex items-start gap-2 mb-5 leading-relaxed">
            <i class="fa-solid fa-circle-info text-blue-600 shrink-0 mt-0.5"></i>
            <span>La evaluación se moverá a la papelera. Si lo requieres, podrás <strong>restablecerla</strong> en cualquier momento desde la pestaña de Papelera.</span>
        </div>

        <!-- Formulario de Envío a Papelera -->
        <form id="formEliminarEvaluacion" method="POST" action="">
            @csrf
            @method('DELETE')
            <div class="flex items-center justify-end gap-2.5">
                <button type="button"
                        onclick="cerrarModalEliminar()"
                        class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-100 text-slate-700 text-xs font-bold transition-colors cursor-pointer">
                    Cancelar
                </button>
                <button type="submit"
                        class="px-4 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 active:scale-95 text-white text-xs font-bold transition-all shadow-md cursor-pointer flex items-center gap-1.5">
                    <i class="fa-solid fa-trash-can text-xs"></i>
                    <span>Sí, enviar a papelera</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL 2: DESTRUIR DEFINITIVAMENTE (Hard Delete Permanente) -->
<div id="modalDestruirDefinitivo" class="fixed inset-0 z-50 hidden bg-slate-950/80 backdrop-blur-xs flex items-center justify-center p-4 sm:p-6 transition-all duration-200" aria-modal="true" role="dialog">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl flex flex-col text-left relative border border-slate-200">
        <!-- Encabezado con Icono -->
        <div class="flex items-start gap-3.5 mb-4">
            <div class="w-12 h-12 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div class="min-w-0 flex-1">
                <span class="text-[10px] font-bold uppercase tracking-wider text-rose-600 block">Acción Irreversible</span>
                <h3 class="text-base font-extrabold text-slate-900 font-heading leading-tight">¿Eliminar definitivamente?</h3>
                <p class="text-xs text-slate-500 mt-0.5">Esta acción eliminará de forma permanente e irrecuperable este registro y todas sus respuestas del servidor.</p>
            </div>
        </div>

        <!-- Tarjeta Informativa del Candidato -->
        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-1.5 mb-3.5">
            <div class="flex items-center justify-between gap-2">
                <span class="text-slate-500 font-medium shrink-0">Persona evaluada:</span>
                <span id="txtModalDestruirNombre" class="font-bold text-slate-900 truncate text-right"></span>
            </div>
            <div class="flex items-center justify-between gap-2">
                <span class="text-slate-500 font-medium shrink-0">Documento:</span>
                <span id="txtModalDestruirCedula" class="font-mono font-bold text-slate-800 text-right"></span>
            </div>
        </div>

        <div class="text-[11px] text-rose-900 font-medium bg-rose-50 p-3 rounded-2xl border border-rose-200/80 flex items-start gap-2 mb-5 leading-relaxed">
            <i class="fa-solid fa-triangle-exclamation text-rose-600 shrink-0 mt-0.5"></i>
            <span>Se borrarán permanentemente las respuestas de <strong>VALANTI</strong> y <strong>PAC-A</strong>, la firma digital y sus resultados. No se podrá recuperar.</span>
        </div>

        <!-- Formulario de Destrucción Definitiva -->
        <form id="formDestruirDefinitivo" method="POST" action="">
            @csrf
            @method('DELETE')
            <div class="flex items-center justify-end gap-2.5">
                <button type="button"
                        onclick="cerrarModalDestruirDefinitivo()"
                        class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-100 text-slate-700 text-xs font-bold transition-colors cursor-pointer">
                    Cancelar
                </button>
                <button type="submit"
                        class="px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 active:scale-95 text-white text-xs font-bold transition-all shadow-md cursor-pointer flex items-center gap-1.5">
                    <i class="fa-solid fa-trash-can text-xs"></i>
                    <span>Sí, eliminar definitivamente</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL 3: VACIAR PAPELERA COMPLETA -->
<div id="modalVaciarPapelera" class="fixed inset-0 z-50 hidden bg-slate-950/80 backdrop-blur-xs flex items-center justify-center p-4 sm:p-6 transition-all duration-200" aria-modal="true" role="dialog">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl flex flex-col text-left relative border border-slate-200">
        <!-- Encabezado con Icono -->
        <div class="flex items-start gap-3.5 mb-4">
            <div class="w-12 h-12 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-dumpster-fire"></i>
            </div>
            <div class="min-w-0 flex-1">
                <span class="text-[10px] font-bold uppercase tracking-wider text-rose-600 block">Purgar Papelera</span>
                <h3 class="text-base font-extrabold text-slate-900 font-heading leading-tight">¿Vaciar toda la papelera?</h3>
                <p class="text-xs text-slate-500 mt-0.5">Se eliminarán definitivamente los <strong>{{ $totalEnPapelera }}</strong> registros de la papelera con todos sus datos asociados.</p>
            </div>
        </div>

        <div class="text-[11px] text-rose-900 font-medium bg-rose-50 p-3 rounded-2xl border border-rose-200/80 flex items-start gap-2 mb-5 leading-relaxed">
            <i class="fa-solid fa-triangle-exclamation text-rose-600 shrink-0 mt-0.5"></i>
            <span>Esta acción no se puede deshacer. Se purgarán todas las respuestas, calificaciones y firmas archivadas en la papelera.</span>
        </div>

        <!-- Formulario Vaciar Papelera -->
        <form method="POST" action="{{ request()->routeIs('admin.*') ? route('admin.psicologia.pruebas.vaciar_papelera') : route('psicologia.pruebas.vaciar_papelera') }}">
            @csrf
            <div class="flex items-center justify-end gap-2.5">
                <button type="button"
                        onclick="cerrarModalVaciarPapelera()"
                        class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-100 text-slate-700 text-xs font-bold transition-colors cursor-pointer">
                    Cancelar
                </button>
                <button type="submit"
                        class="px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 active:scale-95 text-white text-xs font-bold transition-all shadow-md cursor-pointer flex items-center gap-1.5">
                    <i class="fa-solid fa-trash-can text-xs"></i>
                    <span>Sí, vaciar papelera ahora</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Librería QRCode.js para renderizar el QR de continuidad -->
<script src="{{ asset('qrcode.min.js') }}"></script>
<script>
    if (typeof QRCode === 'undefined') {
        document.write('<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"><\/script>');
    }
</script>

<script>
    /**
     * Configuración de URL base para generar el QR móvil de continuidad
     */
    const NGROK_BASE_DETECTADO = @json($urlNgrokBase ?? null);
    const LOCAL_BASE_DETECTADO = @json($urlLocalBase ?? url('/'));
    const ACTUAL_BASE_HOST = window.location.origin;

    function construirUrlMovilContinuidad(pathRelativo) {
        const path = pathRelativo.startsWith('/') ? pathRelativo : ('/' + pathRelativo);
        const ngrokGuardado = localStorage.getItem('pny_psicologia_ngrok_url');
        if (ngrokGuardado && ngrokGuardado.trim() !== '') {
            return ngrokGuardado.trim().replace(/\/+$/, '') + path;
        }
        if (NGROK_BASE_DETECTADO) {
            return NGROK_BASE_DETECTADO.replace(/\/+$/, '') + path;
        }
        if (ACTUAL_BASE_HOST.includes('localhost') || ACTUAL_BASE_HOST.includes('127.0.0.1')) {
            return LOCAL_BASE_DETECTADO.replace(/\/+$/, '') + path;
        }
        return ACTUAL_BASE_HOST.replace(/\/+$/, '') + path;
    }

    function abrirModalQrContinuar(pathRelativo, nombrePersona, cedulaTxt, etapaTxt) {
        const modal = document.getElementById('modalQrContinuar');
        const elNombre = document.getElementById('qrContNombre');
        const elCedula = document.getElementById('qrContCedula');
        const elEtapa = document.getElementById('qrContEtapa');
        const inputUrl = document.getElementById('inputUrlQrContinuar');
        const qrContainer = document.getElementById('contenedorCanvasQrContinuar');

        const urlCompleta = construirUrlMovilContinuidad(pathRelativo);

        if (elNombre) elNombre.textContent = nombrePersona;
        if (elCedula) elCedula.textContent = cedulaTxt;
        if (elEtapa) elEtapa.textContent = etapaTxt;
        if (inputUrl) inputUrl.value = urlCompleta;

        if (qrContainer) {
            qrContainer.innerHTML = '';
            new QRCode(qrContainer, {
                text: urlCompleta,
                width: 230,
                height: 230,
                colorDark: '#002d5e',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.M
            });
        }

        if (modal) {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }
    }

    function cerrarModalQrContinuar() {
        const modal = document.getElementById('modalQrContinuar');
        if (modal) {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    }

    function copiarEnlaceQrContinuar() {
        const input = document.getElementById('inputUrlQrContinuar');
        const txtBtn = document.getElementById('txtBtnCopiarQrCont');
        if (!input) return;

        navigator.clipboard.writeText(input.value).then(() => {
            if (txtBtn) txtBtn.textContent = '¡Copiado!';
            setTimeout(() => {
                if (txtBtn) txtBtn.textContent = 'Copiar';
            }, 2500);
        });
    }
    /**
     * Filtrado en vivo instantáneo + actualización AJAX sin recargar la página
     */
    const URL_BASE_HISTORIAL = @json(request()->routeIs('admin.*') ? route('admin.psicologia.historial') : route('psicologia.pruebas.historial'));
    let timerDebounceAjax = null;
    let controladorFetchActual = null;

    function aplicarFiltroInstantaneoDOM() {
        const inputBuscar = document.getElementById('inputBuscarVivo');
        const selectEstado = document.getElementById('selectEstadoVivo');
        const inputFecha = document.getElementById('inputFechaVivo');
        const btnLimpiar = document.getElementById('btnLimpiarFiltrosVivo');

        const termino = (inputBuscar?.value || '').trim().toLowerCase();
        const estado = selectEstado?.value || '';
        const fecha = inputFecha?.value || '';

        const hayFiltros = termino !== '' || estado !== '' || fecha !== '';
        if (btnLimpiar) {
            btnLimpiar.classList.toggle('hidden', !hayFiltros);
            btnLimpiar.classList.toggle('inline-flex', hayFiltros);
        }

        const items = document.querySelectorAll('.item-historial-vivo');
        if (!items.length) return;

        let coincidencias = 0;
        items.forEach((el) => {
            const dataSearch = (el.getAttribute('data-search') || '').toLowerCase();
            const dataEstado = el.getAttribute('data-estado') || '';
            const dataFecha = el.getAttribute('data-fecha') || '';

            const cumpleTexto = !termino || dataSearch.includes(termino);
            const cumpleEstado = !estado || dataEstado === estado;
            const cumpleFecha = !fecha || dataFecha === fecha;

            if (cumpleTexto && cumpleEstado && cumpleFecha) {
                el.classList.remove('hidden');
                coincidencias++;
            } else {
                el.classList.add('hidden');
            }
        });

        const msgVacio = document.getElementById('mensajeSinResultadosVivo');
        if (msgVacio) {
            msgVacio.classList.toggle('hidden', coincidencias > 0);
        }
    }

    function sincronizarFiltroAjax() {
        const inputBuscar = document.getElementById('inputBuscarVivo');
        const selectEstado = document.getElementById('selectEstadoVivo');
        const inputFecha = document.getElementById('inputFechaVivo');
        const spinner = document.getElementById('spinnerFiltroVivo');

        const params = new URLSearchParams();
        if (inputBuscar && inputBuscar.value.trim() !== '') {
            params.set('buscar', inputBuscar.value.trim());
        }
        if (selectEstado && selectEstado.value !== '') {
            params.set('estado', selectEstado.value);
        }
        if (inputFecha && inputFecha.value !== '') {
            params.set('fecha', inputFecha.value);
        }

        const queryString = params.toString();
        const nuevaUrl = queryString ? `${URL_BASE_HISTORIAL}?${queryString}` : URL_BASE_HISTORIAL;

        window.history.replaceState({}, '', nuevaUrl);

        if (controladorFetchActual) {
            controladorFetchActual.abort();
        }
        controladorFetchActual = new AbortController();

        if (spinner) spinner.classList.remove('hidden');

        fetch(nuevaUrl, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            signal: controladorFetchActual.signal
        })
        .then((res) => res.text())
        .then((html) => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const nuevoContenedor = doc.getElementById('contenedorResultadosHistorial');
            const contenedorActual = document.getElementById('contenedorResultadosHistorial');
            if (nuevoContenedor && contenedorActual) {
                contenedorActual.innerHTML = nuevoContenedor.innerHTML;
                aplicarFiltroInstantaneoDOM();
            }
        })
        .catch((err) => {
            if (err.name !== 'AbortError') {
                console.error(err);
            }
        })
        .finally(() => {
            if (spinner) spinner.classList.add('hidden');
        });
    }

    function dispararFiltroEnVivo() {
        aplicarFiltroInstantaneoDOM();
        clearTimeout(timerDebounceAjax);
        timerDebounceAjax = setTimeout(sincronizarFiltroAjax, 280);
    }

    function limpiarFiltrosEnVivo() {
        const inputBuscar = document.getElementById('inputBuscarVivo');
        const selectEstado = document.getElementById('selectEstadoVivo');
        const inputFecha = document.getElementById('inputFechaVivo');

        if (inputBuscar) inputBuscar.value = '';
        if (selectEstado) selectEstado.value = '';
        if (inputFecha) inputFecha.value = '';

        dispararFiltroEnVivo();
    }

    document.addEventListener('DOMContentLoaded', () => {
        const inputBuscar = document.getElementById('inputBuscarVivo');
        const selectEstado = document.getElementById('selectEstadoVivo');
        const inputFecha = document.getElementById('inputFechaVivo');

        if (inputBuscar) inputBuscar.addEventListener('input', dispararFiltroEnVivo);
        if (selectEstado) selectEstado.addEventListener('change', dispararFiltroEnVivo);
        if (inputFecha) inputFecha.addEventListener('change', dispararFiltroEnVivo);
    });

    function abrirModalPdf(urlInline, urlDescarga, nombrePersona) {
        const modal = document.getElementById('modalVisorPdf');
        const iframe = document.getElementById('iframeVisorPdf');
        const spinner = document.getElementById('spinnerCargaPdf');
        const titulo = document.getElementById('tituloModalPdf');
        const btnDescargar = document.getElementById('btnDescargarPdfModal');
        const btnPestana = document.getElementById('btnPestanaModal');
        const btnFallback = document.getElementById('btnFallbackMobile');

        titulo.textContent = 'Consentimiento Informado · ' + nombrePersona;
        btnDescargar.href = urlDescarga;
        btnPestana.href = urlInline;
        if (btnFallback) btnFallback.href = urlInline;

        spinner.classList.remove('hidden');
        iframe.src = urlInline;

        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function cerrarModalPdf() {
        const modal = document.getElementById('modalVisorPdf');
        const iframe = document.getElementById('iframeVisorPdf');

        modal.classList.add('hidden');
        iframe.src = 'about:blank';
        document.body.classList.remove('overflow-hidden');
    }

    function abrirModalEliminar(urlAction, nombre, cedula) {
        const modal = document.getElementById('modalEliminarEvaluacion');
        const form = document.getElementById('formEliminarEvaluacion');
        const txtNombre = document.getElementById('txtModalEliminarNombre');
        const txtCedula = document.getElementById('txtModalEliminarCedula');

        if (!modal || !form) return;

        form.action = urlAction;
        if (txtNombre) txtNombre.textContent = nombre;
        if (txtCedula) txtCedula.textContent = 'C.C. ' + cedula;

        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function cerrarModalEliminar() {
        const modal = document.getElementById('modalEliminarEvaluacion');
        if (modal) {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    }

    function abrirModalDestruirDefinitivo(urlAction, nombre, cedula) {
        const modal = document.getElementById('modalDestruirDefinitivo');
        const form = document.getElementById('formDestruirDefinitivo');
        const txtNombre = document.getElementById('txtModalDestruirNombre');
        const txtCedula = document.getElementById('txtModalDestruirCedula');

        if (!modal || !form) return;

        form.action = urlAction;
        if (txtNombre) txtNombre.textContent = nombre;
        if (txtCedula) txtCedula.textContent = 'C.C. ' + cedula;

        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function cerrarModalDestruirDefinitivo() {
        const modal = document.getElementById('modalDestruirDefinitivo');
        if (modal) {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    }

    function abrirModalVaciarPapelera() {
        const modal = document.getElementById('modalVaciarPapelera');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }
    }

    function cerrarModalVaciarPapelera() {
        const modal = document.getElementById('modalVaciarPapelera');
        if (modal) {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    }

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            cerrarModalPdf();
            cerrarModalQrContinuar();
            cerrarModalEliminar();
            cerrarModalDestruirDefinitivo();
            cerrarModalVaciarPapelera();
        }
    });

    document.getElementById('modalVisorPdf')?.addEventListener('click', function(event) {
        if (event.target === this) {
            cerrarModalPdf();
        }
    });

    document.getElementById('modalQrContinuar')?.addEventListener('click', function(event) {
        if (event.target === this) {
            cerrarModalQrContinuar();
        }
    });

    document.getElementById('modalEliminarEvaluacion')?.addEventListener('click', function(event) {
        if (event.target === this) {
            cerrarModalEliminar();
        }
    });

    document.getElementById('modalDestruirDefinitivo')?.addEventListener('click', function(event) {
        if (event.target === this) {
            cerrarModalDestruirDefinitivo();
        }
    });

    document.getElementById('modalVaciarPapelera')?.addEventListener('click', function(event) {
        if (event.target === this) {
            cerrarModalVaciarPapelera();
        }
    });
</script>
@endsection
