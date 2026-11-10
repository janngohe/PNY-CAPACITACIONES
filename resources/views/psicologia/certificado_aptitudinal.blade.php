@extends(request()->routeIs('admin.*') ? 'layouts.admin' : 'layouts.psicologia')

@section('title', 'Certificado Aptitudinal · Psicología')
@section('page_title', 'Certificado Aptitudinal (GH-FO-028)')

@section('content')
<div class="w-full max-w-none space-y-6">

    <!-- CABECERA Y KPIS -->
    <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-2xs flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-700 text-[11px] font-bold uppercase tracking-wider mb-1.5 border border-emerald-200/60">
                <i class="fa-solid fa-file-circle-check"></i>
                <span>Formato Oficial GH-FO-028 · Versión 02</span>
            </div>
            <h2 class="text-xl font-heading font-extrabold text-slate-900">Certificado de Aptitud Psicológica</h2>
            <p class="text-xs text-slate-500 mt-0.5">
                Listado de evaluados que ya completaron las 2 pruebas psicotécnicas (<strong>VALANTI / PAC-A</strong>) habilitados para diligenciar y descargar su certificado.
            </p>
        </div>

        <!-- Resumen de Contadores -->
        <div class="grid grid-cols-3 gap-2.5 sm:gap-3 shrink-0">
            <div class="rounded-xl bg-slate-50 border border-slate-200/80 px-3.5 py-2.5 text-center">
                <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Pruebas 2/2</span>
                <span class="text-lg font-extrabold text-slate-900">{{ $totalCompletas }}</span>
            </div>
            <div class="rounded-xl bg-emerald-50/70 border border-emerald-200/80 px-3.5 py-2.5 text-center">
                <span class="block text-[10px] font-bold uppercase tracking-wider text-emerald-700">Emitidos</span>
                <span class="text-lg font-extrabold text-emerald-700">{{ $totalEmitidos }}</span>
            </div>
            <div class="rounded-xl bg-amber-50/70 border border-amber-200/80 px-3.5 py-2.5 text-center">
                <span class="block text-[10px] font-bold uppercase tracking-wider text-amber-700">Pendientes</span>
                <span class="text-lg font-extrabold text-amber-700">{{ $totalPendientes }}</span>
            </div>
        </div>
    </div>

    <!-- BARRA DE FILTRO EN VIVO -->
    <div class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-2xs">
        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
            <div class="relative flex-1">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text"
                       id="inputBuscarCertificado"
                       autocomplete="off"
                       placeholder="Filtrar en vivo por nombre del evaluado, número de cédula o cargo..."
                       class="w-full rounded-xl border border-slate-200 bg-slate-50/50 pl-9 pr-4 py-2 text-xs text-slate-800 placeholder-slate-400 focus:border-brand-blue focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-blue/20">
            </div>

            <div class="flex items-center gap-2">
                <select id="selectEstadoCertificado"
                        class="w-full sm:w-52 rounded-xl border border-slate-200 bg-slate-50/50 px-3 py-2 text-xs text-slate-800 focus:border-brand-blue focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-blue/20 cursor-pointer">
                    <option value="">-- Todos ({{ $totalCompletas }}) --</option>
                    <option value="PENDIENTE">Pendientes por diligenciar ({{ $totalPendientes }})</option>
                    <option value="EMITIDO">Certificados emitidos ({{ $totalEmitidos }})</option>
                </select>

                <button type="button"
                        id="btnLimpiarFiltroCert"
                        onclick="limpiarFiltroCertificados()"
                        class="hidden items-center justify-center gap-1.5 rounded-xl border border-slate-200 bg-white hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200 px-3 py-2 text-xs font-bold text-slate-600 transition-colors cursor-pointer shrink-0">
                    <i class="fa-solid fa-xmark text-xs"></i>
                    <span>Limpiar</span>
                </button>
            </div>
        </div>
    </div>

    @if($evaluacionesCompletas->isEmpty())
        <div class="bg-white rounded-2xl p-12 border border-slate-200/80 text-center shadow-2xs">
            <div class="w-14 h-14 mx-auto rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-2xl mb-3">
                <i class="fa-solid fa-file-circle-check"></i>
            </div>
            <h3 class="font-heading font-bold text-base text-slate-800">Aún no hay evaluados con las 2 pruebas finalizadas</h3>
            <p class="text-xs text-slate-500 max-w-md mx-auto mt-1">
                Cuando un candidato complete tanto la prueba <strong>PAC-A</strong> como <strong>VALANTI</strong>, aparecerá automáticamente en este listado para emitir su Certificado de Aptitud Psicológica.
            </p>
        </div>
    @else

        <!-- 1. VISTA MÓVIL (< md): TARJETAS ADAPTATIVAS -->
        <div class="block md:hidden space-y-3" id="listaMovilCertificados">
            @foreach($evaluacionesCompletas as $ev)
                @php
                    $badge = $ev->badgeCertificadoAptitudinal();
                    $emitido = $ev->certificadoEmitido();
                    $estadoFiltro = $emitido ? 'EMITIDO' : 'PENDIENTE';
                    $textoBusqueda = mb_strtolower($ev->nombre_completo . ' ' . $ev->identificacion . ' ' . ($ev->cargo_aspira ?? '') . ' ' . ($ev->sede?->nombre ?? ''), 'UTF-8');
                    $ciudadFechaDefault = $ev->certificado_ciudad_fecha ?: ('Rivera-Huila, ' . now()->format('d/m/Y'));
                    $urlPdfInline = (request()->routeIs('admin.*') ? route('admin.psicologia.certificados.pdf', $ev) : route('psicologia.certificado_aptitudinal.pdf', $ev)) . '?inline=1';
                    $urlPdfDescarga = request()->routeIs('admin.*') ? route('admin.psicologia.certificados.pdf', $ev) : route('psicologia.certificado_aptitudinal.pdf', $ev);

                    $conceptoDefinido = $ev->conceptoAptitudinalActual();
                    $observacionDefinida = $ev->certificado_observaciones ?: $ev->generarObservacionSugerida();

                    $payloadModal = [
                        'id' => $ev->id,
                        'actionUrl' => request()->routeIs('admin.*') ? route('admin.psicologia.certificados.guardar', $ev) : route('psicologia.certificado_aptitudinal.guardar', $ev),
                        'nombre' => $ev->nombre_completo,
                        'identificacion' => $ev->identificacion,
                        'fecha_nacimiento' => $ev->fecha_nacimiento ? $ev->fecha_nacimiento->format('d/m/Y') : '—',
                        'edad' => ($ev->edad ?? '—') . ' años',
                        'cargo' => $ev->cargo_aspira ?? '—',
                        'ciudad_fecha' => $ciudadFechaDefault,
                        'concepto' => $conceptoDefinido,
                        'observaciones' => $observacionDefinida,
                    ];
                @endphp
                <div class="item-certificado-vivo rounded-2xl border border-slate-300/80 bg-white p-4 shadow-sm space-y-3 transition-all ring-1 ring-slate-900/5"
                     data-search="{{ $textoBusqueda }}"
                     data-estado="{{ $estadoFiltro }}">

                    <!-- Fila Superior: Logo de Usuario + Nombre + Estado -->
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="h-10 w-10 rounded-2xl bg-indigo-50 border border-indigo-200/80 text-indigo-600 flex items-center justify-center shadow-2xs shrink-0 select-none">
                                <i class="fa-solid fa-circle-user text-2xl"></i>
                            </div>
                            <div class="min-w-0">
                                <span class="font-bold text-slate-900 text-sm block leading-snug truncate">
                                    {{ $ev->nombre_completo }}
                                </span>
                                <span class="font-mono bg-slate-100 text-slate-700 px-1.5 py-0.5 rounded font-bold text-[10px] inline-block mt-0.5 whitespace-nowrap">
                                    CC {{ $ev->identificacion }}
                                </span>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-[10px] font-bold border shadow-2xs {{ $badge['clase'] }} shrink-0 whitespace-nowrap">
                            <i class="fa-solid {{ $badge['icono'] }} text-[9px]"></i>
                            {{ $badge['texto'] }}
                        </span>
                    </div>

                    <!-- Fila de Sede a lo largo (Horizontal completa sin saltos de línea) -->
                    @if($ev->sede)
                        <div class="flex items-center gap-2 text-xs text-sky-800 bg-sky-50/90 border border-sky-200/90 px-3 py-1.5 rounded-xl shadow-2xs">
                            <i class="fa-solid fa-location-dot text-sky-600 text-xs shrink-0"></i>
                            <span class="text-[11px] font-semibold whitespace-nowrap truncate">Sede: <strong class="font-bold text-sky-950">{{ $ev->sede->nombre }}</strong></span>
                        </div>
                    @endif

                    <!-- Fila Central: Datos de Cargo y Pruebas -->
                    <div class="grid grid-cols-2 gap-2 p-3 rounded-xl bg-slate-50 text-[11px] border border-slate-200/70">
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-bold tracking-wider">Cargo al que aspira</span>
                            <span class="font-bold text-slate-800 truncate block">{{ $ev->cargo_aspira ?: '—' }}</span>
                            <span class="text-[10px] text-slate-400 block mt-0.5">
                                {{ ($ev->fecha_hora_aceptacion ?? $ev->created_at)->format('d/m/Y') }}
                                @if($ev->edad) · {{ $ev->edad }} años @endif
                            </span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-bold tracking-wider mb-1">Pruebas Psicológicas</span>
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <a href="{{ request()->routeIs('admin.*') ? route('admin.psicologia.valanti.resultado', $ev) : route('psicologia.valanti.resultado', $ev) }}"
                                   class="inline-flex items-center gap-1 text-[10px] font-bold text-sky-800 bg-sky-50 hover:bg-sky-100 px-2 py-0.5 rounded-lg border border-sky-200 transition-colors">
                                    <i class="fa-solid fa-scale-balanced text-[9px] text-sky-600"></i>
                                    VALANTI
                                </a>
                                <a href="{{ request()->routeIs('admin.*') ? route('admin.psicologia.paca.resultado', $ev) : route('psicologia.paca.resultado', $ev) }}"
                                   class="inline-flex items-center gap-1 text-[10px] font-bold text-indigo-800 bg-indigo-50 hover:bg-indigo-100 px-2 py-0.5 rounded-lg border border-indigo-200 transition-colors">
                                    <i class="fa-solid fa-chart-simple text-[9px] text-indigo-600"></i>
                                    PAC-A
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Fila Inferior: Botones de Acción -->
                    <div class="flex items-center justify-between gap-2 pt-2 border-t border-slate-100 flex-wrap">
                        <button type="button"
                                onclick='abrirModalCertificado(@json($payloadModal))'
                                class="inline-flex items-center gap-1.5 rounded-xl {{ $emitido ? 'bg-slate-900 hover:bg-slate-800 text-white' : 'bg-brand-blue hover:bg-brand-deep text-white' }} px-3.5 py-2 text-xs font-bold transition cursor-pointer shadow-2xs hover:scale-[1.02]">
                            <i class="fa-solid {{ $emitido ? 'fa-pen-to-square' : 'fa-check-double' }}"></i>
                            <span>{{ $emitido ? 'Editar Certificado' : 'Diligenciar Certificado' }}</span>
                        </button>

                        <div class="flex items-center gap-1.5">
                            <button type="button"
                                    onclick="abrirModalVistaPdfCertificado('{{ $urlPdfInline }}', '{{ $urlPdfDescarga }}', '{{ addslashes($ev->nombre_completo) }}')"
                                    class="inline-flex items-center gap-1 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 px-3 py-1.5 text-xs font-bold transition cursor-pointer shadow-2xs hover:scale-[1.02]">
                                <i class="fa-solid fa-eye text-emerald-600"></i>
                                <span>Ver PDF</span>
                            </button>
                            <a href="{{ $urlPdfDescarga }}"
                               class="inline-flex items-center gap-1 rounded-xl bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 px-3 py-1.5 text-xs font-bold transition shadow-2xs hover:scale-[1.02]">
                                <i class="fa-solid fa-download text-emerald-600"></i>
                                <span>PDF</span>
                            </a>
                        </div>
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
                                    <i class="fa-solid fa-briefcase text-sky-400"></i>
                                    <span>Cargo al que Aspira</span>
                                </div>
                            </th>
                            <th class="py-4 px-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-square-check text-emerald-400"></i>
                                    <span>Pruebas Psicotécnicas</span>
                                </div>
                            </th>
                            <th class="py-4 px-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-award text-amber-400"></i>
                                    <span>Concepto Aptitudinal (GH-FO-028)</span>
                                </div>
                            </th>
                            <th class="py-4 px-5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <i class="fa-solid fa-file-signature text-rose-400"></i>
                                    <span>Acciones / Certificado</span>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @foreach($evaluacionesCompletas as $ev)
                            @php
                                $badge = $ev->badgeCertificadoAptitudinal();
                                $emitido = $ev->certificadoEmitido();
                                $estadoFiltro = $emitido ? 'EMITIDO' : 'PENDIENTE';
                                $textoBusqueda = mb_strtolower($ev->nombre_completo . ' ' . $ev->identificacion . ' ' . ($ev->cargo_aspira ?? '') . ' ' . ($ev->sede?->nombre ?? ''), 'UTF-8');
                                $ciudadFechaDefault = $ev->certificado_ciudad_fecha ?: ('Rivera-Huila, ' . now()->format('d/m/Y'));
                                $urlPdfInline = (request()->routeIs('admin.*') ? route('admin.psicologia.certificados.pdf', $ev) : route('psicologia.certificado_aptitudinal.pdf', $ev)) . '?inline=1';
                                $urlPdfDescarga = request()->routeIs('admin.*') ? route('admin.psicologia.certificados.pdf', $ev) : route('psicologia.certificado_aptitudinal.pdf', $ev);

                                $conceptoDefinido = $ev->conceptoAptitudinalActual();
                                $observacionDefinida = $ev->certificado_observaciones ?: $ev->generarObservacionSugerida();

                                $payloadModal = [
                                    'id' => $ev->id,
                                    'actionUrl' => request()->routeIs('admin.*') ? route('admin.psicologia.certificados.guardar', $ev) : route('psicologia.certificado_aptitudinal.guardar', $ev),
                                    'nombre' => $ev->nombre_completo,
                                    'identificacion' => $ev->identificacion,
                                    'fecha_nacimiento' => $ev->fecha_nacimiento ? $ev->fecha_nacimiento->format('d/m/Y') : '—',
                                    'edad' => ($ev->edad ?? '—') . ' años',
                                    'cargo' => $ev->cargo_aspira ?? '—',
                                    'ciudad_fecha' => $ciudadFechaDefault,
                                    'concepto' => $conceptoDefinido,
                                    'observaciones' => $observacionDefinida,
                                ];
                            @endphp
                            <tr class="item-certificado-vivo odd:bg-white even:bg-slate-50/70 hover:bg-indigo-50/40 transition-colors duration-150"
                                data-search="{{ $textoBusqueda }}"
                                data-estado="{{ $estadoFiltro }}">

                                <!-- 1. Persona Evaluada (Con Logo de Usuario) -->
                                <td class="py-3.5 px-5">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 rounded-2xl bg-indigo-50 border border-indigo-200/80 text-indigo-600 flex items-center justify-center shadow-2xs shrink-0 select-none">
                                            <i class="fa-solid fa-circle-user text-2xl"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <span class="font-bold text-slate-900 block leading-tight text-sm truncate max-w-[240px]">
                                                {{ $ev->nombre_completo }}
                                            </span>
                                            <div class="flex items-center gap-1.5 mt-1 text-[11px] flex-wrap">
                                                <span class="font-mono bg-slate-100 text-slate-700 px-1.5 py-0.5 rounded font-bold text-[10px] whitespace-nowrap">CC {{ $ev->identificacion }}</span>
                                                @if($ev->sede)
                                                    <span class="inline-flex items-center gap-1 text-[10px] font-semibold bg-sky-50 text-sky-700 border border-sky-100 px-2 py-0.5 rounded-full whitespace-nowrap">
                                                        <i class="fa-solid fa-location-dot text-[9px] text-sky-600"></i>
                                                        <span class="whitespace-nowrap">{{ $ev->sede->nombre }}</span>
                                                    </span>
                                                @endif
                                                @if($ev->edad)
                                                    <span class="text-slate-400 text-[10px] whitespace-nowrap">· {{ $ev->edad }} años</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- 2. Cargo al que Aspira -->
                                <td class="py-3.5 px-4">
                                    <span class="font-bold text-slate-800 text-xs block truncate max-w-[190px]" title="{{ $ev->cargo_aspira }}">{{ $ev->cargo_aspira ?: '—' }}</span>
                                    <div class="text-[10px] text-slate-400 mt-0.5">
                                        Evaluado el {{ ($ev->fecha_hora_aceptacion ?? $ev->created_at)->format('d/m/Y') }}
                                    </div>
                                </td>

                                <!-- 3. Pruebas Psicotécnicas Completas -->
                                <td class="py-3.5 px-4 text-center">
                                    <div class="inline-flex items-center justify-center gap-1.5">
                                        <a href="{{ request()->routeIs('admin.*') ? route('admin.psicologia.valanti.resultado', $ev) : route('psicologia.valanti.resultado', $ev) }}"
                                           title="Entrar al resultado VALANTI"
                                           class="inline-flex items-center gap-1 rounded-xl bg-sky-50 hover:bg-sky-100 text-sky-800 border border-sky-200/90 px-2.5 py-1 text-[11px] font-bold transition-all shadow-2xs hover:scale-[1.02]">
                                            <i class="fa-solid fa-scale-balanced text-[10px] text-sky-600"></i>
                                            <span>VALANTI</span>
                                        </a>
                                        <a href="{{ request()->routeIs('admin.*') ? route('admin.psicologia.paca.resultado', $ev) : route('psicologia.paca.resultado', $ev) }}"
                                           title="Entrar al resultado PAC-A"
                                           class="inline-flex items-center gap-1 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-800 border border-indigo-200/90 px-2.5 py-1 text-[11px] font-bold transition-all shadow-2xs hover:scale-[1.02]">
                                            <i class="fa-solid fa-chart-simple text-[10px] text-indigo-600"></i>
                                            <span>PAC-A</span>
                                        </a>
                                    </div>
                                </td>

                                <!-- 4. Concepto Aptitudinal (GH-FO-028) -->
                                <td class="py-3.5 px-4 text-center">
                                    <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-[11px] font-bold border shadow-2xs {{ $badge['clase'] }} whitespace-nowrap">
                                        <i class="fa-solid {{ $badge['icono'] }} text-[10px]"></i>
                                        {{ $badge['texto'] }}
                                    </span>
                                    @if($ev->certificado_emitido_at)
                                        <div class="text-[10px] text-slate-400 mt-1 whitespace-nowrap">
                                            Emitido: {{ $ev->certificado_emitido_at->format('d/m/Y h:i A') }}
                                        </div>
                                    @endif
                                </td>

                                <!-- 5. Acciones / Certificado -->
                                <td class="py-3.5 px-5 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center justify-end gap-2">
                                        <button type="button"
                                                onclick='abrirModalCertificado(@json($payloadModal))'
                                                class="inline-flex items-center gap-1.5 rounded-xl {{ $emitido ? 'bg-slate-800 hover:bg-slate-700 text-white' : 'bg-brand-blue hover:bg-brand-deep text-white' }} px-3.5 py-1.5 text-xs font-bold transition-all cursor-pointer shadow-2xs hover:scale-[1.02]">
                                            <i class="fa-solid {{ $emitido ? 'fa-pen-to-square' : 'fa-check-to-slot' }} text-xs"></i>
                                            <span>{{ $emitido ? 'Editar Formato' : 'Llenar Certificado' }}</span>
                                        </button>

                                        <button type="button"
                                                onclick="abrirModalVistaPdfCertificado('{{ $urlPdfInline }}', '{{ $urlPdfDescarga }}', '{{ addslashes($ev->nombre_completo) }}')"
                                                title="Previsualizar Certificado GH-FO-028"
                                                class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200/90 px-3 py-1.5 text-xs font-bold transition-all shadow-2xs hover:scale-[1.02] cursor-pointer">
                                            <i class="fa-solid fa-eye text-emerald-600 text-xs"></i>
                                            <span>Ver PDF</span>
                                        </button>

                                        <a href="{{ $urlPdfDescarga }}"
                                           title="Descargar Certificado PDF Oficial GH-FO-028"
                                           class="inline-flex items-center gap-1.5 rounded-xl bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 px-3 py-1.5 text-xs font-bold transition-all shadow-2xs hover:scale-[1.02]">
                                            <i class="fa-solid fa-file-arrow-down text-emerald-600 text-xs"></i>
                                            <span>PDF</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Aviso sin coincidencias en filtro en vivo -->
        <div id="sinResultadosFiltroCert" class="hidden rounded-2xl border border-slate-200/80 bg-white p-10 text-center shadow-2xs">
            <i class="fa-solid fa-magnifying-glass text-2xl text-slate-300 mb-2"></i>
            <p class="text-sm font-bold text-slate-700">No hay evaluados que coincidan con el filtro</p>
            <p class="text-xs text-slate-400 mt-0.5">Prueba con otro nombre, cédula o cambia el estado seleccionado.</p>
        </div>
    @endif

