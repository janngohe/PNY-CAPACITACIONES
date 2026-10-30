@extends('layouts.psicologia')

@section('title', 'Historial de Pruebas')
@section('page_title', 'Pruebas Psicotécnicas')
@section('page_subtitle', 'Historial General y Custodia de Evaluaciones')

@section('content')
<div class="space-y-6">

    <!-- Encabezado Principal -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Historial de Aplicaciones y Consentimientos</h1>
            <p class="text-xs text-slate-500 mt-1">Registro de evaluaciones psicotécnicas, custodia de consentimientos GH-FO-025 y calificaciones</p>
        </div>
        <a href="{{ route('psicologia.qr.index') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 px-4 py-2.5 text-xs font-bold text-white shadow-2xs transition-all hover:scale-[1.02] shrink-0">
            <i class="fa-solid fa-qrcode text-xs"></i>
            Proyectar Código QR
        </a>
    </div>

    <!-- Barra de Filtros Resumida y 100% Responsive -->
    <div class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-2xs">
        <form method="GET" action="{{ route('psicologia.pruebas.historial') }}" class="flex flex-col md:flex-row md:items-center gap-3">
            
            <!-- Buscador por Texto -->
            <div class="relative flex-1">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text"
                       name="buscar"
                       value="{{ request('buscar') }}"
                       placeholder="Buscar por evaluado, cédula o cargo..."
                       class="w-full rounded-xl border border-slate-200 bg-slate-50/50 pl-9 pr-3 py-2 text-xs text-slate-800 placeholder-slate-400 focus:border-brand-blue focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-blue/20">
            </div>

            <div class="flex flex-wrap sm:flex-nowrap items-center gap-2">
                <!-- Selector de Estado -->
                <div class="w-full sm:w-44">
                    <select name="estado" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3 py-2 text-xs text-slate-800 focus:border-brand-blue focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-blue/20">
                        <option value="">-- Todos los estados --</option>
                        <option value="FINALIZADA" {{ request('estado') === 'FINALIZADA' ? 'selected' : '' }}>Finalizadas</option>
                        <option value="EN_CURSO" {{ request('estado') === 'EN_CURSO' ? 'selected' : '' }}>En Curso</option>
                        <option value="BORRADOR" {{ request('estado') === 'BORRADOR' ? 'selected' : '' }}>Borrador</option>
                    </select>
                </div>

                <!-- Selector de Fecha -->
                <div class="w-full sm:w-36">
                    <input type="date"
                           name="fecha"
                           value="{{ request('fecha') }}"
                           title="Filtrar por fecha de aplicación"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-2.5 py-2 text-xs text-slate-800 focus:border-brand-blue focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-blue/20">
                </div>

                <!-- Botones de Acción -->
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <button type="submit" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 px-4 py-2 text-xs font-bold text-white shadow-2xs transition-colors">
                        <i class="fa-solid fa-filter text-xs"></i>
                        <span>Filtrar</span>
                    </button>

                    @if($filtrosActivos)
                        <a href="{{ route('psicologia.pruebas.historial') }}" title="Limpiar filtros" class="inline-flex items-center justify-center gap-1 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 px-3 py-2 text-xs font-bold text-slate-600 transition-colors">
                            <i class="fa-solid fa-xmark text-xs"></i>
                            <span class="sm:hidden">Limpiar</span>
                        </a>
                    @endif
                </div>
            </div>

        </form>
    </div>

    <!-- Contenedor Principal de Registros (Responsive: Tabla en PC, Tarjetas en Móvil) -->
    @if($aplicaciones->isEmpty())
        <div class="rounded-2xl border border-slate-200/80 bg-white p-12 text-center shadow-2xs">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-slate-50 text-slate-400 mb-3">
                <i class="fa-solid fa-clipboard-question text-2xl"></i>
            </div>
            <h3 class="text-sm font-bold text-slate-700">No se encontraron evaluaciones</h3>
            <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">No hay registros que coincidan con los criterios de búsqueda.</p>
            @if($filtrosActivos)
                <div class="mt-4">
                    <a href="{{ route('psicologia.pruebas.historial') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 px-4 py-2 text-xs font-bold text-slate-700 transition-colors">
                        <i class="fa-solid fa-rotate-left text-xs"></i> Restablecer filtros
                    </a>
                </div>
            @endif
        </div>
    @else

        <!-- 1. VISTA MÓVIL: Tarjetas Adaptativas (< md) -->
        <div class="block md:hidden space-y-3">
            @foreach($aplicaciones as $app)
                @php
                    $badge = $app->badgeEstado();
                    $res = $app->resultadoPaca;
                    $consentimiento = $app->consentimiento ?? $app->candidato->ultimoConsentimiento();
                    $urlPdfInline = $consentimiento ? (route('psicologia.consentimientos.pdf', $consentimiento) . '?inline=1') : null;
                    $urlPdfDescarga = $consentimiento ? route('psicologia.consentimientos.pdf', $consentimiento) : null;
                @endphp
                <div class="rounded-2xl border border-slate-200/90 bg-white p-4 shadow-2xs space-y-3">
                    
                    <!-- Fila Superior: Nombre y Estado -->
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <span class="font-bold text-slate-900 text-sm block leading-snug">
                                {{ $app->candidato->nombre_completo }}
                            </span>
                            <div class="flex flex-wrap items-center gap-1.5 text-[11px] text-slate-500 mt-0.5">
                                <span class="font-mono">CC {{ $app->candidato->numero_identificacion }}</span>
                                @if($app->candidato->cargo_postulado)
                                    <span>•</span>
                                    <span class="text-slate-600 font-medium truncate max-w-[140px]">{{ $app->candidato->cargo_postulado }}</span>
                                @endif
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-bold border {{ $badge['clase'] }} shrink-0">
                            <i class="fa-solid {{ $badge['icono'] }} text-[9px]"></i>
                            {{ $badge['texto'] }}
                        </span>
                    </div>

                    <!-- Fila Central: Datos de Prueba y Puntajes -->
                    <div class="grid grid-cols-2 gap-2 p-2.5 rounded-xl bg-slate-50 text-[11px] border border-slate-100">
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-semibold">Prueba & Fecha</span>
                            <span class="font-bold text-slate-800">{{ $app->prueba_codigo }}</span>
                            <span class="text-slate-500 block text-[10px]">{{ $app->fecha_aplicacion->format('d/m/Y') }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-semibold">Tendencia Accid.</span>
                            @if($res)
                                @php
                                    $ptsAcc = $res->puntaje_tendencia_accidentogena;
                                    $clasePuntaje = $ptsAcc >= 11 ? 'text-rose-700 font-extrabold' : ($ptsAcc >= 9 ? 'text-amber-700 font-extrabold' : 'text-emerald-700 font-extrabold');
                                @endphp
                                <span class="{{ $clasePuntaje }} text-xs">{{ $ptsAcc }} pts</span>
                                <span class="text-slate-500 block text-[10px] truncate">Patrón: {{ $res->puntaje_patron_tipo_a }} pts</span>
                            @else
                                <span class="text-slate-400 italic">Pendiente</span>
                            @endif
                        </div>
                    </div>

                    <!-- Fila Inferior: Botones de Acción Táctiles (Consentimiento + Resultados) -->
                    <div class="flex items-center justify-between gap-2 pt-1 border-t border-slate-100 flex-wrap">
                        
                        <!-- Acciones Consentimiento -->
                        @if($consentimiento)
                            <div class="flex items-center gap-1.5">
                                <button type="button"
                                        onclick="abrirModalPdf('{{ $urlPdfInline }}', '{{ $urlPdfDescarga }}', '{{ addslashes($app->candidato->nombre_completo) }}')"
                                        class="inline-flex items-center gap-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 px-3 py-1.5 text-xs font-bold transition-colors">
                                    <i class="fa-solid fa-eye text-xs text-rose-600"></i>
                                    <span>Ver Consentimiento</span>
                                </button>
                                <a href="{{ $urlPdfDescarga }}"
                                   title="Descargar PDF"
                                   class="inline-flex items-center justify-center h-8 w-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs transition-colors">
                                    <i class="fa-solid fa-download"></i>
                                </a>
                            </div>
                        @else
                            <span class="text-[11px] text-slate-400 italic">Sin consentimiento</span>
                        @endif

                        <!-- Botón Resultados / Continuar -->
                        <div>
                            @if($app->estaFinalizada())
                                <a href="{{ route('psicologia.paca.resultado', $app) }}" class="inline-flex items-center gap-1.5 rounded-xl bg-indigo-50 text-indigo-700 hover:bg-indigo-100 px-3 py-1.5 text-xs font-bold transition-colors">
                                    <i class="fa-solid fa-chart-simple text-xs"></i>
                                    <span>Resultados</span>
                                </a>
                            @else
                                <a href="{{ route('psicologia.paca.aplicar', $app) }}" class="inline-flex items-center gap-1.5 rounded-xl bg-amber-50 text-amber-700 hover:bg-amber-100 px-3 py-1.5 text-xs font-bold transition-colors">
                                    <i class="fa-solid fa-play text-xs"></i>
                                    <span>Continuar</span>
                                </a>
                            @endif
                        </div>

                    </div>

                </div>
            @endforeach
        </div>

        <!-- 2. VISTA ESCRITORIO: Tabla Completa (>= md) -->
        <div class="hidden md:block rounded-2xl border border-slate-200/80 bg-white shadow-2xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/75 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                            <th class="py-3 px-4">ID</th>
                            <th class="py-3 px-4">Persona Evaluada</th>
                            <th class="py-3 px-4">Consentimiento GH-FO-025</th>
                            <th class="py-3 px-4">Prueba / Fecha</th>
                            <th class="py-3 px-4">Estado</th>
                            <th class="py-3 px-4">Tendencia Accid.</th>
                            <th class="py-3 px-4">Patrón Tipo A</th>
                            <th class="py-3 px-5 text-right">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @foreach($aplicaciones as $app)
                            @php
                                $badge = $app->badgeEstado();
                                $res = $app->resultadoPaca;
                                $consentimiento = $app->consentimiento ?? $app->candidato->ultimoConsentimiento();
                                $urlPdfInline = $consentimiento ? (route('psicologia.consentimientos.pdf', $consentimiento) . '?inline=1') : null;
                                $urlPdfDescarga = $consentimiento ? route('psicologia.consentimientos.pdf', $consentimiento) : null;
                            @endphp
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <!-- ID -->
                                <td class="py-3.5 px-4 font-mono text-slate-400 text-[11px]">#{{ $app->id }}</td>

                                <!-- Persona Evaluada -->
                                <td class="py-3.5 px-4">
                                    <span class="font-bold text-slate-900 block leading-tight">
                                        {{ $app->candidato->nombre_completo }}
                                    </span>
                                    <div class="flex items-center gap-2 mt-0.5 text-[11px] text-slate-400">
                                        <span>CC {{ $app->candidato->numero_identificacion }}</span>
                                        @if($app->candidato->cargo_postulado)
                                            <span>•</span>
                                            <span class="text-slate-500 font-medium truncate max-w-[150px]" title="{{ $app->candidato->cargo_postulado }}">
                                                {{ $app->candidato->cargo_postulado }}
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <!-- CONSENTIMIENTO: Previsualización + Descarga Directa -->
                                <td class="py-3.5 px-4">
                                    @if($consentimiento)
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <!-- Botón Previsualizar PDF en Modal -->
                                            <button type="button"
                                                    onclick="abrirModalPdf('{{ $urlPdfInline }}', '{{ $urlPdfDescarga }}', '{{ addslashes($app->candidato->nombre_completo) }}')"
                                                    title="Previsualizar consentimiento en pantalla"
                                                    class="inline-flex items-center gap-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200/90 px-2.5 py-1 text-[11px] font-bold transition-all shadow-2xs hover:scale-[1.02]">
                                                <i class="fa-solid fa-file-pdf text-rose-600"></i>
                                                <span>Ver PDF</span>
                                            </button>

                                            <!-- Botón Descargar PDF Directo -->
                                            <a href="{{ $urlPdfDescarga }}"
                                               title="Descargar archivo PDF oficial"
                                               class="inline-flex items-center justify-center h-6 w-6 rounded-lg bg-slate-50 hover:bg-slate-100 text-slate-600 border border-slate-200 text-[10px] transition-colors">
                                                <i class="fa-solid fa-download"></i>
                                            </a>

                                            <!-- Botón Descargar DOCX -->
                                            <a href="{{ route('psicologia.consentimientos.docx', $consentimiento) }}"
                                               title="Descargar Word DOCX editable"
                                               class="inline-flex items-center justify-center h-6 w-6 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200/80 text-[10px] font-bold transition-colors">
                                                <i class="fa-solid fa-file-word text-blue-600"></i>
                                            </a>
                                        </div>
                                        <div class="text-[10px] text-slate-400 mt-1 flex items-center gap-1">
                                            @if($consentimiento->estado === 'FIRMADO_DIGITAL' || $consentimiento->tipo_firma === 'DIGITAL')
                                                <span class="text-emerald-700 font-semibold flex items-center gap-1">
                                                    <i class="fa-solid fa-check text-[9px]"></i> Firmado digitalmente
                                                </span>
                                            @elseif($consentimiento->estado === 'FIRMADO_FISICO')
                                                <span class="text-blue-700 font-semibold flex items-center gap-1">
                                                    <i class="fa-solid fa-pen text-[9px]"></i> Firmado físico
                                                </span>
                                            @else
                                                <span class="text-amber-600 font-semibold">Pendiente firma</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-slate-400 italic text-[11px]">Sin consentimiento</span>
                                    @endif
                                </td>

                                <!-- Prueba / Fecha -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <strong class="text-slate-900 block font-bold">{{ $app->prueba_codigo }}</strong>
                                    <span class="text-[11px] text-slate-400">
                                        {{ $app->fecha_aplicacion->format('d/m/Y') }}
                                        @if($app->edad_evaluado)
                                            · {{ $app->edad_evaluado }} años
                                        @endif
                                    </span>
                                </td>

                                <!-- Estado -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-[10px] font-semibold border {{ $badge['clase'] }}">
                                        <i class="fa-solid {{ $badge['icono'] }} text-[9px]"></i>
                                        {{ $badge['texto'] }}
                                    </span>
                                </td>

                                <!-- Tendencia Accidentógena -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    @if($res)
                                        @php
                                            $ptsAcc = $res->puntaje_tendencia_accidentogena;
                                            $clasePuntaje = $ptsAcc >= 11 ? 'text-rose-600 bg-rose-50 border-rose-200' : ($ptsAcc >= 9 ? 'text-amber-700 bg-amber-50 border-amber-200' : 'text-emerald-700 bg-emerald-50 border-emerald-200');
                                        @endphp
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md font-extrabold text-[11px] border {{ $clasePuntaje }}">
                                            {{ $ptsAcc }} pts
                                        </span>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>

                                <!-- Patrón Tipo A -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    @if($res)
                                        @php
                                            $ptsTipoA = $res->puntaje_patron_tipo_a;
                                            $claseTipoA = $ptsTipoA >= 20 ? 'text-rose-600 bg-rose-50 border-rose-200' : ($ptsTipoA >= 18 ? 'text-amber-700 bg-amber-50 border-amber-200' : 'text-slate-700 bg-slate-50 border-slate-200');
                                        @endphp
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md font-extrabold text-[11px] border {{ $claseTipoA }}">
                                            {{ $ptsTipoA }} pts
                                        </span>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>

                                <!-- Acciones -->
                                <td class="py-3.5 px-5 text-right whitespace-nowrap">
                                    @if($app->estaFinalizada())
                                        <a href="{{ route('psicologia.paca.resultado', $app) }}" class="inline-flex items-center gap-1.5 rounded-xl bg-indigo-50 text-indigo-700 hover:bg-indigo-100 px-3 py-1.5 text-xs font-bold transition-colors">
                                            <i class="fa-solid fa-chart-simple text-[11px]"></i> Resultados
                                        </a>
                                    @else
                                        <a href="{{ route('psicologia.paca.aplicar', $app) }}" class="inline-flex items-center gap-1.5 rounded-xl bg-amber-50 text-amber-700 hover:bg-amber-100 px-3 py-1.5 text-xs font-bold transition-colors">
                                            <i class="fa-solid fa-play text-[11px]"></i> Continuar
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Paginación -->
        @if($aplicaciones->hasPages())
            <div class="rounded-2xl border border-slate-200/80 bg-white px-5 py-4 shadow-2xs">
                {{ $aplicaciones->links() }}
            </div>
        @endif

    @endif

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
                        class="inline-flex items-center justify-center h-8 w-8 rounded-xl bg-white hover:bg-rose-50 hover:text-rose-600 text-slate-500 border border-slate-200 text-xs transition-colors">
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

<script>
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

    // Cerrar al presionar tecla ESC
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            cerrarModalPdf();
        }
    });

    // Cerrar al hacer clic fuera del modal
    document.getElementById('modalVisorPdf')?.addEventListener('click', function(event) {
        if (event.target === this) {
            cerrarModalPdf();
        }
    });
</script>
@endsection