</div>

<!-- =========================================================================
     MODAL INTERACTIVO DE DILIGENCIAMIENTO DEL FORMATO OFICIAL GH-FO-028
     ========================================================================= -->
<div id="modalDiligenciarCertificado"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/70 p-2 sm:p-4 backdrop-blur-xs overflow-y-auto"
     role="dialog"
     aria-modal="true">
    <div class="relative w-full max-w-4xl rounded-2xl bg-white shadow-2xl border border-slate-200 overflow-hidden my-4 flex flex-col max-h-[94vh]">

        <!-- Barra Superior del Modal -->
        <div class="flex items-center justify-between border-b border-slate-200 px-4 sm:px-6 py-3.5 bg-slate-900 text-white shrink-0">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-500/20 text-emerald-400 border border-emerald-400/30 shrink-0">
                    <i class="fa-solid fa-file-circle-check text-sm"></i>
                </div>
                <div class="truncate">
                    <h3 class="text-sm font-bold text-white truncate">Diligenciar Certificado de Aptitud Psicológica (GH-FO-028)</h3>
                    <p class="text-[11px] text-slate-300 truncate">Haz clic sobre el cuadrito correspondiente para marcarlo con la flechita (✓) y agrega las observaciones</p>
                </div>
            </div>
            <button type="button"
                    onclick="cerrarModalCertificado()"
                    class="rounded-xl bg-white/10 hover:bg-white/20 p-2 text-white transition-colors cursor-pointer shrink-0"
                    title="Cerrar">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <!-- Cuerpo del Documento Oficial Interactivo -->
        <form id="formCertificadoAptitudinal" method="POST" action="" class="flex flex-col flex-1 min-h-0">
            @csrf
            <input type="hidden" name="certificado_concepto" id="inputConceptoSeleccionado" value="">
            <input type="hidden" name="descargar_pdf" id="inputDescargarPdfDirecto" value="0">

            <div class="p-4 sm:p-7 overflow-y-auto space-y-5 bg-slate-100/70 flex-1">

                <!-- HOJA ESTILO FORMATO WORD GH-FO-028 -->
                <div class="bg-white border border-slate-300 shadow-sm rounded-xl p-4 sm:p-8 max-w-3xl mx-auto text-slate-900 space-y-5">

                    <!-- 1. ENCABEZADO INSTITUCIONAL (Idéntico al Word GH-FO-028) -->
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse border border-black text-center text-xs">
                            <tr>
                                <td rowspan="2" class="border border-black p-2 w-[18%] align-middle">
                                    <img src="{{ asset('images/Logo.png') }}" alt="Logo PNY" class="max-h-12 mx-auto object-contain">
                                </td>
                                <td class="border border-black py-1.5 px-2 font-bold text-xs sm:text-sm tracking-wide w-[54%]">
                                    GESTION HUMANA
                                </td>
                                <td class="border border-black py-1 px-2 font-bold text-[10px] leading-tight w-[14%]">
                                    <div>CODIGO:</div>
                                    <div>GH- FO-028</div>
                                </td>
                                <td class="border border-black py-1 px-2 font-bold text-[10px] leading-tight w-[14%]">
                                    <div>VERSION:</div>
                                    <div>02</div>
                                </td>
                            </tr>
                            <tr>
                                <td class="border border-black py-1.5 px-2 font-bold text-xs sm:text-sm tracking-wide">
                                    CERTIFICADO DE APTITUD PSICOLÓGICA
                                </td>
                                <td class="border border-black py-1 px-2 font-bold text-[10px] leading-tight">
                                    <div>FECHA:</div>
                                    <div>27/07/2023</div>
                                </td>
                                <td class="border border-black py-1 px-2 font-bold text-[10px] leading-tight">
                                    <div>PAGINA</div>
                                    <div>1 de 1</div>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!-- 2. TABLA DE DATOS DEL EVALUADO -->
                    <table class="w-full border-collapse border border-black text-xs sm:text-[13px]">
                        <tr>
                            <td colspan="2" class="border border-black px-3 py-1.5">
                                <div class="flex flex-col sm:flex-row sm:items-center gap-1.5">
                                    <label for="inputCiudadFechaModal" class="font-bold shrink-0">Ciudad/Municipio/ Fecha:</label>
                                    <input type="text"
                                           name="certificado_ciudad_fecha"
                                           id="inputCiudadFechaModal"
                                           placeholder="Ej: Rivera-Huila, {{ now()->format('d/m/Y') }}"
                                           class="w-full rounded border border-slate-300 bg-slate-50/70 px-2.5 py-1 text-xs font-medium text-slate-900 focus:border-brand-blue focus:bg-white focus:outline-none">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="border border-black px-3 py-1.5">
                                <span class="font-bold">Nombre del Evaluado:</span>
                                <span id="modalNombreEvaluado" class="ml-1.5 font-medium"></span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="border border-black px-3 py-1.5">
                                <span class="font-bold">Número de cedula:</span>
                                <span id="modalCedulaEvaluado" class="ml-1.5 font-medium"></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="border border-black px-3 py-1.5 w-1/2">
                                <span class="font-bold">Fecha de Nacimiento:</span>
                                <span id="modalFechaNacEvaluado" class="ml-1.5 font-medium"></span>
                            </td>
                            <td class="border border-black px-3 py-1.5 w-1/2">
                                <span class="font-bold">Edad:</span>
                                <span id="modalEdadEvaluado" class="ml-1.5 font-medium"></span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="border border-black px-3 py-1.5">
                                <span class="font-bold">Cargo al que aspira:</span>
                                <span id="modalCargoEvaluado" class="ml-1.5 font-medium"></span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="border border-black px-3 py-1.5">
                                <span class="font-bold">Pruebas Psicológicas:</span>
                                <span class="ml-8 sm:ml-14 font-extrabold tracking-wide">VALANTI / PAC-A</span>
                            </td>
                        </tr>
                    </table>

                    <!-- 3. TEXTO DE CONSTANCIA -->
                    <p class="text-xs sm:text-[13px] font-bold text-justify leading-relaxed">
                        Hacemos Constar que evaluamos a la persona en mención de acuerdo con los criterios dados por la empresa C.I. Piscícola New York S.A. en relación con: Batería de Pruebas.
                    </p>

                    <!-- Aviso Informativo: Concepto premarcado automáticamente según pruebas -->
                    <div class="rounded-xl bg-blue-50 border border-blue-200 p-3 flex items-start gap-3 shadow-2xs">
                        <div class="w-8 h-8 rounded-lg bg-brand-blue text-white flex items-center justify-center shrink-0 text-xs mt-0.5">
                            <i class="fa-solid fa-wand-magic-sparkles"></i>
                        </div>
                        <div class="text-xs text-slate-700">
                            <div class="flex items-center gap-2">
                                <span class="font-extrabold text-brand-blue">CONCEPTO PREMARCADO SEGÚN RESULTADOS</span>
                                <span class="text-[10px] font-bold uppercase tracking-wider bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full border border-blue-300">PAC-A &amp; VALANTI</span>
                            </div>
                            <p class="mt-0.5 leading-relaxed text-slate-600">
                                La casilla correspondiente viene predeterminada con la flechita (<span class="font-black text-emerald-700">✓</span>) según las pruebas psicotécnicas. Puedes confirmarla o hacer clic sobre otra opción si se requiere, y registrar las <strong>Observaciones</strong> oficiales.
                            </p>
                        </div>
                    </div>

                    <!-- 4. LOS 3 CUADRITOS INTERACTIVOS CON FLECHITA (✓) -->
                    <div class="space-y-3 pl-0 sm:pl-10">

                        <!-- Opción 1: Cumple sin índice de accidentalidad -->
                        <div onclick="seleccionarCuadritoConcepto('CUMPLE_SIN_INDICE')"
                             id="filaConcepto_CUMPLE_SIN_INDICE"
                             class="group flex items-center gap-3.5 p-3 rounded-xl border border-transparent hover:border-brand-blue/30 hover:bg-sky-50/40 transition cursor-pointer select-none">
                            <button type="button"
                                    aria-label="Marcar opción 1"
                                    class="cuadrito-btn w-10 h-8 shrink-0 border-2 border-[#2f5597] bg-white rounded-xs flex items-center justify-center text-xl font-extrabold text-slate-900 shadow-2xs group-hover:scale-105 transition-transform cursor-pointer">
                                <span id="check_CUMPLE_SIN_INDICE" class="hidden text-emerald-700 font-black leading-none">✓</span>
                            </button>
                            <p class="text-[11px] sm:text-xs font-bold text-justify leading-snug text-slate-900">
                                CUMPLE CON EL PERFIL LABORAL ESPERADO POR LA C.I. PISCÍCOLA NEW YORK S.A. (EN TÉRMINOS DE: RASGOS DE PERSONALIDAD, TENDENCIA DE VALORES Y SIN INDICE DE ACCIDENTALIDAD.
                            </p>
                        </div>

                        <!-- Opción 2: Cumple con recomendaciones a nivel accidentógeno -->
                        <div onclick="seleccionarCuadritoConcepto('CUMPLE_CON_RECOMENDACIONES')"
                             id="filaConcepto_CUMPLE_CON_RECOMENDACIONES"
                             class="group flex items-center gap-3.5 p-3 rounded-xl border border-transparent hover:border-brand-blue/30 hover:bg-sky-50/40 transition cursor-pointer select-none">
                            <button type="button"
                                    aria-label="Marcar opción 2"
                                    class="cuadrito-btn w-10 h-8 shrink-0 border-2 border-[#2f5597] bg-white rounded-xs flex items-center justify-center text-xl font-extrabold text-slate-900 shadow-2xs group-hover:scale-105 transition-transform cursor-pointer">
                                <span id="check_CUMPLE_CON_RECOMENDACIONES" class="hidden text-emerald-700 font-black leading-none">✓</span>
                            </button>
                            <p class="text-[11px] sm:text-xs font-bold text-justify leading-snug text-slate-900">
                                CUMPLE CON EL PERFIL LABORAL ESPERADO POR LA C.I. PISCÍCOLA NEW YORK S.A. (EN TÉRMINOS DE: RASGOS DE PERSONALIDAD, TENDENCIA DE VALORES Y SE SUGIEREN RECOMENDACIONES A NIVEL ACCIDENTÓGENO.
                            </p>
                        </div>

                        <!-- Opción 3: No cumple -->
                        <div onclick="seleccionarCuadritoConcepto('NO_CUMPLE')"
                             id="filaConcepto_NO_CUMPLE"
                             class="group flex items-center gap-3.5 p-3 rounded-xl border border-transparent hover:border-brand-blue/30 hover:bg-sky-50/40 transition cursor-pointer select-none">
                            <button type="button"
                                    aria-label="Marcar opción 3"
                                    class="cuadrito-btn w-10 h-8 shrink-0 border-2 border-[#2f5597] bg-white rounded-xs flex items-center justify-center text-xl font-extrabold text-slate-900 shadow-2xs group-hover:scale-105 transition-transform cursor-pointer">
                                <span id="check_NO_CUMPLE" class="hidden text-emerald-700 font-black leading-none">✓</span>
                            </button>
                            <p class="text-[11px] sm:text-xs font-bold text-justify leading-snug text-slate-900">
                                NO CUMPLE CON EL PERFIL LABORAL ESPERADO POR LA C.I. PISCICOLA NEW YORK S.A.
                            </p>
                        </div>

                    </div>

                    <!-- 5. CAMPO DE OBSERVACIONES (ÚNICO CAMPO EDITABLE) -->
                    <div class="pt-2">
                        <div class="flex items-center justify-between gap-2 mb-1.5">
                            <label for="textareaObservacionesModal" class="block text-xs font-extrabold uppercase tracking-wide text-slate-900 flex items-center gap-1.5">
                                <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-brand-blue text-white text-[10px]">
                                    <i class="fa-solid fa-pencil"></i>
                                </span>
                                <span>OBSERVACIONES:</span>
                            </label>
                            <span class="text-[10px] font-bold text-brand-blue bg-blue-50 px-2 py-0.5 rounded-full border border-blue-200">
                                Único campo editable por psicología
                            </span>
                        </div>
                        <textarea name="certificado_observaciones"
                                  id="textareaObservacionesModal"
                                  rows="4"
                                  oninput="this.value = this.value.toUpperCase()"
                                  style="text-transform: uppercase;"
                                  placeholder="ESCRIBE AQUÍ LAS OBSERVACIONES PSICOLÓGICAS O RECOMENDACIONES (TODO EN MAYÚSCULAS)..."
                                  class="w-full uppercase rounded-xl border-2 border-brand-blue/40 bg-white p-3 text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:border-brand-blue focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-blue/20 leading-relaxed font-sans shadow-2xs"></textarea>
                        <p class="text-[11px] text-slate-500 mt-1 flex items-center gap-1">
                            <i class="fa-solid fa-circle-info text-brand-blue text-xs"></i>
                            <span>El texto se guarda automáticamente en mayúsculas y se reflejará en el certificado oficial y reporte anual.</span>
                        </p>
                    </div>

                    <!-- 6. FIRMA PSICÓLOGA (Vista previa fiel al PDF) -->
                    <div class="pt-6 pb-2 text-center">
                        @if($usuario && $usuario->tieneFirmaRegistrada())
                            <div class="h-12 mb-1 flex items-center justify-center">
                                <img src="{{ $usuario->firmaUrl() }}" alt="Firma Oficial Psicóloga" class="max-h-12 max-w-[200px] object-contain">
                            </div>
                            <div class="w-56 border-t border-black mx-auto mb-1"></div>
                            <span class="text-xs font-extrabold tracking-wide text-slate-900 block">PSICOLOGA</span>
                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200 mt-1">
                                <i class="fa-solid fa-circle-check"></i>
                                Firma automática vinculada
                            </span>
                        @else
                            <div class="h-6"></div>
                            <div class="w-56 border-t border-black mx-auto mb-1"></div>
                            <span class="text-xs font-extrabold tracking-wide text-slate-900 block">PSICOLOGA</span>
                            <button type="button"
                                    onclick="abrirModalFirmaPsicologa()"
                                    class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-800 bg-amber-50 hover:bg-amber-100 px-2.5 py-1 rounded-full border border-amber-300 mt-1 cursor-pointer transition">
                                <i class="fa-solid fa-pen-nib text-amber-600"></i>
                                <span>No has registrado tu firma · Clic aquí para registrar</span>
                            </button>
                        @endif
                    </div>

                </div>
            </div>

            <!-- Pie del Modal: Botones de Guardado y Descarga -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 border-t border-slate-200 bg-white px-5 py-3.5 shrink-0">
                <button type="button"
                        onclick="cerrarModalCertificado()"
                        class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 px-4 py-2 text-xs font-bold text-slate-600 transition cursor-pointer">
                    Cancelar
                </button>

                <div class="flex flex-wrap items-center justify-end gap-2">
                    <button type="button"
                            onclick="enviarFormularioCertificado(false)"
                            class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-brand-blue hover:bg-brand-deep px-4 py-2 text-xs font-bold text-white shadow-2xs transition cursor-pointer">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Guardar Certificado</span>
                    </button>

                    <button type="button"
                            onclick="enviarFormularioCertificado(true)"
                            class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 px-4 py-2 text-xs font-bold text-white shadow-2xs transition cursor-pointer">
                        <i class="fa-solid fa-file-pdf"></i>
                        <span>Guardar y Descargar PDF</span>
                    </button>
                </div>
            </div>
        </form>

    </div>
</div>

<!-- =========================================================================
     MODAL VISOR DE PDF (GH-FO-028)
     ========================================================================= -->
<div id="modalVistaPreviaPdfCert"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/70 p-2 sm:p-4 backdrop-blur-xs"
     role="dialog"
     aria-modal="true">
    <div class="relative flex h-[92vh] w-full max-w-5xl flex-col rounded-2xl bg-white shadow-2xl overflow-hidden border border-slate-200">
        <div class="flex items-center justify-between border-b border-slate-200 px-4 sm:px-5 py-3 bg-slate-900 text-white">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-500/20 text-emerald-400 border border-emerald-400/30 shrink-0">
                    <i class="fa-solid fa-file-pdf text-sm"></i>
                </div>
                <div class="truncate">
                    <h3 id="tituloModalPdfCert" class="text-sm font-bold text-white truncate">Certificado de Aptitud Psicológica GH-FO-028</h3>
                    <p class="text-[11px] text-slate-300 truncate">Formato Oficial C.I. Piscícola New York S.A.</p>
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a id="btnDescargarModalPdfCert"
                   href="#"
                   class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 px-3 py-1.5 text-xs font-bold text-white shadow-2xs transition-colors">
                    <i class="fa-solid fa-download text-xs"></i>
                    <span class="hidden sm:inline">Descargar PDF</span>
                </a>
                <button type="button"
                        onclick="cerrarModalVistaPdfCertificado()"
                        class="rounded-xl bg-white/10 hover:bg-white/20 p-2 text-white transition-colors cursor-pointer"
                        title="Cerrar visor">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>
        </div>
        <div class="flex-1 bg-slate-100 relative">
            <iframe id="iframeModalPdfCert" src="about:blank" class="h-full w-full border-0" title="Visor PDF GH-FO-028"></iframe>
        </div>
    </div>
</div>

<script>
    const CONCEPTOS_VALIDOS = ['CUMPLE_SIN_INDICE', 'CUMPLE_CON_RECOMENDACIONES', 'NO_CUMPLE'];

    function abrirModalCertificado(datos) {
        const modal = document.getElementById('modalDiligenciarCertificado');
        const form = document.getElementById('formCertificadoAptitudinal');

        form.action = datos.actionUrl;
        document.getElementById('inputDescargarPdfDirecto').value = '0';
        document.getElementById('inputCiudadFechaModal').value = datos.ciudad_fecha || '';
        document.getElementById('modalNombreEvaluado').textContent = datos.nombre || '';
        document.getElementById('modalCedulaEvaluado').textContent = datos.identificacion || '';
        document.getElementById('modalFechaNacEvaluado').textContent = datos.fecha_nacimiento || '';
        document.getElementById('modalEdadEvaluado').textContent = datos.edad || '';
        document.getElementById('modalCargoEvaluado').textContent = datos.cargo || '';
        
        const textareaObs = document.getElementById('textareaObservacionesModal');
        textareaObs.value = (datos.observaciones || '').toUpperCase();

        // Marcar automáticamente el concepto con la flechita según resultados obtenidos en las pruebas
        mostrarCuadritoConceptoMarcado(datos.concepto || 'CUMPLE_CON_RECOMENDACIONES');

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.classList.add('overflow-hidden');

        // Enfocar el campo de observaciones (único editable por la psicóloga)
        setTimeout(function () {
            textareaObs.focus();
        }, 150);
    }

    function cerrarModalCertificado() {
        const modal = document.getElementById('modalDiligenciarCertificado');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
    }

    function mostrarCuadritoConceptoMarcado(codigo) {
        const input = document.getElementById('inputConceptoSeleccionado');
        input.value = codigo;

        CONCEPTOS_VALIDOS.forEach(function (item) {
            const check = document.getElementById('check_' + item);
            const fila = document.getElementById('filaConcepto_' + item);
            if (!check || !fila) return;

            if (item === codigo) {
                check.classList.remove('hidden');
                fila.classList.add('bg-emerald-50/90', 'border-2', 'border-emerald-400', 'ring-2', 'ring-emerald-500/20');
                fila.classList.remove('border-transparent', 'opacity-50');
            } else {
                check.classList.add('hidden');
                fila.classList.remove('bg-emerald-50/90', 'border-2', 'border-emerald-400', 'ring-2', 'ring-emerald-500/20');
                fila.classList.add('border-transparent', 'opacity-50');
            }
        });
    }

    function seleccionarCuadritoConcepto(codigo) {
        mostrarCuadritoConceptoMarcado(codigo);
    }

    function enviarFormularioCertificado(descargarPdf) {
        document.getElementById('inputDescargarPdfDirecto').value = descargarPdf ? '1' : '0';
        document.getElementById('formCertificadoAptitudinal').submit();

        if (descargarPdf) {
            setTimeout(function () {
                window.location.reload();
            }, 1400);
        }
    }

    function abrirModalVistaPdfCertificado(urlInline, urlDescarga, nombreEvaluado) {
        const modal = document.getElementById('modalVistaPreviaPdfCert');
        document.getElementById('iframeModalPdfCert').src = urlInline;
        document.getElementById('btnDescargarModalPdfCert').href = urlDescarga;
        document.getElementById('tituloModalPdfCert').textContent = 'Certificado GH-FO-028 · ' + nombreEvaluado;

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    }

    function cerrarModalVistaPdfCertificado() {
        const modal = document.getElementById('modalVistaPreviaPdfCert');
        document.getElementById('iframeModalPdfCert').src = 'about:blank';
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
    }

    // Filtrado en vivo instantáneo
    function aplicarFiltroCertificados() {
        const inputBuscar = document.getElementById('inputBuscarCertificado');
        const selectEstado = document.getElementById('selectEstadoCertificado');
        const btnLimpiar = document.getElementById('btnLimpiarFiltroCert');

        if (!inputBuscar || !selectEstado) return;

        const texto = inputBuscar.value.trim().toLowerCase();
        const estado = selectEstado.value;

        if (btnLimpiar) {
            if (texto !== '' || estado !== '') {
                btnLimpiar.classList.remove('hidden');
                btnLimpiar.classList.add('inline-flex');
            } else {
                btnLimpiar.classList.add('hidden');
                btnLimpiar.classList.remove('inline-flex');
            }
        }

        const items = document.querySelectorAll('.item-certificado-vivo');
        let visibles = 0;

        items.forEach(function (el) {
            const matchTexto = !texto || (el.dataset.search || '').includes(texto);
            const matchEstado = !estado || (el.dataset.estado || '') === estado;

            if (matchTexto && matchEstado) {
                el.classList.remove('hidden');
                visibles++;
            } else {
                el.classList.add('hidden');
            }
        });

        const avisoVacio = document.getElementById('sinResultadosFiltroCert');
        if (avisoVacio) {
            if (visibles === 0 && items.length > 0) {
                avisoVacio.classList.remove('hidden');
            } else {
                avisoVacio.classList.add('hidden');
            }
        }
    }

    function limpiarFiltroCertificados() {
        const inputBuscar = document.getElementById('inputBuscarCertificado');
        const selectEstado = document.getElementById('selectEstadoCertificado');
        if (inputBuscar) inputBuscar.value = '';
        if (selectEstado) selectEstado.value = '';
        aplicarFiltroCertificados();
    }

    document.addEventListener('DOMContentLoaded', function () {
        const inputBuscar = document.getElementById('inputBuscarCertificado');
        const selectEstado = document.getElementById('selectEstadoCertificado');
        if (inputBuscar) inputBuscar.addEventListener('input', aplicarFiltroCertificados);
        if (selectEstado) selectEstado.addEventListener('change', aplicarFiltroCertificados);
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            cerrarModalCertificado();
            cerrarModalVistaPdfCertificado();
        }
    });
</script>
@endsection
