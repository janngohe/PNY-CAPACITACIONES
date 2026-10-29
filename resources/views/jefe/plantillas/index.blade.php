@extends('layouts.jefe')

@section('title', 'Mis Plantillas de Certificados')
@section('page_title', 'Mis Plantillas de Certificados')

@section('content')
<div class="space-y-6">

    <!-- ENCABEZADO Y ACCIONES PRINCIPALES -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-brand-light text-brand-blue border border-brand-blue/20">
                    <i class="fa-solid fa-sitemap text-[11px]"></i> Área: {{ $area->nombre ?? 'Mi Área' }}
                </span>
                <span class="text-xs text-muted">Gestión de Acreditaciones</span>
            </div>
            <h1 class="font-heading font-extrabold text-xl sm:text-2xl text-brand-dark">
                Plantillas de Certificados del Área
            </h1>
            <p class="text-xs text-muted max-w-2xl mt-0.5">
                Explora las plantillas institucionales asignadas y diseña plantillas personalizadas para las capacitaciones de tu equipo.
            </p>
        </div>

        <button type="button"
                onclick="abrirModalCrearPlantilla()"
                class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-gradient-to-r from-brand-blue to-brand-deep hover:brightness-110 text-white font-heading font-bold text-xs sm:text-sm shadow-md hover:-translate-y-0.5 transition-all cursor-pointer shrink-0">
            <i class="fa-solid fa-plus text-xs"></i>
            <span>Crear Nueva Plantilla</span>
        </button>
    </div>

    <!-- TARJETAS MÉTRICAS RESUMEN -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-3xl border border-line/80 shadow-2xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-brand-light text-brand-blue flex items-center justify-center text-lg font-bold shrink-0">
                <i class="fa-solid fa-stamp"></i>
            </div>
            <div>
                <span class="text-[11px] text-muted font-bold uppercase tracking-wider block">Total Plantillas</span>
                <span class="text-xl sm:text-2xl font-heading font-extrabold text-brand-dark">{{ count($todasPlantillas) }}</span>
            </div>
        </div>

        <!-- KPI Plantillas Activas (Reemplaza Firmas Pendientes temporalmente) -->
        <div class="bg-white p-5 rounded-3xl border border-line/80 shadow-2xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg font-bold shrink-0">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <span class="text-[11px] text-muted font-bold uppercase tracking-wider block">Plantillas Activas</span>
                <span class="text-xl sm:text-2xl font-heading font-extrabold text-brand-dark">
                    {{ collect($todasPlantillas)->where('estado', true)->count() }}
                </span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-line/80 shadow-2xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-brand-blue flex items-center justify-center text-lg font-bold shrink-0">
                <i class="fa-solid fa-building-shield"></i>
            </div>
            <div>
                <span class="text-[11px] text-muted font-bold uppercase tracking-wider block">Asignadas por Admin</span>
                <span class="text-xl sm:text-2xl font-heading font-extrabold text-brand-dark">{{ count($plantillasAdmin) }}</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-line/80 shadow-2xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-lg font-bold shrink-0">
                <i class="fa-solid fa-certificate"></i>
            </div>
            <div>
                <span class="text-[11px] text-muted font-bold uppercase tracking-wider block">Creadas por Mi Área</span>
                <span class="text-xl sm:text-2xl font-heading font-extrabold text-brand-dark">{{ count($plantillasPropias) }}</span>
            </div>
        </div>
    </div>

    {{-- OCULTO TEMPORALMENTE: Banner de Firmas Pendientes para implementación futura
    @if($firmasPendientesCount > 0)
        <div class="p-4 sm:p-5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs sm:text-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-2xs">
            <div class="flex items-start gap-3">
                <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0 text-base">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div>
                    <h3 class="font-heading font-bold text-sm text-amber-950">Tienes {{ $firmasPendientesCount }} plantilla(s) esperando tu firma oficial</h3>
                    <p class="text-amber-800 text-xs mt-0.5">
                        El Administrador ha asignado plantillas institucionales para tu área. Debes cargar o estampar tu firma para que los certificados de tus colaboradores puedan expedirse con tu aval.
                    </p>
                </div>
            </div>
            <button type="button"
                    onclick="filtrarTab('admin')"
                    class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-heading font-bold text-xs shadow-2xs transition-all shrink-0 cursor-pointer">
                Ver Pendientes de Firma
            </button>
        </div>
    @endif
    --}}

    <!-- PESTAÑAS DE FILTRO -->
    <div class="flex items-center gap-2 border-b border-line/70 pb-2 overflow-x-auto text-xs font-semibold">
        <button type="button" onclick="filtrarTab('todas')" id="tab-btn-todas"
                class="tab-btn px-4 py-2 rounded-xl bg-brand-blue text-white shadow-2xs transition-all cursor-pointer">
            Todas ({{ count($todasPlantillas) }})
        </button>
        <button type="button" onclick="filtrarTab('admin')" id="tab-btn-admin"
                class="tab-btn px-4 py-2 rounded-xl text-slate-600 hover:text-brand-blue hover:bg-slate-100 transition-all cursor-pointer">
            Institucionales del Admin ({{ count($plantillasAdmin) }})
            {{-- OCULTO TEMPORALMENTE: Badge firmas pendientes
            @if($firmasPendientesCount > 0)
                <span class="ml-1 px-1.5 py-0.5 rounded-full bg-amber-500 text-white text-[10px] font-bold">{{ $firmasPendientesCount }} pendientes</span>
            @endif
            --}}
        </button>
        <button type="button" onclick="filtrarTab('propias')" id="tab-btn-propias"
                class="tab-btn px-4 py-2 rounded-xl text-slate-600 hover:text-brand-blue hover:bg-slate-100 transition-all cursor-pointer">
            Creadas por Mi Área ({{ count($plantillasPropias) }})
        </button>
    </div>

    <!-- LISTADO DE PLANTILLAS EN GRID -->
    @if($todasPlantillas->isEmpty())
        <div class="bg-white rounded-3xl border border-line/80 p-12 text-center space-y-3">
            <div class="w-16 h-16 mx-auto rounded-3xl bg-brand-light text-brand-blue flex items-center justify-center text-2xl font-bold">
                <i class="fa-solid fa-stamp"></i>
            </div>
            <h3 class="font-heading font-extrabold text-base text-brand-dark">No hay plantillas registradas para tu área</h3>
            <p class="text-xs text-muted max-w-md mx-auto">
                Puedes comenzar creando la primera plantilla personalizada para certificar las capacitaciones de tu área de {{ $area->nombre ?? 'trabajo' }}.
            </p>
            <button type="button" onclick="abrirModalCrearPlantilla()"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-blue text-white font-heading font-bold text-xs shadow-xs hover:bg-brand-deep transition-all cursor-pointer">
                <i class="fa-solid fa-plus"></i>
                <span>Crear Primera Plantilla</span>
            </button>
        </div>
    @else
        <div class="flex flex-col gap-4" id="contenedor-plantillas">
            @foreach($todasPlantillas as $pl)
                @php
                    $esDelAdmin = ($pl->creado_por !== $usuario->id);
                    $tieneFirmaJefe = !empty($pl->firma_1_ruta);
                    $tipoClase = $esDelAdmin ? 'tipo-admin' : 'tipo-propia';
                @endphp
                <div class="plantilla-card {{ $tipoClase }} bg-white rounded-3xl border border-line/80 shadow-2xs overflow-hidden transition-all duration-200 hover:border-brand-blue/40"
                     id="plantilla-card-{{ $pl->id }}"
                     data-id="{{ $pl->id }}">
                    
                    <!-- CABECERA RESUMEN INTERACTIVA (CLICK PARA DESGLOSAR / COLAPSAR) -->
                    <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3.5 cursor-pointer select-none hover:bg-slate-50/70 transition-colors"
                         onclick="toggleDesglosePlantilla({{ $pl->id }})"
                         id="cabecera-plantilla-{{ $pl->id }}"
                         role="button"
                         aria-expanded="false"
                         aria-controls="detalle-plantilla-{{ $pl->id }}">

                        <div class="flex items-center gap-3.5 min-w-0">
                            <!-- Icono con insignia de estado -->
                            <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl {{ $esDelAdmin ? 'bg-blue-50 text-brand-blue border border-blue-100' : 'bg-emerald-50 text-emerald-700 border border-emerald-100' }} flex items-center justify-center text-lg font-bold shrink-0 shadow-2xs">
                                <i class="fa-solid {{ $esDelAdmin ? 'fa-award' : 'fa-certificate' }}"></i>
                            </div>

                            <div class="min-w-0 space-y-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h4 class="font-heading font-extrabold text-sm sm:text-base text-brand-dark leading-tight truncate">
                                        {{ $pl->nombre }}
                                    </h4>

                                    @if($esDelAdmin)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-100/90 text-brand-dark border border-blue-200">
                                            <i class="fa-solid fa-shield-halved text-[9px] text-brand-blue"></i> Institucional
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                            <i class="fa-solid fa-user-check text-[9px]"></i> Creada por Mi Área
                                        </span>
                                    @endif

                                    @if($pl->estado)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <i class="fa-solid fa-circle-check text-[9px]"></i> Activa
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500 border border-slate-200">
                                            Inactiva
                                        </span>
                                    @endif
                                </div>

                                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-[11px] text-muted">
                                    <span>Área vinculada: <strong class="text-slate-700 font-semibold">{{ $pl->area->nombre ?? 'General Institucional' }}</strong></span>
                                    {{-- OCULTO TEMPORALMENTE: Estado de firma en cabecera de tarjeta
                                    <span>•</span>
                                    @if($tieneFirmaJefe)
                                        <span class="inline-flex items-center gap-1 text-emerald-700 font-bold bg-emerald-50/80 px-2 py-0.5 rounded-full border border-emerald-200">
                                            <i class="fa-solid fa-check text-[9px]"></i> Tu Firma Vinculada
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-amber-900 bg-amber-100/90 px-2.5 py-0.5 rounded-full font-bold border border-amber-300 animate-pulse">
                                            <i class="fa-solid fa-triangle-exclamation text-[10px] text-amber-700"></i> Firma Pendiente
                                        </span>
                                    @endif
                                    --}}
                                </div>
                            </div>
                        </div>

                        <!-- Lado derecho del resumen (Acción rápida y chevron interactivo) -->
                        <div class="flex items-center gap-2 shrink-0">
                            {{-- OCULTO TEMPORALMENTE: Botón firmar ahora para implementación futura
                            @if(!$tieneFirmaJefe)
                                <button type="button"
                                        onclick="event.stopPropagation(); abrirModalFirmaJefe({{ $pl->id }}, '{{ addslashes($pl->nombre) }}', '{{ addslashes($pl->firma_1_nombre ?? $usuario->nombre_completo) }}', '{{ addslashes($pl->firma_1_cargo ?? ('Jefe de Área ' . ($area->nombre ?? ''))) }}', '{{ $pl->firma_1_ruta ? asset('storage/' . $pl->firma_1_ruta) : '' }}', '{{ route('jefe.plantillas.firma', $pl) }}')"
                                        class="px-3.5 py-1.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-heading font-bold text-xs shadow-2xs transition-all flex items-center gap-1.5 cursor-pointer">
                                    <i class="fa-solid fa-signature"></i>
                                    <span>Firmar Ahora</span>
                                </button>
                            @endif
                            --}}

                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 text-slate-600 hover:text-brand-blue hover:bg-slate-200 text-xs font-bold transition-all">
                                <span id="texto-chevron-{{ $pl->id }}">Desglosar</span>
                                <i id="icono-chevron-{{ $pl->id }}" class="fa-solid fa-chevron-down text-[11px] transition-transform duration-200"></i>
                            </span>
                        </div>
                    </div>

                    <!-- DETALLE DESGLOSABLE DE LA PLANTILLA (COLLAPSIBLE CONTENT) -->
                    <div id="detalle-plantilla-{{ $pl->id }}"
                         class="hidden border-t border-line/70 bg-gradient-to-b from-slate-50/40 via-white to-white p-5 sm:p-6 space-y-5">
                        
                        <!-- INFORMACIÓN OFICIAL DEL CERTIFICADO -->
                        <div class="rounded-2xl border border-line/80 bg-white p-4 space-y-3 shadow-2xs">
                            <div class="flex items-center justify-between text-xs pb-2 border-b border-line/60">
                                <span class="text-[10px] font-extrabold uppercase tracking-wider text-muted flex items-center gap-1.5">
                                    <i class="fa-solid fa-building-columns text-brand-blue"></i>
                                    Organización Emisora
                                </span>
                                <span class="font-bold text-brand-dark">
                                    {{ $pl->nombre_organizacion ?: 'C.I. Piscícola New York S.A.S.' }}
                                </span>
                            </div>

                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-muted block mb-1">
                                    Texto Oficial / Objeto de Certificación
                                </span>
                                <p class="text-xs text-slate-700 bg-slate-50 p-3 rounded-xl border border-line/60 leading-relaxed font-sans">
                                    {{ $pl->texto_certificado ?: ($pl->descripcion ?: 'Certificación oficial expedida por culminación y aprobación del plan de formación institucional con competencias laborales acreditadas.') }}
                                </p>
                            </div>

                            @if($pl->descripcion)
                                <div class="text-[11px] text-muted flex items-center gap-1.5 pt-1">
                                    <i class="fa-regular fa-note-sticky text-slate-400"></i>
                                    <span>Notas internas: {{ $pl->descripcion }}</span>
                                </div>
                            @endif
                        </div>

                        {{-- OCULTO TEMPORALMENTE: Bloque de firmas en tarjeta para implementación futura
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                            
                            <!-- Columna Firma del Jefe (Acción central) -->
                            <div class="p-4 rounded-2xl border {{ $tieneFirmaJefe ? 'bg-white border-emerald-200 ring-1 ring-emerald-100 shadow-2xs' : 'bg-amber-50/80 border-amber-300 ring-1 ring-amber-100 shadow-2xs' }} space-y-2.5">
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-brand-blue">Tu Firma (Jefe de Área)</span>
                                    @if($tieneFirmaJefe)
                                        <span class="text-[9px] font-extrabold text-emerald-800 bg-emerald-100 px-2 py-0.5 rounded-full border border-emerald-200 inline-flex items-center gap-1">
                                            <i class="fa-solid fa-check text-[8px]"></i> Vinculada
                                        </span>
                                    @else
                                        <span class="text-[9px] font-extrabold text-amber-900 bg-amber-100 px-2 py-0.5 rounded-full border border-amber-300 inline-flex items-center gap-1 animate-pulse">
                                            <i class="fa-solid fa-triangle-exclamation text-[8px]"></i> Pendiente
                                        </span>
                                    @endif
                                </div>

                                @if($tieneFirmaJefe && $pl->firma_1_ruta)
                                    <div class="h-14 bg-slate-50/80 rounded-xl border border-line/70 p-1.5 flex items-center justify-center">
                                        <img src="{{ asset('storage/' . $pl->firma_1_ruta) }}?v={{ $pl->updated_at?->timestamp ?? time() }}" alt="Firma Jefe" class="max-h-12 object-contain">
                                    </div>
                                @elseif($usuario->tieneFirmaRegistrada())
                                    <div class="h-14 bg-slate-50/80 rounded-xl border border-line/70 p-1.5 flex items-center justify-center">
                                        <img src="{{ $usuario->firmaUrl() }}" alt="Firma Jefe" class="max-h-12 object-contain">
                                    </div>
                                @else
                                    <div class="h-14 bg-amber-100/60 rounded-xl border border-dashed border-amber-300 flex items-center justify-center text-[11px] text-amber-800 font-medium">
                                        <i class="fa-solid fa-file-signature mr-1.5 text-amber-600"></i> Firma no vinculada aún
                                    </div>
                                @endif

                                <div>
                                    <p class="font-bold text-brand-dark truncate leading-tight">{{ $pl->firma_1_nombre ?? ($usuario->nombre_completo ?? 'Jefe Inmediato') }}</p>
                                    <p class="text-[10px] text-muted truncate">{{ $pl->firma_1_cargo ?? ('Jefe de Área ' . ($area->nombre ?? '')) }}</p>

                                    @if($pl->firma_1_actualizada_el)
                                        <p class="text-[9px] text-slate-400 mt-0.5">Firmado: {{ $pl->firma_1_actualizada_el->format('d/m/Y H:i') }}</p>
                                    @endif
                                </div>

                                <!-- Botón Cargar / Actualizar Firma del Jefe -->
                                <div class="pt-1.5 border-t border-line/50">
                                    <button type="button"
                                            onclick="abrirModalFirmaJefe({{ $pl->id }}, '{{ addslashes($pl->nombre) }}', '{{ addslashes($pl->firma_1_nombre ?? $usuario->nombre_completo) }}', '{{ addslashes($pl->firma_1_cargo ?? ('Jefe de Área ' . ($area->nombre ?? ''))) }}', '{{ $pl->firma_1_ruta ? asset('storage/' . $pl->firma_1_ruta) . '?v=' . ($pl->updated_at?->timestamp ?? time()) : ($usuario->firma_digital ? asset('storage/' . $usuario->firma_digital) : '') }}', '{{ route('jefe.plantillas.firma', $pl) }}')"
                                            class="w-full py-2 px-3 rounded-xl {{ $tieneFirmaJefe ? 'bg-slate-100 hover:bg-slate-200 text-slate-700' : 'bg-amber-500 hover:bg-amber-600 text-white shadow-2xs font-bold' }} text-[11px] font-heading font-semibold transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                                        <i class="fa-solid fa-signature text-xs"></i>
                                        <span>{{ $tieneFirmaJefe ? 'Actualizar Mi Firma' : 'Cargar Mi Firma Ahora' }}</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Columna Firma Gerencia -->
                            <div class="p-4 rounded-2xl bg-white border border-line/70 space-y-2.5 flex flex-col justify-between shadow-2xs">
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-cyan-800">Gerencia General</span>
                                        @if($pl->firma_2_ruta)
                                            <span class="text-[9px] font-semibold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200">
                                                <i class="fa-solid fa-check text-[8px]"></i> Firma OK
                                            </span>
                                        @else
                                            <span class="text-[9px] text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded">Institucional</span>
                                        @endif
                                    </div>

                                    @if($pl->firma_2_ruta)
                                        <div class="h-14 bg-slate-50/80 rounded-xl border border-line/70 p-1.5 flex items-center justify-center my-1">
                                            <img src="{{ asset('storage/' . $pl->firma_2_ruta) }}?v={{ $pl->updated_at?->timestamp ?? time() }}" alt="Firma Gerencia" class="max-h-12 object-contain">
                                        </div>
                                    @else
                                        <div class="h-14 bg-white/70 rounded-xl border border-line/50 flex items-center justify-center text-[10px] text-slate-400 italic my-1">
                                            Aval institucional
                                        </div>
                                    @endif

                                    <p class="font-bold text-brand-dark truncate leading-tight">{{ $pl->firma_2_nombre ?? 'Dra. Elena Ramos' }}</p>
                                    <p class="text-[10px] text-muted truncate">{{ $pl->firma_2_cargo ?? 'Gerente General' }}</p>
                                </div>
                                <p class="text-[10px] text-slate-400 italic">Segundo firmante institucional</p>
                            </div>
                        </div>
                        --}}

                        <!-- BARRA INFERIOR DE ACCIONES Y PREVISUALIZACIÓN EN TIEMPO REAL -->
                        <div class="pt-3 border-t border-line/70 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                            <div class="flex items-center gap-2">
                                <span class="text-muted text-[11px]">ID #{{ $pl->id }} • Registrada: {{ $pl->created_at ? $pl->created_at->format('d/m/Y') : 'Oficial' }}</span>
                            </div>

                            <div class="flex flex-wrap items-center gap-2">
                                <!-- BOTÓN PREVISUALIZAR CERTIFICADO EN TIEMPO REAL (OCULTO EN MÓVIL POR ESPACIO) -->
                                <button type="button"
                                        onclick="previsualizarCertificadoEnVivo({
                                            nombre: '{{ addslashes($pl->nombre) }}',
                                            organizacion: '{{ addslashes($pl->nombre_organizacion ?: 'C.I. Piscícola New York S.A.S.') }}',
                                            texto: '{{ addslashes($pl->texto_certificado ?: ($pl->descripcion ?: 'Por haber culminado y aprobado satisfactoriamente el plan formativo institucional con competencias laborales acreditadas.')) }}',
                                            jefeNombre: '{{ addslashes($pl->firma_1_nombre ?? $usuario->nombre_completo) }}',
                                            jefeCargo: '{{ addslashes($pl->firma_1_cargo ?? ('Jefe de Área ' . ($area->nombre ?? ''))) }}',
                                            jefeFirmaUrl: '{{ $pl->firma_1_ruta ? asset('storage/' . $pl->firma_1_ruta) . '?v=' . ($pl->updated_at?->timestamp ?? time()) : ($usuario->firma_digital ? asset('storage/' . $usuario->firma_digital) . '?v=' . ($usuario->firma_actualizada_el?->timestamp ?? time()) : '') }}',
                                            gerenteNombre: '{{ addslashes($pl->firma_2_nombre ?? 'Dra. Elena Ramos') }}',
                                            gerenteCargo: '{{ addslashes($pl->firma_2_cargo ?? 'Gerente General') }}',
                                            gerenteFirmaUrl: '{{ $pl->firma_2_ruta ? asset('storage/' . $pl->firma_2_ruta) . '?v=' . ($pl->updated_at?->timestamp ?? time()) : '' }}',
                                            plantillaUrl: '{{ ($pl->ruta_plantilla && $pl->ruta_plantilla !== 'plantillas/oficial_pny.png') ? asset('storage/' . $pl->ruta_plantilla) . '?v=' . ($pl->updated_at?->timestamp ?? time()) : '' }}',
                                            areaNombre: '{{ addslashes($area->nombre ?? 'Producción Piscícola') }}',
                                            codigo: 'PNY-{{ date('Y') }}-{{ str_pad($pl->id, 4, '0', STR_PAD_LEFT) }}'
                                        })"
                                        class="hidden md:inline-flex px-4 py-2 rounded-xl bg-gradient-to-r from-brand-blue to-brand-deep hover:brightness-110 text-white font-heading font-bold text-xs shadow-xs transition-all items-center gap-1.5 cursor-pointer">
                                    <i class="fa-solid fa-award"></i>
                                    <span>Previsualizar Certificado en Vivo</span>
                                </button>

                                @if(!$esDelAdmin)
                                    <button type="button"
                                            class="btn-editar-propia px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition-colors cursor-pointer flex items-center gap-1"
                                            title="Editar mi plantilla"
                                            data-id="{{ $pl->id }}"
                                            data-nombre="{{ $pl->nombre }}"
                                            data-descripcion="{{ $pl->descripcion }}"
                                            data-organizacion="{{ $pl->nombre_organizacion }}"
                                            data-texto="{{ $pl->texto_certificado }}"
                                            data-jefe-nombre="{{ $pl->firma_1_nombre }}"
                                            data-jefe-cargo="{{ $pl->firma_1_cargo }}"
                                            data-jefe-firma-url="{{ $pl->firma_1_ruta ? asset('storage/' . $pl->firma_1_ruta) . '?v=' . ($pl->updated_at?->timestamp ?? time()) : ($usuario->firma_digital ? asset('storage/' . $usuario->firma_digital) . '?v=' . ($usuario->firma_actualizada_el?->timestamp ?? time()) : '') }}"
                                            data-gerente-nombre="{{ $pl->firma_2_nombre }}"
                                            data-gerente-cargo="{{ $pl->firma_2_cargo }}"
                                            data-gerente-firma-url="{{ $pl->firma_2_ruta ? asset('storage/' . $pl->firma_2_ruta) . '?v=' . ($pl->updated_at?->timestamp ?? time()) : '' }}"
                                            data-plantilla-url="{{ ($pl->ruta_plantilla && $pl->ruta_plantilla !== 'plantillas/oficial_pny.png') ? asset('storage/' . $pl->ruta_plantilla) . '?v=' . ($pl->updated_at?->timestamp ?? time()) : '' }}"
                                            data-estado="{{ $pl->estado ? '1' : '0' }}"
                                            data-action="{{ route('jefe.plantillas.update', $pl) }}">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                        <span>Editar</span>
                                    </button>

                                    <form method="POST" action="{{ route('jefe.plantillas.estado', $pl) }}" class="m-0">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="px-2.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition-colors cursor-pointer"
                                                title="{{ $pl->estado ? 'Desactivar plantilla' : 'Activar plantilla' }}">
                                            <i class="fa-solid {{ $pl->estado ? 'fa-toggle-on text-emerald-600' : 'fa-toggle-off text-slate-400' }}"></i>
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('jefe.plantillas.destroy', $pl) }}" class="m-0"
                                          onsubmit="return confirm('¿Estás seguro de eliminar esta plantilla de tu área?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="px-2.5 py-2 rounded-xl bg-slate-100 hover:bg-rose-50 text-slate-500 hover:text-rose-600 transition-colors cursor-pointer"
                                                title="Eliminar plantilla">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>

<!-- ========================================================
     MODAL 1: CARGAR / ACTUALIZAR MI FIRMA (PLANTILLAS DEL ADMIN)
     ======================================================== -->
<div id="modal-firma-jefe" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div id="modal-firma-backdrop" class="fixed inset-0 bg-brand-dark/70 backdrop-blur-sm transition-opacity opacity-0" onclick="cerrarModalFirmaJefe()"></div>

    <div class="fixed inset-0 z-10 overflow-y-auto p-4 sm:p-6 flex items-center justify-center min-h-full">
        <div id="modal-firma-panel" class="relative bg-white rounded-3xl max-w-xl w-full shadow-2xl border border-line/80 overflow-hidden transform transition-all scale-95 opacity-0">
            
            <div class="bg-gradient-to-r from-slate-900 via-brand-dark to-brand-blue text-white px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="w-9 h-9 rounded-xl bg-white/10 text-cyan-300 flex items-center justify-center text-sm font-bold">
                        <i class="fa-solid fa-signature"></i>
                    </span>
                    <div>
                        <h4 class="font-heading font-extrabold text-sm sm:text-base text-white">Cargar Mi Firma Oficial</h4>
                        <p id="modal-firma-subtitulo" class="text-[11px] text-slate-300">Vincula tu firma a la plantilla asignada</p>
                    </div>
                </div>
                <button type="button" onclick="cerrarModalFirmaJefe()" class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors cursor-pointer">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <form id="form-firma-jefe" method="POST" action="" enctype="multipart/form-data" class="p-6 space-y-4 text-xs">
                @csrf
                @method('PATCH')
                <input type="hidden" name="firma_1_canvas" id="input-firma-canvas">

                <div class="p-3.5 rounded-2xl bg-blue-50/70 border border-blue-200 text-slate-700 text-[11px] leading-relaxed">
                    <i class="fa-solid fa-circle-info text-brand-blue mr-1"></i>
                    Esta es una plantilla institucional asignada por el Administrador. Solo se actualizará tu firma y datos de aval para tu área de <strong>{{ $area->nombre ?? 'trabajo' }}</strong>.
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Nombre Completo <span class="text-rose-500">*</span></label>
                        <input type="text" name="firma_1_nombre" id="input-firma-nombre" required
                               class="w-full px-3 py-2 rounded-xl border border-line bg-white text-xs text-brand-dark focus:outline-none focus:border-brand-blue font-semibold">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Cargo Oficial <span class="text-rose-500">*</span></label>
                        <input type="text" name="firma_1_cargo" id="input-firma-cargo" required
                               class="w-full px-3 py-2 rounded-xl border border-line bg-white text-xs text-brand-dark focus:outline-none focus:border-brand-blue">
                    </div>
                </div>

                <!-- SELECTOR DE MÉTODO DE FIRMA: ARCHIVO O PAD DIGITAL -->
                <div class="space-y-3 pt-2">
                    <div class="flex items-center justify-between border-b border-line/60 pb-2">
                        <label class="font-heading font-extrabold text-xs text-brand-dark uppercase tracking-wider">
                            Rúbrica Digital
                        </label>
                        <div class="inline-flex rounded-lg bg-slate-100 p-0.5 text-[11px]">
                            <button type="button" onclick="cambiarModoFirma('archivo')" id="btn-modo-archivo"
                                    class="px-2.5 py-1 rounded-md font-bold bg-white text-brand-blue shadow-2xs">
                                Subir Archivo PNG
                            </button>
                            <button type="button" onclick="cambiarModoFirma('dibujar')" id="btn-modo-dibujar"
                                    class="px-2.5 py-1 rounded-md font-bold text-slate-600 hover:text-brand-blue">
                                Dibujar en Pantalla
                            </button>
                        </div>
                    </div>

                    <!-- MODO 1: ARCHIVO -->
                    <div id="bloque-firma-archivo" class="space-y-2">
                        <div class="p-4 rounded-2xl bg-slate-50 border border-line text-center space-y-2">
                            <i class="fa-solid fa-cloud-arrow-up text-2xl text-slate-400"></i>
                            <p class="text-[11px] text-muted">Selecciona tu firma escaneada (PNG con fondo transparente recomendado, máx. 2MB)</p>
                            <input type="file" name="firma_1_archivo" accept="image/png,image/jpeg,image/webp"
                                   class="w-full text-[11px] text-slate-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-[11px] file:font-bold file:bg-brand-blue file:text-white hover:file:bg-brand-deep cursor-pointer">
                        </div>

                        <div id="preview-firma-actual-box" class="hidden p-3 rounded-xl bg-slate-100 border border-line/60 flex items-center justify-between">
                            <span class="text-[11px] text-muted">Firma actual registrada:</span>
                            <img id="preview-firma-actual-img" src="" alt="Firma Actual" class="h-10 object-contain">
                        </div>
                    </div>

                    <!-- MODO 2: DIBUJAR EN PANTALLA -->
                    <div id="bloque-firma-dibujar" class="hidden space-y-2">
                        <p class="text-[11px] text-muted">Dibuja tu firma en el recuadro blanco usando el mouse o tu dedo en pantalla táctil:</p>
                        <div class="relative rounded-2xl border-2 border-dashed border-brand-blue/40 bg-white overflow-hidden">
                            <canvas id="canvas-firma" width="500" height="160" class="w-full h-40 touch-none cursor-crosshair bg-white"></canvas>
                            <button type="button" onclick="limpiarCanvasFirma()"
                                    class="absolute top-2 right-2 px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-[10px] shadow-2xs">
                                <i class="fa-solid fa-eraser mr-1"></i> Borrar
                            </button>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-line/70">
                    <button type="button" onclick="cerrarModalFirmaJefe()"
                            class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-heading font-bold text-xs transition-colors cursor-pointer">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-brand-blue to-brand-deep hover:brightness-110 text-white font-heading font-bold text-xs shadow-md transition-all cursor-pointer">
                        <i class="fa-solid fa-check mr-1.5"></i> Guardar Mi Firma Oficial
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================
     MODAL 2: CREAR NUEVA PLANTILLA (STUDIO EN TIEMPO REAL)
     ======================================================== -->
<div id="modal-crear-plantilla" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div id="modal-crear-backdrop" class="fixed inset-0 bg-brand-dark/80 backdrop-blur-sm transition-opacity opacity-0" onclick="cerrarModalCrearPlantilla()"></div>

    <div class="fixed inset-0 z-10 overflow-y-auto p-2 sm:p-4 lg:p-6 flex items-center justify-center min-h-full">
        <div id="modal-crear-panel" class="relative bg-white rounded-3xl w-full max-w-xl lg:max-w-[96vw] xl:max-w-[1520px] h-[92vh] shadow-2xl border border-line/80 overflow-hidden transform transition-all scale-95 opacity-0 flex flex-col font-sans">
            
            <!-- BARRA SUPERIOR DE LA VENTANA -->
            <div class="bg-slate-900 text-white px-6 py-3.5 flex items-center justify-between border-b border-slate-800 shrink-0">
                <div class="flex items-center gap-3">
                    <span class="w-9 h-9 rounded-xl bg-white/10 text-cyan-300 flex items-center justify-center text-sm font-bold">
                        <i class="fa-solid fa-stamp"></i>
                    </span>
                    <div>
                        <h4 class="font-heading font-extrabold text-sm sm:text-base text-white">Crear Plantilla para Mi Área</h4>
                        <p class="text-[11px] text-slate-300 hidden lg:block">Completa los campos a la izquierda y visualiza el diploma en tiempo real a la derecha.</p>
                        <p class="text-[11px] text-slate-300 lg:hidden">Completa los datos del formulario para registrar la plantilla de tu área.</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" onclick="cerrarModalCrearPlantilla()" class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors cursor-pointer" title="Cerrar">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>
            </div>

            <!-- CONTENIDO: 2 COLUMNAS (FORMULARIO A LA IZQUIERDA + CANVAS DIPLOMA A LA DERECHA) -->
            <div class="flex-1 flex flex-col lg:flex-row overflow-hidden">
                
                <!-- COLUMNA IZQUIERDA: FORMULARIO -->
                <form id="form-crear-plantilla" method="POST" action="{{ route('jefe.plantillas.store') }}" enctype="multipart/form-data" 
                      class="w-full lg:w-[420px] xl:w-[460px] lg:shrink-0 lg:border-r border-line bg-slate-50 flex flex-col overflow-hidden flex-1">
                    @csrf
                    
                    <div class="p-3.5 border-b border-line/70 bg-white flex items-center justify-between shrink-0">
                        <span class="text-xs font-heading font-extrabold text-brand-dark uppercase tracking-wider flex items-center gap-1.5">
                            <i class="fa-solid fa-sliders text-brand-blue"></i>
                            Campos del Certificado
                        </span>
                        <span class="text-[10px] text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200 font-bold hidden lg:flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> En vivo
                        </span>
                    </div>

                    <div class="flex-1 overflow-y-auto p-4 sm:p-5 space-y-4 text-xs font-sans">
                        
                        <!-- 1. IDENTIFICACIÓN -->
                        <div class="bg-white p-3.5 rounded-2xl border border-line/80 shadow-2xs space-y-2.5">
                            <label class="block font-heading font-bold text-brand-dark text-xs">Identificación de la Plantilla</label>
                            
                            <div>
                                <label class="block text-slate-600 font-semibold mb-1">Nombre de la Plantilla <span class="text-rose-500">*</span></label>
                                <input type="text" name="nombre" id="crear-nombre" required placeholder="Ej: Certificado Operativo Piscícola 2026"
                                       class="w-full px-3 py-2 rounded-xl border border-line bg-white text-xs text-brand-dark focus:outline-none focus:border-brand-blue font-semibold">
                            </div>

                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-slate-600 font-semibold mb-1">Área Vinculada</label>
                                    <input type="text" readonly value="{{ $area->nombre ?? 'Mi Área' }}"
                                           class="w-full px-2.5 py-1.5 rounded-lg border border-line bg-slate-100 text-xs text-slate-500 font-bold cursor-not-allowed">
                                </div>
                                <div>
                                    <label class="block text-slate-600 font-semibold mb-1">Notas / Uso</label>
                                    <input type="text" name="descripcion" id="crear-descripcion" placeholder="Ej: Uso interno área"
                                           class="w-full px-2.5 py-1.5 rounded-lg border border-line bg-white text-xs text-brand-dark focus:outline-none focus:border-brand-blue">
                                </div>
                            </div>
                        </div>

                        <!-- 2. ENCABEZADO Y EMISOR -->
                        <div class="bg-white p-3.5 rounded-2xl border border-line/80 shadow-2xs space-y-2.5">
                            <label class="block font-heading font-bold text-brand-dark text-xs">Encabezado y Emisor</label>
                            
                            <div>
                                <label class="block text-slate-600 font-semibold mb-1">Nombre de la Organización</label>
                                <input type="text" name="nombre_organizacion" id="crear-organizacion" value="C.I. Piscícola New York S.A.S."
                                       class="w-full px-3 py-2 rounded-xl border border-line bg-white text-xs text-brand-dark focus:outline-none focus:border-brand-blue font-semibold">
                            </div>

                            <div>
                                <label class="block text-slate-600 font-semibold mb-1">Subtítulo Institucional</label>
                                <input type="text" id="crear-subtitulo" value="Sistema Institucional de Gestión del Talento Humano & Capacitación Continuada"
                                       class="w-full px-3 py-2 rounded-xl border border-line bg-white text-xs text-brand-dark focus:outline-none focus:border-brand-blue">
                            </div>

                            <div>
                                <label class="block text-slate-600 font-semibold mb-1">Título del Certificado</label>
                                <input type="text" id="crear-titulo" value="CERTIFICADO DE CAPACITACIÓN Y APROBACIÓN"
                                       class="w-full px-3 py-2 rounded-xl border border-line bg-white text-xs text-brand-dark focus:outline-none focus:border-brand-blue font-bold uppercase">
                            </div>
                        </div>

                        <!-- 3. TEXTO OFICIAL Y CAPACITACIÓN -->
                        <div class="bg-white p-3.5 rounded-2xl border border-line/80 shadow-2xs space-y-2.5">
                            <label class="block font-heading font-bold text-brand-dark text-xs">Contenido de Acreditación</label>
                            
                            <div>
                                <label class="block text-slate-600 font-semibold mb-1">Texto Oficial / Objeto <span class="text-rose-500">*</span></label>
                                <textarea name="texto_certificado" id="crear-texto" rows="2"
                                          class="w-full px-3 py-2 rounded-xl border border-line bg-white text-xs text-brand-dark focus:outline-none focus:border-brand-blue leading-relaxed font-sans">Por haber completado satisfactoriamente el programa de capacitación oficial con competencias laborales aprobadas.</textarea>
                            </div>

                            <div>
                                <label class="block text-slate-600 font-semibold mb-1">Nombre de la Capacitación</label>
                                <input type="text" id="crear-capacitacion" value="Manejo Integral y Bioseguridad en Producción Piscícola"
                                       class="w-full px-3 py-2 rounded-xl border border-line bg-white text-xs text-brand-dark focus:outline-none focus:border-brand-blue font-semibold">
                            </div>

                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-slate-600 font-semibold mb-1">Participante Muestra</label>
                                    <input type="text" id="crear-participante" value="Juan Pérez Gómez"
                                           class="w-full px-2.5 py-1.5 rounded-lg border border-line bg-white text-xs text-brand-dark focus:outline-none focus:border-brand-blue font-semibold">
                                </div>
                                <div>
                                    <label class="block text-slate-600 font-semibold mb-1">Documento Muestra</label>
                                    <input type="text" id="crear-documento" value="C.C. 1.117.811.618"
                                           class="w-full px-2.5 py-1.5 rounded-lg border border-line bg-white text-xs text-brand-dark focus:outline-none focus:border-brand-blue">
                                </div>
                            </div>

                            <div>
                                <label class="block text-slate-600 font-semibold mb-1">Lugar y Fecha de Expedición</label>
                                <input type="text" id="crear-ciudad-fecha" value="Rivera - Huila, {{ date('d/m/Y') }}"
                                       class="w-full px-3 py-2 rounded-xl border border-line bg-white text-xs text-brand-dark focus:outline-none focus:border-brand-blue">
                            </div>
                        </div>

                        <!-- 4. FIRMAS OFICIALES (OCULTAS TEMPORALMENTE PARA IMPLEMENTACIÓN A FUTURO) -->
                        <div class="hidden" style="display: none;">
                            <div class="bg-white p-3.5 rounded-2xl border border-line/80 shadow-2xs space-y-3">
                                <label class="block font-heading font-bold text-brand-dark text-xs">Firmas Oficiales del Certificado</label>

                                <!-- Firma 1: Jefe -->
                                <div class="p-3 bg-slate-50 rounded-xl border border-line/60 space-y-2">
                                    <span class="font-heading font-extrabold text-[11px] text-brand-blue block">Firma 1: Jefe de Área (Tu Firma)</span>
                                    <input type="text" name="firma_1_nombre" id="crear-firma-1-nombre" value="{{ $usuario->nombre_completo }}" placeholder="Nombre del Jefe"
                                           class="w-full px-2.5 py-1.5 rounded-lg border border-line text-xs font-semibold">
                                    <input type="text" name="firma_1_cargo" id="crear-firma-1-cargo" value="{{ $usuario->cargoOficial() }}" placeholder="Cargo del Jefe"
                                           class="w-full px-2.5 py-1.5 rounded-lg border border-line text-xs font-semibold">
                                    
                                    @if($usuario->tieneFirmaRegistrada())
                                        <div class="p-2 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-between text-xs shadow-2xs">
                                            <div class="flex items-center gap-1.5">
                                                <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                                                <span class="text-[11px] text-emerald-800 font-bold">Firma guardada en tu cuenta cargada automáticamente</span>
                                            </div>
                                            <div class="h-7 w-20 bg-white rounded-lg border border-emerald-200 p-0.5 flex items-center justify-center shrink-0">
                                                <img src="{{ $usuario->firmaUrl() }}" alt="Firma" class="max-h-6 max-w-full object-contain">
                                            </div>
                                        </div>
                                    @endif

                                    <div>
                                        <label class="block text-[10px] text-muted font-bold mb-0.5">Reemplazar Imagen de Firma (Opcional)</label>
                                        <input type="file" name="firma_1_archivo" id="crear-firma-1-archivo" accept="image/png,image/jpeg,image/webp"
                                               class="w-full text-[10px]">
                                    </div>
                                </div>

                                <!-- Firma 2: Aval Institucional / Gerencia General (Definida exclusivamente por el Administrador) -->
                                <div class="p-3 bg-slate-50/80 rounded-xl border border-line/70 space-y-2.5">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="font-heading font-extrabold text-[11px] text-brand-dark flex items-center gap-1.5">
                                            <i class="fa-solid fa-building-shield text-brand-blue text-xs"></i>
                                            <span>Firma 2: Aval Institucional (Gerencia)</span>
                                        </span>
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-extrabold uppercase tracking-wide bg-amber-50 text-amber-700 border border-amber-200">
                                            <i class="fa-solid fa-lock text-[8px]"></i>
                                            Solo Administrador
                                        </span>
                                    </div>

                                    <div class="p-2.5 bg-white rounded-lg border border-line/70 space-y-1.5 shadow-2xs">
                                        <div class="flex items-start justify-between gap-2">
                                            <div>
                                                <p class="font-heading font-bold text-xs text-brand-dark leading-tight">
                                                    {{ $firma2Oficial['nombre'] }}
                                                </p>
                                                <p class="text-[10px] text-muted">
                                                    {{ $firma2Oficial['cargo'] }}
                                                </p>
                                            </div>
                                            @if(!empty($firma2Oficial['url']))
                                                <div class="h-8 w-20 bg-slate-50 rounded border border-line/60 p-0.5 flex items-center justify-center shrink-0">
                                                    <img src="{{ $firma2Oficial['url'] }}" alt="Firma Institucional" class="max-h-7 max-w-full object-contain">
                                                </div>
                                            @else
                                                <span class="text-[9px] text-slate-400 italic">Rúbrica institucional</span>
                                            @endif
                                        </div>
                                        <p class="text-[10px] text-slate-500 leading-normal flex items-center gap-1 pt-1 border-t border-slate-100">
                                            <i class="fa-solid fa-circle-info text-brand-blue text-[9px]"></i>
                                            Esta firma es estándar e institucional. Solo el Administrador general puede modificar sus datos.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 5. MAQUETA FONDO OPCIONAL -->
                        <div class="bg-white p-3.5 rounded-2xl border border-line/80 shadow-2xs space-y-1.5">
                            <label class="block font-heading font-bold text-brand-dark text-xs">Maqueta / Fondo Personalizado (Opcional)</label>
                            <p class="text-[11px] text-slate-500">Si cuentas con un diseño de fondo propio para el diploma, súbelo aquí.</p>
                            <input type="file" name="archivo_plantilla" id="crear-archivo-plantilla" accept="image/png,image/jpeg,image/webp"
                                   class="w-full text-xs text-slate-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-brand-blue file:text-white cursor-pointer">
                        </div>

                    </div>

                    <!-- BOTONES INFERIORES -->
                    <div class="p-3.5 bg-white border-t border-line/80 flex items-center justify-end gap-2 shrink-0">
                        <button type="button" onclick="cerrarModalCrearPlantilla()"
                                class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-heading font-bold text-xs transition-colors cursor-pointer">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-brand-blue to-brand-deep hover:brightness-110 text-white font-heading font-bold text-xs shadow-md transition-all cursor-pointer flex items-center gap-1.5">
                            <i class="fa-solid fa-stamp"></i>
                            <span>Crear Plantilla</span>
                        </button>
                    </div>
                </form>

                <!-- COLUMNA DERECHA: STUDIO CANVAS CON EL DIPLOMA OFICIAL (IDÉNTICO A EMPLEADO) -->
                <aside class="hidden lg:flex flex-1 bg-slate-200/90 p-4 sm:p-6 lg:p-8 overflow-y-auto overflow-x-auto items-center justify-center">
                    
                    <!-- DIPLOMA OFICIAL EN FORMATO HORIZONTAL ELEGANTE -->
                    <div id="preview-crear-diploma-wrapper" class="w-full max-w-4xl bg-white rounded-3xl border-8 border-slate-100 shadow-2xl relative overflow-hidden shrink-0 transition-all">
                        
                        <!-- BANNER DE OLAS EN LA PARTE SUPERIOR -->
                        <div class="relative bg-gradient-to-r from-brand-dark via-brand-deep to-[#0056b3] text-white p-5 sm:p-6 overflow-hidden">
                            <div class="relative z-10 flex flex-col sm:flex-row items-center justify-between gap-3 text-center sm:text-left">
                                <div class="flex items-center gap-3">
                                    <div class="bg-white/95 p-2 rounded-2xl border border-white/40 shadow-sm shrink-0">
                                        <img src="{{ asset('images/Logo.png') }}" alt="Logo PNY" class="h-10 sm:h-12 w-auto object-contain">
                                    </div>
                                    <div>
                                        <h2 id="preview-crear-organizacion" class="font-heading font-black text-base sm:text-lg text-white tracking-wide uppercase">
                                            C.I. PISCÍCOLA NEW YORK S.A.S.
                                        </h2>
                                        <p id="preview-crear-subtitulo" class="text-[11px] text-slate-200/90 font-medium">
                                            Sistema Institucional de Gestión del Talento Humano & Capacitación Continuada
                                        </p>
                                    </div>
                                </div>

                                <div class="text-right shrink-0">
                                    <span class="font-mono text-[10px] text-cyan-200 bg-white/10 px-3 py-1 rounded-full border border-white/20">
                                        PNY-{{ date('Y') }}-NUEVA
                                    </span>
                                </div>
                            </div>

                            <!-- SVG Wave Motif Decorativo -->
                            <div class="absolute bottom-0 left-0 w-full overflow-hidden leading-none pointer-events-none opacity-25">
                                <svg class="relative block w-full h-5 text-white" viewBox="0 0 1200 40" preserveAspectRatio="none">
                                    <path d="M0,0 C150,35 350,10 500,25 C650,40 850,5 1000,20 C1100,30 1160,15 1200,25 L1200,40 L0,40 Z" fill="currentColor"/>
                                </svg>
                            </div>
                        </div>

                        <!-- CUERPO INTERNO DEL CERTIFICADO -->
                        <div class="p-6 sm:p-10 space-y-6 bg-gradient-to-b from-white via-slate-50/50 to-white relative">
                            
                            <!-- Marca de agua central suave -->
                            <div class="absolute inset-0 flex items-center justify-center pointer-events-none z-0">
                                <img src="{{ asset('images/Logo.png') }}" alt="Watermark" class="w-[450px] max-w-[85vw] h-auto object-contain select-none" style="opacity: 0.04; filter: grayscale(100%) opacity(0.5);">
                            </div>

                            <!-- Encabezado de Acreditación con Logo -->
                            <div class="text-center space-y-2.5 relative z-10">
                                <div class="flex items-center justify-center mb-1">
                                    <img src="{{ asset('images/Logo.png') }}" alt="Logo" class="h-14 sm:h-16 w-auto object-contain drop-shadow-sm">
                                </div>

                                <h1 id="preview-crear-titulo" class="font-heading font-black text-2xl sm:text-3xl text-brand-dark tracking-tight uppercase leading-tight">
                                    CERTIFICADO DE CAPACITACIÓN Y APROBACIÓN
                                </h1>

                                <p class="text-xs sm:text-sm text-slate-500 max-w-xl mx-auto font-medium">
                                    La Dirección de <strong id="preview-crear-organizacion-inline" class="text-brand-dark font-bold">C.I. Piscícola New York S.A.S.</strong> hace constar formalmente que:
                                </p>
                            </div>

                            <!-- Datos del Colaborador -->
                            <div class="text-center space-y-2 py-1 relative z-10">
                                <div class="inline-block relative">
                                    <h2 id="preview-crear-participante" class="font-heading font-black text-2xl sm:text-3xl text-brand-dark tracking-tight px-4 uppercase">
                                        Juan Pérez Gómez
                                    </h2>
                                    <div class="h-1 w-full bg-gradient-to-r from-transparent via-brand-blue to-transparent mt-1"></div>
                                </div>

                                <p class="text-xs sm:text-sm text-slate-600 font-semibold">
                                    Con documento de identificación N.º <strong id="preview-crear-documento" class="text-brand-dark font-extrabold">C.C. 1.117.811.618</strong>, adscrito al área de <strong id="preview-crear-area" class="text-brand-blue font-extrabold">{{ $area->nombre ?? 'Producción Piscícola' }}</strong>,
                                </p>
                            </div>

                            <!-- Detalles del Curso / Capacitación y Acreditación -->
                            <div class="max-w-2xl mx-auto bg-white p-5 sm:p-6 rounded-2xl border border-line/80 shadow-xs text-center space-y-3 relative z-10">
                                <span class="text-[11px] uppercase font-extrabold tracking-widest text-brand-sky block">
                                    HA CUMPLIDO SATISFACTORIAMENTE LA CAPACITACIÓN FORMATIVA EN:
                                </span>
                                
                                <h3 id="preview-crear-capacitacion" class="font-heading font-black text-lg sm:text-xl text-brand-dark leading-snug uppercase">
                                    Manejo Integral y Bioseguridad en Producción Piscícola
                                </h3>
                                
                                <p id="preview-crear-texto" class="text-xs sm:text-sm text-slate-600 font-medium leading-relaxed max-w-xl mx-auto font-sans">
                                    Por haber completado satisfactoriamente el programa de capacitación oficial con competencias laborales aprobadas.
                                </p>

                                <p class="text-xs text-slate-500 italic max-w-lg mx-auto pt-0.5">
                                    Se expide el presente certificado como constancia de su participación y cumplimiento de la actividad formativa.
                                </p>

                                <div class="pt-1 flex items-center justify-center text-xs">
                                    <span id="preview-crear-ciudad-fecha" class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full bg-slate-100 text-slate-700 font-bold border border-slate-200">
                                        <i class="fa-solid fa-location-dot text-brand-blue"></i> Rivera - Huila, {{ date('d/m/Y') }}
                                    </span>
                                </div>
                            </div>

                            <!-- FIRMAS DINÁMICAS (OCULTAS TEMPORALMENTE PARA IMPLEMENTACIÓN A FUTURO) -->
                            <div class="hidden" style="display: none;">
                                <div class="pt-6 border-t border-line/70 grid grid-cols-2 gap-6 text-center text-xs relative z-10">
                                    <!-- Firma 1: Jefe Inmediato -->
                                    <div class="space-y-1.5">
                                        <div class="h-14 flex items-end justify-center pb-1">
                                            <img id="preview-crear-firma-1-img" src="" alt="Firma Jefe" class="max-h-14 max-w-[170px] object-contain mx-auto hidden">
                                            <span id="preview-crear-firma-1-placeholder" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 text-brand-blue font-bold text-xs border border-blue-200/80">
                                                <i class="fa-solid fa-signature"></i>
                                                <span>Firma Verificada</span>
                                            </span>
                                        </div>
                                        <div class="w-56 mx-auto border-t-2 border-brand-dark/40 pt-1.5">
                                            <p id="preview-crear-firma-1-nombre" class="font-heading font-extrabold text-sm text-brand-dark truncate leading-tight">
                                                {{ $usuario->nombre_completo }}
                                            </p>
                                            <p id="preview-crear-firma-1-cargo" class="text-[11px] text-muted font-medium truncate">
                                                Jefe de Área {{ $area->nombre ?? '' }}
                                            </p>
                                            <span class="inline-block text-[10px] font-bold text-brand-blue uppercase mt-0.5 tracking-wider">Jefe de Área</span>
                                        </div>
                                    </div>

                                    <!-- Firma 2: Gerente General -->
                                    <div class="space-y-1.5">
                                        <div class="h-14 flex items-end justify-center pb-1">
                                            <img id="preview-crear-firma-2-img" src="" alt="Firma Gerencia" class="max-h-14 max-w-[170px] object-contain mx-auto hidden">
                                            <span id="preview-crear-firma-2-placeholder" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 text-brand-blue font-bold text-xs border border-blue-200/80">
                                                <i class="fa-solid fa-signature"></i>
                                                <span>Firma Verificada</span>
                                            </span>
                                        </div>
                                        <div class="w-56 mx-auto border-t-2 border-brand-dark/40 pt-1.5">
                                            <p id="preview-crear-firma-2-nombre" class="font-heading font-extrabold text-sm text-brand-dark truncate leading-tight">
                                                Dra. Elena Ramos
                                            </p>
                                            <p id="preview-crear-firma-2-cargo" class="text-[11px] text-muted font-medium truncate">
                                                Gerente General
                                            </p>
                                            <span class="inline-block text-[10px] font-bold text-brand-dark uppercase mt-0.5 tracking-wider">Gerencia General</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- Pie del diploma -->
                        <div class="bg-slate-900 text-slate-400 p-2.5 text-center text-[10px] border-t border-slate-800">
                            Documento Oficial Institucional emitido por el Sistema de Capacitación de C.I. Piscícola New York S.A.S.
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================
     MODAL 3: EDITAR PLANTILLA PROPIA DEL JEFE (STUDIO EN TIEMPO REAL)
     ======================================================== -->
<div id="modal-editar-plantilla" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div id="modal-editar-backdrop" class="fixed inset-0 bg-brand-dark/80 backdrop-blur-sm transition-opacity opacity-0" onclick="cerrarModalEditarPlantilla()"></div>

    <div class="fixed inset-0 z-10 overflow-y-auto p-2 sm:p-4 lg:p-6 flex items-center justify-center min-h-full">
        <div id="modal-editar-panel" class="relative bg-white rounded-3xl w-full max-w-xl lg:max-w-[96vw] xl:max-w-[1520px] h-[92vh] shadow-2xl border border-line/80 overflow-hidden transform transition-all scale-95 opacity-0 flex flex-col font-sans">
            
            <div class="bg-gradient-to-r from-brand-dark via-brand-deep to-brand-blue text-white px-6 py-3.5 flex items-center justify-between border-b border-slate-800 shrink-0">
                <div class="flex items-center gap-3">
                    <span class="w-9 h-9 rounded-xl bg-white/10 text-cyan-300 flex items-center justify-center text-sm font-bold">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </span>
                    <div>
                        <h4 class="font-heading font-extrabold text-sm sm:text-base text-white">Editar Plantilla de Mi Área</h4>
                        <p class="text-[11px] text-slate-200 hidden lg:block">Modifica los campos del certificado y visualiza los cambios en tiempo real a la derecha.</p>
                        <p class="text-[11px] text-slate-200 lg:hidden">Modifica los campos del certificado para actualizar tu plantilla.</p>
                    </div>
                </div>
                <button type="button" onclick="cerrarModalEditarPlantilla()" class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors cursor-pointer" title="Cerrar">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <div class="flex-1 flex flex-col lg:flex-row overflow-hidden">
                <!-- COLUMNA IZQUIERDA: FORMULARIO DE EDICIÓN -->
                <form id="form-editar-plantilla" method="POST" action="" enctype="multipart/form-data" 
                      class="w-full lg:w-[420px] xl:w-[460px] lg:shrink-0 lg:border-r border-line bg-slate-50 flex flex-col overflow-hidden flex-1">
                    @csrf
                    @method('PUT')

                    <div class="p-3.5 border-b border-line/70 bg-white flex items-center justify-between shrink-0">
                        <span class="text-xs font-heading font-extrabold text-brand-dark uppercase tracking-wider flex items-center gap-1.5">
                            <i class="fa-solid fa-sliders text-brand-blue"></i>
                            Campos del Certificado
                        </span>
                        <span class="text-[10px] text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200 font-bold hidden lg:flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> En vivo
                        </span>
                    </div>

                    <div class="flex-1 overflow-y-auto p-4 sm:p-5 space-y-4 text-xs font-sans">
                        
                        <!-- 1. IDENTIFICACIÓN -->
                        <div class="bg-white p-3.5 rounded-2xl border border-line/80 shadow-2xs space-y-2.5">
                            <label class="block font-heading font-bold text-brand-dark text-xs">Identificación de la Plantilla</label>
                            
                            <div>
                                <label class="block text-slate-600 font-semibold mb-1">Nombre de la Plantilla <span class="text-rose-500">*</span></label>
                                <input type="text" name="nombre" id="editar-nombre" required
                                       class="w-full px-3 py-2 rounded-xl border border-line bg-white text-xs text-brand-dark focus:outline-none focus:border-brand-blue font-semibold">
                            </div>

                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-slate-600 font-semibold mb-1">Área Vinculada</label>
                                    <input type="text" readonly value="{{ $area->nombre ?? 'Mi Área' }}"
                                           class="w-full px-2.5 py-1.5 rounded-lg border border-line bg-slate-100 text-xs text-slate-500 font-bold cursor-not-allowed">
                                </div>
                                <div>
                                    <label class="block text-slate-600 font-semibold mb-1">Notas / Uso</label>
                                    <input type="text" name="descripcion" id="editar-descripcion"
                                           class="w-full px-2.5 py-1.5 rounded-lg border border-line bg-white text-xs text-brand-dark focus:outline-none focus:border-brand-blue">
                                </div>
                            </div>
                        </div>

                        <!-- 2. ENCABEZADO Y EMISOR -->
                        <div class="bg-white p-3.5 rounded-2xl border border-line/80 shadow-2xs space-y-2.5">
                            <label class="block font-heading font-bold text-brand-dark text-xs">Encabezado y Emisor</label>
                            
                            <div>
                                <label class="block text-slate-600 font-semibold mb-1">Nombre de la Organización</label>
                                <input type="text" name="nombre_organizacion" id="editar-organizacion"
                                       class="w-full px-3 py-2 rounded-xl border border-line bg-white text-xs text-brand-dark focus:outline-none focus:border-brand-blue font-semibold">
                            </div>

                            <div>
                                <label class="block text-slate-600 font-semibold mb-1">Subtítulo Institucional</label>
                                <input type="text" id="editar-subtitulo" value="Sistema Institucional de Gestión del Talento Humano & Capacitación Continuada"
                                       class="w-full px-3 py-2 rounded-xl border border-line bg-white text-xs text-brand-dark focus:outline-none focus:border-brand-blue">
                            </div>

                            <div>
                                <label class="block text-slate-600 font-semibold mb-1">Título del Certificado</label>
                                <input type="text" id="editar-titulo" value="CERTIFICADO DE CAPACITACIÓN Y APROBACIÓN"
                                       class="w-full px-3 py-2 rounded-xl border border-line bg-white text-xs text-brand-dark focus:outline-none focus:border-brand-blue font-bold uppercase">
                            </div>
                        </div>

                        <!-- 3. TEXTO OFICIAL Y CAPACITACIÓN -->
                        <div class="bg-white p-3.5 rounded-2xl border border-line/80 shadow-2xs space-y-2.5">
                            <label class="block font-heading font-bold text-brand-dark text-xs">Contenido de Acreditación</label>
                            
                            <div>
                                <label class="block text-slate-600 font-semibold mb-1">Texto Oficial / Objeto <span class="text-rose-500">*</span></label>
                                <textarea name="texto_certificado" id="editar-texto" rows="2"
                                          class="w-full px-3 py-2 rounded-xl border border-line bg-white text-xs text-brand-dark focus:outline-none focus:border-brand-blue leading-relaxed font-sans"></textarea>
                            </div>

                            <div>
                                <label class="block text-slate-600 font-semibold mb-1">Nombre de la Capacitación</label>
                                <input type="text" id="editar-capacitacion" value="Manejo Integral y Bioseguridad en Producción Piscícola"
                                       class="w-full px-3 py-2 rounded-xl border border-line bg-white text-xs text-brand-dark focus:outline-none focus:border-brand-blue font-semibold">
                            </div>

                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-slate-600 font-semibold mb-1">Participante Muestra</label>
                                    <input type="text" id="editar-participante" value="Juan Pérez Gómez"
                                           class="w-full px-2.5 py-1.5 rounded-lg border border-line bg-white text-xs text-brand-dark focus:outline-none focus:border-brand-blue font-semibold">
                                </div>
                                <div>
                                    <label class="block text-slate-600 font-semibold mb-1">Documento Muestra</label>
                                    <input type="text" id="editar-documento" value="C.C. 1.117.811.618"
                                           class="w-full px-2.5 py-1.5 rounded-lg border border-line bg-white text-xs text-brand-dark focus:outline-none focus:border-brand-blue">
                                </div>
                            </div>

                            <div>
                                <label class="block text-slate-600 font-semibold mb-1">Lugar y Fecha de Expedición</label>
                                <input type="text" id="editar-ciudad-fecha" value="Rivera - Huila, {{ date('d/m/Y') }}"
                                       class="w-full px-3 py-2 rounded-xl border border-line bg-white text-xs text-brand-dark focus:outline-none focus:border-brand-blue">
                            </div>
                        </div>

                        <!-- 4. FIRMAS OFICIALES (OCULTAS TEMPORALMENTE PARA IMPLEMENTACIÓN A FUTURO) -->
                        <div class="hidden" style="display: none;">
                            <div class="bg-white p-3.5 rounded-2xl border border-line/80 shadow-2xs space-y-3">
                                <label class="block font-heading font-bold text-brand-dark text-xs">Firmas Oficiales del Certificado</label>

                                <!-- Firma 1: Jefe -->
                                <div class="p-3 bg-slate-50 rounded-xl border border-line/60 space-y-2">
                                    <span class="font-heading font-extrabold text-[11px] text-brand-blue block">Firma 1: Jefe de Área (Tu Firma)</span>
                                    <input type="text" name="firma_1_nombre" id="editar-firma-1-nombre" placeholder="Nombre del Jefe"
                                           class="w-full px-2.5 py-1.5 rounded-lg border border-line text-xs font-semibold">
                                    <input type="text" name="firma_1_cargo" id="editar-firma-1-cargo" placeholder="Cargo del Jefe"
                                           class="w-full px-2.5 py-1.5 rounded-lg border border-line text-xs">
                                    <div>
                                        <label class="block text-[10px] text-muted font-bold mb-0.5">Reemplazar Firma Digital (PNG)</label>
                                        <input type="file" name="firma_1_archivo" id="editar-firma-1-archivo" accept="image/png,image/jpeg,image/webp"
                                               class="w-full text-[10px]">
                                    </div>
                                </div>

                                <!-- Firma 2: Aval Institucional / Gerente General (Solo Administrador) -->
                                <div class="p-3 bg-slate-50/80 rounded-xl border border-line/70 space-y-2.5">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="font-heading font-extrabold text-[11px] text-brand-dark flex items-center gap-1.5">
                                            <i class="fa-solid fa-building-shield text-brand-blue text-xs"></i>
                                            <span>Firma 2: Aval Institucional (Gerencia)</span>
                                        </span>
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-extrabold uppercase tracking-wide bg-amber-50 text-amber-700 border border-amber-200">
                                            <i class="fa-solid fa-lock text-[8px]"></i>
                                            Solo Administrador
                                        </span>
                                    </div>

                                    <div class="p-2.5 bg-white rounded-lg border border-line/70 space-y-1.5 shadow-2xs">
                                        <div class="flex items-start justify-between gap-2">
                                            <div>
                                                <p id="editar-firma-2-nombre-label" class="font-heading font-bold text-xs text-brand-dark leading-tight">
                                                    Dra. Elena Ramos
                                                </p>
                                                <p id="editar-firma-2-cargo-label" class="text-[10px] text-muted">
                                                    Gerente General
                                                </p>
                                            </div>
                                            <div id="editar-firma-2-preview-box" class="h-8 w-20 bg-slate-50 rounded border border-line/60 p-0.5 flex items-center justify-center shrink-0">
                                                <img id="editar-firma-2-img" src="" alt="Firma Institucional" class="max-h-7 max-w-full object-contain hidden">
                                                <span id="editar-firma-2-placeholder" class="text-[9px] text-slate-400 italic">Rúbrica activa</span>
                                            </div>
                                        </div>
                                        <p class="text-[10px] text-slate-500 leading-normal flex items-center gap-1 pt-1 border-t border-slate-100">
                                            <i class="fa-solid fa-circle-info text-brand-blue text-[9px]"></i>
                                            Los datos de esta firma corresponden al aval de Gerencia y únicamente la Administración puede modificarlos.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 5. MAQUETA FONDO OPCIONAL -->
                        <div class="bg-white p-3.5 rounded-2xl border border-line/80 shadow-2xs space-y-1.5">
                            <label class="block font-heading font-bold text-brand-dark text-xs">Reemplazar Maqueta / Fondo (Opcional)</label>
                            <input type="file" name="archivo_plantilla" id="editar-archivo-plantilla" accept="image/png,image/jpeg,image/webp"
                                   class="w-full text-xs text-slate-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-brand-blue file:text-white cursor-pointer">
                        </div>

                    </div>

                    <!-- BOTONES INFERIORES -->
                    <div class="p-3.5 bg-white border-t border-line/80 flex items-center justify-end gap-2 shrink-0">
                        <button type="button" onclick="cerrarModalEditarPlantilla()"
                                class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-heading font-bold text-xs transition-colors cursor-pointer">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-brand-blue to-brand-deep hover:brightness-110 text-white font-heading font-bold text-xs shadow-md transition-all cursor-pointer flex items-center gap-1.5">
                            <i class="fa-solid fa-floppy-disk"></i>
                            <span>Guardar Cambios</span>
                        </button>
                    </div>
                </form>

                <!-- COLUMNA DERECHA: STUDIO CANVAS CON EL DIPLOMA OFICIAL (IDÉNTICO A EMPLEADO) -->
                <aside class="hidden lg:flex flex-1 bg-slate-200/90 p-4 sm:p-6 lg:p-8 overflow-y-auto overflow-x-auto items-center justify-center">
                    
                    <!-- DIPLOMA OFICIAL EN FORMATO HORIZONTAL ELEGANTE -->
                    <div id="preview-editar-diploma-wrapper" class="w-full max-w-4xl bg-white rounded-3xl border-8 border-slate-100 shadow-2xl relative overflow-hidden shrink-0 transition-all">
                        
                        <!-- BANNER DE OLAS EN LA PARTE SUPERIOR -->
                        <div class="relative bg-gradient-to-r from-brand-dark via-brand-deep to-[#0056b3] text-white p-5 sm:p-6 overflow-hidden">
                            <div class="relative z-10 flex flex-col sm:flex-row items-center justify-between gap-3 text-center sm:text-left">
                                <div class="flex items-center gap-3">
                                    <div class="bg-white/95 p-2 rounded-2xl border border-white/40 shadow-sm shrink-0">
                                        <img src="{{ asset('images/Logo.png') }}" alt="Logo PNY" class="h-10 sm:h-12 w-auto object-contain">
                                    </div>
                                    <div>
                                        <h2 id="preview-editar-organizacion" class="font-heading font-black text-base sm:text-lg text-white tracking-wide uppercase">
                                            C.I. PISCÍCOLA NEW YORK S.A.S.
                                        </h2>
                                        <p id="preview-editar-subtitulo" class="text-[11px] text-slate-200/90 font-medium">
                                            Sistema Institucional de Gestión del Talento Humano & Capacitación Continuada
                                        </p>
                                    </div>
                                </div>

                                <div class="text-right shrink-0">
                                    <span class="font-mono text-[10px] text-cyan-200 bg-white/10 px-3 py-1 rounded-full border border-white/20" id="preview-editar-codigo">
                                        PNY-{{ date('Y') }}-0001
                                    </span>
                                </div>
                            </div>

                            <!-- SVG Wave Motif Decorativo -->
                            <div class="absolute bottom-0 left-0 w-full overflow-hidden leading-none pointer-events-none opacity-25">
                                <svg class="relative block w-full h-5 text-white" viewBox="0 0 1200 40" preserveAspectRatio="none">
                                    <path d="M0,0 C150,35 350,10 500,25 C650,40 850,5 1000,20 C1100,30 1160,15 1200,25 L1200,40 L0,40 Z" fill="currentColor"/>
                                </svg>
                            </div>
                        </div>

                        <!-- CUERPO INTERNO DEL CERTIFICADO -->
                        <div class="p-6 sm:p-10 space-y-6 bg-gradient-to-b from-white via-slate-50/50 to-white relative">
                            
                            <!-- Marca de agua central suave -->
                            <div class="absolute inset-0 flex items-center justify-center pointer-events-none z-0">
                                <img src="{{ asset('images/Logo.png') }}" alt="Watermark" class="w-[450px] max-w-[85vw] h-auto object-contain select-none" style="opacity: 0.04; filter: grayscale(100%) opacity(0.5);">
                            </div>

                            <!-- Encabezado de Acreditación con Logo -->
                            <div class="text-center space-y-2.5 relative z-10">
                                <div class="flex items-center justify-center mb-1">
                                    <img src="{{ asset('images/Logo.png') }}" alt="Logo" class="h-14 sm:h-16 w-auto object-contain drop-shadow-sm">
                                </div>

                                <h1 id="preview-editar-titulo" class="font-heading font-black text-2xl sm:text-3xl text-brand-dark tracking-tight uppercase leading-tight">
                                    CERTIFICADO DE CAPACITACIÓN Y APROBACIÓN
                                </h1>

                                <p class="text-xs sm:text-sm text-slate-500 max-w-xl mx-auto font-medium">
                                    La Dirección de <strong id="preview-editar-organizacion-inline" class="text-brand-dark font-bold">C.I. Piscícola New York S.A.S.</strong> hace constar formalmente que:
                                </p>
                            </div>

                            <!-- Datos del Colaborador -->
                            <div class="text-center space-y-2 py-1 relative z-10">
                                <div class="inline-block relative">
                                    <h2 id="preview-editar-participante" class="font-heading font-black text-2xl sm:text-3xl text-brand-dark tracking-tight px-4 uppercase">
                                        Juan Pérez Gómez
                                    </h2>
                                    <div class="h-1 w-full bg-gradient-to-r from-transparent via-brand-blue to-transparent mt-1"></div>
                                </div>

                                <p class="text-xs sm:text-sm text-slate-600 font-semibold">
                                    Con documento de identificación N.º <strong id="preview-editar-documento" class="text-brand-dark font-extrabold">C.C. 1.117.811.618</strong>, adscrito al área de <strong id="preview-editar-area" class="text-brand-blue font-extrabold">{{ $area->nombre ?? 'Producción Piscícola' }}</strong>,
                                </p>
                            </div>

                            <!-- Detalles del Curso / Capacitación y Acreditación -->
                            <div class="max-w-2xl mx-auto bg-white p-5 sm:p-6 rounded-2xl border border-line/80 shadow-xs text-center space-y-3 relative z-10">
                                <span class="text-[11px] uppercase font-extrabold tracking-widest text-brand-sky block">
                                    HA CUMPLIDO SATISFACTORIAMENTE LA CAPACITACIÓN FORMATIVA EN:
                                </span>
                                
                                <h3 id="preview-editar-capacitacion" class="font-heading font-black text-lg sm:text-xl text-brand-dark leading-snug uppercase">
                                    Manejo Integral y Bioseguridad en Producción Piscícola
                                </h3>
                                
                                <p id="preview-editar-texto" class="text-xs sm:text-sm text-slate-600 font-medium leading-relaxed max-w-xl mx-auto font-sans">
                                    Por haber completado satisfactoriamente el programa de capacitación oficial con competencias laborales aprobadas.
                                </p>

                                <p class="text-xs text-slate-500 italic max-w-lg mx-auto pt-0.5">
                                    Se expide el presente certificado como constancia de su participación y cumplimiento de la actividad formativa.
                                </p>

                                <div class="pt-1 flex items-center justify-center text-xs">
                                    <span id="preview-editar-ciudad-fecha" class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full bg-slate-100 text-slate-700 font-bold border border-slate-200">
                                        <i class="fa-solid fa-location-dot text-brand-blue"></i> Rivera - Huila, {{ date('d/m/Y') }}
                                    </span>
                                </div>
                            </div>

                            <!-- FIRMAS DINÁMICAS (OCULTAS TEMPORALMENTE PARA IMPLEMENTACIÓN A FUTURO) -->
                            <div class="hidden" style="display: none;">
                                <div class="pt-6 border-t border-line/70 grid grid-cols-2 gap-6 text-center text-xs relative z-10">
                                    <!-- Firma 1: Jefe Inmediato -->
                                    <div class="space-y-1.5">
                                        <div class="h-14 flex items-end justify-center pb-1">
                                            <img id="preview-editar-firma-1-img" src="" alt="Firma Jefe" class="max-h-14 max-w-[170px] object-contain mx-auto hidden">
                                            <span id="preview-editar-firma-1-placeholder" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 text-brand-blue font-bold text-xs border border-blue-200/80">
                                                <i class="fa-solid fa-signature"></i>
                                                <span>Firma Verificada</span>
                                            </span>
                                        </div>
                                        <div class="w-56 mx-auto border-t-2 border-brand-dark/40 pt-1.5">
                                            <p id="preview-editar-firma-1-nombre" class="font-heading font-extrabold text-sm text-brand-dark truncate leading-tight">
                                                {{ $usuario->nombre_completo }}
                                            </p>
                                            <p id="preview-editar-firma-1-cargo" class="text-[11px] text-muted font-medium truncate">
                                                Jefe de Área {{ $area->nombre ?? '' }}
                                            </p>
                                            <span class="inline-block text-[10px] font-bold text-brand-blue uppercase mt-0.5 tracking-wider">Jefe de Área</span>
                                        </div>
                                    </div>

                                    <!-- Firma 2: Gerente General -->
                                    <div class="space-y-1.5">
                                        <div class="h-14 flex items-end justify-center pb-1">
                                            <img id="preview-editar-firma-2-img" src="" alt="Firma Gerencia" class="max-h-14 max-w-[170px] object-contain mx-auto hidden">
                                            <span id="preview-editar-firma-2-placeholder" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 text-brand-blue font-bold text-xs border border-blue-200/80">
                                                <i class="fa-solid fa-signature"></i>
                                                <span>Firma Verificada</span>
                                            </span>
                                        </div>
                                        <div class="w-56 mx-auto border-t-2 border-brand-dark/40 pt-1.5">
                                            <p id="preview-editar-firma-2-nombre" class="font-heading font-extrabold text-sm text-brand-dark truncate leading-tight">
                                                Dra. Elena Ramos
                                            </p>
                                            <p id="preview-editar-firma-2-cargo" class="text-[11px] text-muted font-medium truncate">
                                                Gerente General
                                            </p>
                                            <span class="inline-block text-[10px] font-bold text-brand-dark uppercase mt-0.5 tracking-wider">Gerencia General</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- Pie del diploma -->
                        <div class="bg-slate-900 text-slate-400 p-2.5 text-center text-[10px] border-t border-slate-800">
                            Documento Oficial Institucional emitido por el Sistema de Capacitación de C.I. Piscícola New York S.A.S.
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================
     MODAL 4: PREVISUALIZADOR OFICIAL DEL CERTIFICADO EN TIEMPO REAL
     ======================================================== -->
<div id="modal-visor-certificado-envivo" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div id="modal-visor-envivo-backdrop" class="fixed inset-0 bg-brand-dark/70 backdrop-blur-sm transition-opacity opacity-0" onclick="cerrarVisorCertificadoEnVivo()"></div>

    <div class="fixed inset-0 z-10 overflow-y-auto p-4 sm:p-6 flex items-center justify-center min-h-full">
        <div id="modal-visor-envivo-panel" class="relative bg-white rounded-3xl max-w-4xl w-full shadow-2xl border border-line/80 overflow-hidden transform transition-all scale-95 opacity-0">
            
            <!-- Barra superior del visor -->
            <div class="bg-slate-900 text-white px-6 py-3.5 flex items-center justify-between border-b border-slate-800">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-xl bg-white/10 text-amber-400 flex items-center justify-center text-sm font-bold">
                        <i class="fa-solid fa-award"></i>
                    </span>
                    <div>
                        <h4 class="font-heading font-extrabold text-xs sm:text-sm text-white">Previsualizador en Tiempo Real del Certificado</h4>
                        <p class="text-[10px] text-cyan-300 font-mono">Diseño Institucional con Firmas y Campos Oficiales</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <span class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-white/10 text-slate-200 text-xs font-medium border border-white/15">
                        <i class="fa-solid fa-eye text-cyan-300 text-[11px]"></i>
                        <span>Solo Vista Previa</span>
                    </span>
                    <button type="button" onclick="cerrarVisorCertificadoEnVivo()" class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors cursor-pointer" title="Cerrar">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>
            </div>

            <!-- CUERPO DEL DIPLOMA OFICIAL (IDÉNTICO AL GENERADO POR EL SISTEMA) -->
            <div class="p-4 sm:p-6 max-h-[82vh] overflow-y-auto bg-slate-100 flex items-center justify-center">
                <div id="visor-envivo-diploma-wrapper" class="w-full bg-white rounded-2xl border-4 border-slate-200 shadow-xl overflow-hidden relative">
                    
                    <!-- Banner superior con olas marinas y Logo oficial -->
                    <div class="relative bg-gradient-to-r from-brand-dark via-brand-deep to-[#0056b3] text-white p-5 sm:p-6 overflow-hidden">
                        <div class="relative z-10 flex flex-col sm:flex-row items-center justify-between gap-3 text-center sm:text-left">
                            <div class="flex items-center gap-3">
                                <div class="bg-white/95 p-1.5 rounded-xl border border-white/40 shadow-sm shrink-0">
                                    <img src="{{ asset('images/Logo.png') }}" alt="Logo PNY" class="h-10 w-auto object-contain">
                                </div>
                                <div>
                                    <h2 class="font-heading font-black text-base text-white uppercase tracking-wider">
                                        C.I. PISCÍCOLA <span class="text-cyan-300">NEW YORK</span> S.A.S.
                                    </h2>
                                    <p class="text-[10px] text-slate-200">Gestión de Talento Humano & Capacitación Continuada</p>
                                </div>
                            </div>

                            <div class="text-right">
                                <span class="font-mono text-[10px] text-cyan-200 bg-white/10 px-2.5 py-1 rounded-full border border-white/20" id="visor-envivo-codigo">
                                    PNY-{{ date('Y') }}-0001
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Contenido central del Diploma -->
                    <div class="p-6 sm:p-10 space-y-6 text-center bg-gradient-to-b from-white via-slate-50/50 to-white relative">
                        <!-- Fondo de plantilla personalizada si existe -->
                        <div id="visor-envivo-fondo-plantilla-box" class="absolute inset-0 flex items-center justify-center pointer-events-none z-0 opacity-20 overflow-hidden hidden">
                            <img id="visor-envivo-fondo-plantilla-img" src="" alt="Fondo de Plantilla" class="w-full h-full object-cover select-none">
                        </div>

                        <!-- Marca de agua central -->
                        <div id="visor-envivo-watermark" class="absolute inset-0 flex items-center justify-center pointer-events-none opacity-5">
                            <img src="{{ asset('images/Logo.png') }}" alt="Watermark" class="w-80 h-auto">
                        </div>

                        <div class="relative z-10 space-y-2">
                            <h3 class="font-heading font-black text-xl sm:text-2xl text-brand-dark uppercase tracking-tight">
                                CERTIFICADO DE CAPACITACIÓN Y APROBACIÓN
                            </h3>
                            <p class="text-xs text-slate-500">
                                La Dirección de <strong id="visor-envivo-organizacion" class="text-brand-dark">C.I. Piscícola New York S.A.S.</strong> hace constar formalmente que:
                            </p>
                        </div>

                        <!-- Participante de muestra -->
                        <div class="relative z-10 space-y-2 py-1">
                            <h2 class="font-heading font-black text-2xl sm:text-3xl text-brand-dark uppercase">
                                Juan Pérez Gómez
                            </h2>
                            <div class="h-0.5 w-48 bg-gradient-to-r from-transparent via-brand-blue to-transparent mx-auto"></div>
                            <p class="text-xs text-slate-600">
                                Con documento de identificación N.º <strong class="text-brand-dark font-extrabold">C.C. 1.117.811.618</strong>, adscrito al área de <strong id="visor-envivo-area" class="text-brand-blue font-extrabold">{{ $area->nombre ?? 'Producción Piscícola' }}</strong>,
                            </p>
                        </div>

                        <!-- Capacitación y Texto Oficial del Certificado en Tiempo Real -->
                        <div class="relative z-10 max-w-xl mx-auto bg-slate-50 p-5 rounded-2xl border border-line/80 space-y-3 shadow-2xs">
                            <span class="text-[10px] uppercase font-extrabold tracking-widest text-brand-sky block">
                                HA CUMPLIDO SATISFACTORIAMENTE LA CAPACITACIÓN FORMATIVA EN:
                            </span>
                            <h4 class="font-heading font-black text-base sm:text-lg text-brand-dark uppercase">
                                Manejo Integral y Bioseguridad en Producción Piscícola
                            </h4>
                            <p id="visor-envivo-texto" class="text-xs text-slate-700 leading-relaxed max-w-md mx-auto font-sans">
                                Por haber culminado y aprobado satisfactoriamente el plan formativo con calificación sobresaliente.
                            </p>
                            <p class="text-[11px] text-slate-500 italic">
                                Se expide el presente certificado como constancia de su participación y cumplimiento de la actividad formativa.
                            </p>
                            <div class="flex items-center justify-center gap-3 text-xs pt-1">
                                <span class="inline-flex items-center gap-1.5 text-slate-700 bg-white px-3.5 py-1 rounded-full border border-line font-bold shadow-2xs">
                                    <i class="fa-solid fa-location-dot text-brand-blue"></i> Rivera - Huila, {{ date('d M Y') }}
                                </span>
                            </div>
                        </div>

                        <!-- FIRMAS OFICIALES EN TIEMPO REAL (OCULTAS TEMPORALMENTE PARA IMPLEMENTACIÓN A FUTURO) -->
                        <div class="hidden" style="display: none;">
                            <div class="relative z-10 pt-4 border-t border-line/70 grid grid-cols-2 gap-4 text-center text-xs">
                                <!-- Firma 1: Jefe de Área -->
                                <div class="space-y-1">
                                    <div class="h-12 flex items-center justify-center">
                                        <img id="visor-envivo-firma-1-img" src="" alt="Firma Jefe" class="max-h-12 object-contain mx-auto hidden">
                                        <span id="visor-envivo-firma-1-placeholder" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 text-brand-blue font-heading font-bold text-xs border border-blue-200/80 select-none">
                                            <i class="fa-solid fa-signature text-[10px]"></i>
                                            <span>Firma Verificada</span>
                                        </span>
                                    </div>
                                    <div class="w-44 mx-auto border-t border-brand-dark/40 pt-1">
                                        <p id="visor-envivo-firma-1-nombre" class="font-heading font-bold text-xs text-brand-dark truncate leading-tight">
                                            {{ $usuario->nombre_completo }}
                                        </p>
                                        <p id="visor-envivo-firma-1-cargo" class="text-[10px] text-muted truncate">
                                            Jefe de Área {{ $area->nombre ?? '' }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Firma 2: Gerente General -->
                                <div class="space-y-1">
                                    <div class="h-12 flex items-center justify-center">
                                        <img id="visor-envivo-firma-2-img" src="" alt="Firma Gerencia" class="max-h-12 object-contain mx-auto hidden">
                                        <span id="visor-envivo-firma-2-placeholder" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 text-brand-blue font-heading font-bold text-xs border border-blue-200/80 select-none">
                                            <i class="fa-solid fa-signature text-[10px]"></i>
                                            <span>Firma Verificada</span>
                                        </span>
                                    </div>
                                    <div class="w-44 mx-auto border-t border-brand-dark/40 pt-1">
                                        <p id="visor-envivo-firma-2-nombre" class="font-heading font-bold text-xs text-brand-dark truncate leading-tight">
                                            Dra. Elena Ramos
                                        </p>
                                        <p id="visor-envivo-firma-2-cargo" class="text-[10px] text-muted truncate">
                                            Gerente General
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Pie del diploma -->
                    <div class="bg-slate-900 text-slate-400 p-2.5 text-center text-[10px] border-t border-slate-800">
                        Documento Oficial Institucional emitido por el Sistema de Capacitación de C.I. Piscícola New York S.A.S.
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
    // ========================================================
    // 1. SISTEMA DE DESGLOSE / ACORDEÓN DE PLANTILLAS
    // ========================================================
    function toggleDesglosePlantilla(id) {
        const detalle = document.getElementById(`detalle-plantilla-${id}`);
        const icono = document.getElementById(`icono-chevron-${id}`);
        const texto = document.getElementById(`texto-chevron-${id}`);
        const cabecera = document.getElementById(`cabecera-plantilla-${id}`);

        if (!detalle) return;

        const estaOculto = detalle.classList.contains('hidden');

        if (estaOculto) {
            detalle.classList.remove('hidden');
            if (icono) icono.classList.add('rotate-180');
            if (texto) texto.textContent = 'Ocultar';
            if (cabecera) cabecera.setAttribute('aria-expanded', 'true');
        } else {
            detalle.classList.add('hidden');
            if (icono) icono.classList.remove('rotate-180');
            if (texto) texto.textContent = 'Desglosar';
            if (cabecera) cabecera.setAttribute('aria-expanded', 'false');
        }
    }

    // ========================================================
    // 2. PREVISUALIZADOR EN TIEMPO REAL DEL CERTIFICADO
    // ========================================================
    let visorEnvivoModoActual = null;

    function previsualizarCertificadoEnVivo(datos) {
        // Datos organizacionales y textos oficiales
        const elOrg = document.getElementById('visor-envivo-organizacion');
        const elTxt = document.getElementById('visor-envivo-texto');
        const elCod = document.getElementById('visor-envivo-codigo');
        const elArea = document.getElementById('visor-envivo-area');

        if (elOrg) elOrg.textContent = datos.organizacion || 'C.I. Piscícola New York S.A.S.';
        if (elTxt) elTxt.textContent = datos.texto || 'Por haber culminado y aprobado satisfactoriamente el plan formativo institucional con competencias laborales acreditadas.';
        if (elCod) elCod.textContent = datos.codigo || 'PNY-{{ date("Y") }}-0001';
        if (elArea) elArea.textContent = datos.areaNombre || '{{ $area->nombre ?? "Producción Piscícola" }}';

        // Firma 1: Jefe de Área
        const elF1Nom = document.getElementById('visor-envivo-firma-1-nombre');
        const elF1Car = document.getElementById('visor-envivo-firma-1-cargo');
        const elF1Img = document.getElementById('visor-envivo-firma-1-img');
        const elF1Plh = document.getElementById('visor-envivo-firma-1-placeholder');

        if (elF1Nom) elF1Nom.textContent = datos.jefeNombre || '{{ $usuario->nombre_completo }}';
        if (elF1Car) elF1Car.textContent = datos.jefeCargo || 'Jefe de Área {{ $area->nombre ?? "" }}';

        if (datos.jefeFirmaUrl) {
            elF1Img.src = datos.jefeFirmaUrl;
            elF1Img.classList.remove('hidden');
            if (elF1Plh) elF1Plh.classList.add('hidden');
        } else {
            elF1Img.classList.add('hidden');
            elF1Img.src = '';
            if (elF1Plh) elF1Plh.classList.remove('hidden');
        }

        // Firma 2: Gerente General
        const elF2Nom = document.getElementById('visor-envivo-firma-2-nombre');
        const elF2Car = document.getElementById('visor-envivo-firma-2-cargo');
        const elF2Img = document.getElementById('visor-envivo-firma-2-img');
        const elF2Plh = document.getElementById('visor-envivo-firma-2-placeholder');

        if (elF2Nom) elF2Nom.textContent = datos.gerenteNombre || 'Dra. Elena Ramos';
        if (elF2Car) elF2Car.textContent = datos.gerenteCargo || 'Gerente General';

        if (datos.gerenteFirmaUrl) {
            elF2Img.src = datos.gerenteFirmaUrl;
            elF2Img.classList.remove('hidden');
            if (elF2Plh) elF2Plh.classList.add('hidden');
        } else {
            elF2Img.classList.add('hidden');
            elF2Img.src = '';
            if (elF2Plh) elF2Plh.classList.remove('hidden');
        }

        // Fondo / Maqueta personalizada de la plantilla
        const elFondoBox = document.getElementById('visor-envivo-fondo-plantilla-box');
        const elFondoImg = document.getElementById('visor-envivo-fondo-plantilla-img');
        const elWatermark = document.getElementById('visor-envivo-watermark');
        const elWrapVisor = document.getElementById('visor-envivo-diploma-wrapper');

        if (datos.plantillaUrl) {
            if (elFondoImg && elFondoBox) {
                elFondoImg.src = datos.plantillaUrl;
                elFondoBox.classList.remove('hidden');
            }
            if (elWatermark) elWatermark.classList.add('hidden');
            if (elWrapVisor) {
                elWrapVisor.style.backgroundImage = `url('${datos.plantillaUrl}')`;
                elWrapVisor.style.backgroundSize = 'cover';
                elWrapVisor.style.backgroundPosition = 'center';
            }
        } else {
            if (elFondoBox) elFondoBox.classList.add('hidden');
            if (elWatermark) elWatermark.classList.remove('hidden');
            if (elWrapVisor) {
                elWrapVisor.style.backgroundImage = '';
            }
        }

        // Mostrar modal con animación fluida
        const m = document.getElementById('modal-visor-certificado-envivo');
        const b = document.getElementById('modal-visor-envivo-backdrop');
        const p = document.getElementById('modal-visor-envivo-panel');

        m.classList.remove('hidden');
        setTimeout(() => {
            b.classList.remove('opacity-0');
            p.classList.remove('scale-95', 'opacity-0');
        }, 10);
    }

    function cerrarVisorCertificadoEnVivo() {
        visorEnvivoModoActual = null;
        const m = document.getElementById('modal-visor-certificado-envivo');
        const b = document.getElementById('modal-visor-envivo-backdrop');
        const p = document.getElementById('modal-visor-envivo-panel');

        b.classList.add('opacity-0');
        p.classList.add('scale-95', 'opacity-0');
        setTimeout(() => m.classList.add('hidden'), 200);
    }

    // ========================================================
    // 3. ACTUALIZACIÓN EN TIEMPO REAL AL ESCRIBIR EN FORMULARIOS
    // ========================================================
    function actualizarPreviewModal(prefijo, datos) {
        if (!datos) return;
        const elOrg = document.getElementById(`preview-${prefijo}-organizacion`);
        const elOrgInline = document.getElementById(`preview-${prefijo}-organizacion-inline`);
        const elSub = document.getElementById(`preview-${prefijo}-subtitulo`);
        const elTit = document.getElementById(`preview-${prefijo}-titulo`);
        const elTxt = document.getElementById(`preview-${prefijo}-texto`);
        const elCod = document.getElementById(`preview-${prefijo}-codigo`);
        const elCap = document.getElementById(`preview-${prefijo}-capacitacion`);
        const elCiu = document.getElementById(`preview-${prefijo}-ciudad-fecha`);
        const elPar = document.getElementById(`preview-${prefijo}-participante`);
        const elDoc = document.getElementById(`preview-${prefijo}-documento`);
        const elF1Nom = document.getElementById(`preview-${prefijo}-firma-1-nombre`);
        const elF1Car = document.getElementById(`preview-${prefijo}-firma-1-cargo`);
        const elF1Img = document.getElementById(`preview-${prefijo}-firma-1-img`);
        const elF1Plh = document.getElementById(`preview-${prefijo}-firma-1-placeholder`);
        const elF2Nom = document.getElementById(`preview-${prefijo}-firma-2-nombre`);
        const elF2Car = document.getElementById(`preview-${prefijo}-firma-2-cargo`);
        const elF2Img = document.getElementById(`preview-${prefijo}-firma-2-img`);
        const elF2Plh = document.getElementById(`preview-${prefijo}-firma-2-placeholder`);

        if (elOrg && datos.organizacion !== undefined) elOrg.textContent = datos.organizacion;
        if (elOrgInline && datos.organizacion !== undefined) elOrgInline.textContent = datos.organizacion;
        if (elSub && datos.subtitulo !== undefined) elSub.textContent = datos.subtitulo;
        if (elTit && datos.titulo !== undefined) elTit.textContent = datos.titulo;
        if (elTxt && datos.texto !== undefined) elTxt.textContent = datos.texto;
        if (elCod && datos.codigo !== undefined) elCod.textContent = datos.codigo;
        if (elCap && datos.capacitacion !== undefined) elCap.textContent = datos.capacitacion;
        if (elCiu && datos.ciudadFecha !== undefined) elCiu.innerHTML = `<i class="fa-solid fa-location-dot text-brand-blue"></i> ${datos.ciudadFecha}`;
        if (elPar && datos.participante !== undefined) elPar.textContent = datos.participante;
        if (elDoc && datos.documento !== undefined) elDoc.textContent = datos.documento;
        if (elF1Nom && datos.jefeNombre !== undefined) elF1Nom.textContent = datos.jefeNombre;
        if (elF1Car && datos.jefeCargo !== undefined) elF1Car.textContent = datos.jefeCargo;
        if (elF2Nom && datos.gerenteNombre !== undefined) elF2Nom.textContent = datos.gerenteNombre;
        if (elF2Car && datos.gerenteCargo !== undefined) elF2Car.textContent = datos.gerenteCargo;

        if (elF1Img && elF1Plh) {
            if (datos.jefeFirmaUrl) {
                elF1Img.src = datos.jefeFirmaUrl;
                elF1Img.classList.remove('hidden');
                elF1Plh.classList.add('hidden');
            } else {
                elF1Img.classList.add('hidden');
                elF1Img.src = '';
                elF1Plh.classList.remove('hidden');
            }
        }

        if (elF2Img && elF2Plh) {
            if (datos.gerenteFirmaUrl) {
                elF2Img.src = datos.gerenteFirmaUrl;
                elF2Img.classList.remove('hidden');
                elF2Plh.classList.add('hidden');
            } else {
                elF2Img.classList.add('hidden');
                elF2Img.src = '';
                elF2Plh.classList.remove('hidden');
            }
        }

        const wrapDiploma = document.getElementById(`preview-${prefijo}-diploma-wrapper`);
        if (wrapDiploma && datos.plantillaUrl !== undefined) {
            if (datos.plantillaUrl) {
                wrapDiploma.style.backgroundImage = `url('${datos.plantillaUrl}')`;
                wrapDiploma.style.backgroundSize = 'cover';
                wrapDiploma.style.backgroundPosition = 'center';
            } else {
                wrapDiploma.style.backgroundImage = '';
            }
        }
    }

    function vincularEscuchaEnTiempoReal(prefijo) {
        const inpOrg = document.getElementById(`${prefijo}-organizacion`);
        const inpSub = document.getElementById(`${prefijo}-subtitulo`);
        const inpTit = document.getElementById(`${prefijo}-titulo`);
        const inpTxt = document.getElementById(`${prefijo}-texto`);
        const inpCap = document.getElementById(`${prefijo}-capacitacion`);
        const inpCiu = document.getElementById(`${prefijo}-ciudad-fecha`);
        const inpPar = document.getElementById(`${prefijo}-participante`);
        const inpDoc = document.getElementById(`${prefijo}-documento`);
        const inpF1Nom = document.getElementById(`${prefijo}-firma-1-nombre`);
        const inpF1Car = document.getElementById(`${prefijo}-firma-1-cargo`);
        const fileF1 = document.getElementById(`${prefijo}-firma-1-archivo`);
        const inpF2Nom = document.getElementById(`${prefijo}-firma-2-nombre`);
        const inpF2Car = document.getElementById(`${prefijo}-firma-2-cargo`);
        const fileF2 = document.getElementById(`${prefijo}-firma-2-archivo`);
        const filePlantilla = document.getElementById(`${prefijo}-archivo-plantilla`);

        if (inpOrg) {
            inpOrg.addEventListener('input', function() {
                const val = this.value || 'C.I. Piscícola New York S.A.S.';
                const el1 = document.getElementById(`preview-${prefijo}-organizacion`);
                const el2 = document.getElementById(`preview-${prefijo}-organizacion-inline`);
                if (el1) el1.textContent = val;
                if (el2) el2.textContent = val;
            });
        }

        if (inpSub) {
            inpSub.addEventListener('input', function() {
                const el = document.getElementById(`preview-${prefijo}-subtitulo`);
                if (el) el.textContent = this.value || 'Gestión de Talento Humano & Capacitación Continuada';
            });
        }

        if (inpTit) {
            inpTit.addEventListener('input', function() {
                const el = document.getElementById(`preview-${prefijo}-titulo`);
                if (el) el.textContent = this.value || 'CERTIFICADO DE CAPACITACIÓN Y APROBACIÓN';
            });
        }

        if (inpTxt) {
            inpTxt.addEventListener('input', function() {
                const el = document.getElementById(`preview-${prefijo}-texto`);
                if (el) el.textContent = this.value || 'Por haber completado satisfactoriamente el programa de capacitación oficial.';
            });
        }

        if (inpCap) {
            inpCap.addEventListener('input', function() {
                const el = document.getElementById(`preview-${prefijo}-capacitacion`);
                if (el) el.textContent = this.value || 'Manejo Integral y Bioseguridad en Producción Piscícola';
            });
        }

        if (inpCiu) {
            inpCiu.addEventListener('input', function() {
                const el = document.getElementById(`preview-${prefijo}-ciudad-fecha`);
                if (el) el.innerHTML = `<i class="fa-solid fa-location-dot text-brand-blue"></i> ${this.value || 'Rivera - Huila'}`;
            });
        }

        if (inpPar) {
            inpPar.addEventListener('input', function() {
                const el = document.getElementById(`preview-${prefijo}-participante`);
                if (el) el.textContent = this.value || 'Juan Pérez Gómez';
            });
        }

        if (inpDoc) {
            inpDoc.addEventListener('input', function() {
                const el = document.getElementById(`preview-${prefijo}-documento`);
                if (el) el.textContent = this.value || 'C.C. 1.117.811.618';
            });
        }

        if (inpF1Nom) {
            inpF1Nom.addEventListener('input', function() {
                const el = document.getElementById(`preview-${prefijo}-firma-1-nombre`);
                if (el) el.textContent = this.value || 'Jefe Inmediato';
            });
        }

        if (inpF1Car) {
            inpF1Car.addEventListener('input', function() {
                const el = document.getElementById(`preview-${prefijo}-firma-1-cargo`);
                if (el) el.textContent = this.value || 'Jefe de Área';
            });
        }

        if (fileF1) {
            fileF1.addEventListener('change', function() {
                const f = this.files[0];
                const img = document.getElementById(`preview-${prefijo}-firma-1-img`);
                const plh = document.getElementById(`preview-${prefijo}-firma-1-placeholder`);
                if (f && img) {
                    img.src = URL.createObjectURL(f);
                    img.classList.remove('hidden');
                    if (plh) plh.classList.add('hidden');
                }
            });
        }

        if (inpF2Nom) {
            inpF2Nom.addEventListener('input', function() {
                const el = document.getElementById(`preview-${prefijo}-firma-2-nombre`);
                if (el) el.textContent = this.value || 'Gerente General';
            });
        }

        if (inpF2Car) {
            inpF2Car.addEventListener('input', function() {
                const el = document.getElementById(`preview-${prefijo}-firma-2-cargo`);
                if (el) el.textContent = this.value || 'Gerente General';
            });
        }

        if (fileF2) {
            fileF2.addEventListener('change', function() {
                const f = this.files[0];
                const img = document.getElementById(`preview-${prefijo}-firma-2-img`);
                const plh = document.getElementById(`preview-${prefijo}-firma-2-placeholder`);
                if (f && img) {
                    img.src = URL.createObjectURL(f);
                    img.classList.remove('hidden');
                    if (plh) plh.classList.add('hidden');
                }
            });
        }

        if (filePlantilla) {
            filePlantilla.addEventListener('change', function() {
                const f = this.files[0];
                const wrapper = document.getElementById(`preview-${prefijo}-diploma-wrapper`);
                if (f && wrapper) {
                    const url = URL.createObjectURL(f);
                    wrapper.style.backgroundImage = `url('${url}')`;
                    wrapper.style.backgroundSize = 'cover';
                    wrapper.style.backgroundPosition = 'center';
                }
            });
        }
    }

    // ========================================================
    // 4. FILTROS Y PESTAÑAS
    // ========================================================
    function filtrarTab(tipo) {
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('bg-brand-blue', 'text-white', 'shadow-2xs');
            btn.classList.add('text-slate-600');
        });

        const botonActivo = document.getElementById(`tab-btn-${tipo}`);
        if (botonActivo) {
            botonActivo.classList.add('bg-brand-blue', 'text-white', 'shadow-2xs');
            botonActivo.classList.remove('text-slate-600');
        }

        const cards = document.querySelectorAll('.plantilla-card');
        cards.forEach(card => {
            if (tipo === 'todas') {
                card.style.display = 'block';
            } else if (tipo === 'admin') {
                card.style.display = card.classList.contains('tipo-admin') ? 'block' : 'none';
            } else if (tipo === 'propias') {
                card.style.display = card.classList.contains('tipo-propia') ? 'block' : 'none';
            }
        });
    }

    // ========================================================
    // 5. MODALES DE CREACIÓN, EDICIÓN Y FIRMA
    // ========================================================
    function abrirModalCrearPlantilla() {
        actualizarPreviewModal('crear', {
            organizacion: document.getElementById('crear-organizacion')?.value || 'C.I. Piscícola New York S.A.S.',
            subtitulo: document.getElementById('crear-subtitulo')?.value || 'Gestión de Talento Humano & Capacitación Continuada',
            titulo: document.getElementById('crear-titulo')?.value || 'CERTIFICADO DE CAPACITACIÓN Y APROBACIÓN',
            texto: document.getElementById('crear-texto')?.value || 'Por haber completado satisfactoriamente el programa de capacitación oficial con calificación destacada.',
            capacitacion: document.getElementById('crear-capacitacion')?.value || 'Manejo Integral y Bioseguridad en Producción Piscícola',
            ciudadFecha: document.getElementById('crear-ciudad-fecha')?.value || 'Rivera - Huila, {{ date("d M Y") }}',
            participante: document.getElementById('crear-participante')?.value || 'Juan Pérez Gómez',
            documento: document.getElementById('crear-documento')?.value || 'C.C. 1.117.811.618',
            jefeNombre: document.getElementById('crear-firma-1-nombre')?.value || '{{ $usuario->nombre_completo }}',
            jefeCargo: document.getElementById('crear-firma-1-cargo')?.value || '{{ $usuario->cargoOficial() }}',
            jefeFirmaUrl: '{{ $usuario->firmaUrl() ?? "" }}',
            gerenteNombre: '{{ addslashes($firma2Oficial["nombre"]) }}',
            gerenteCargo: '{{ addslashes($firma2Oficial["cargo"]) }}',
            gerenteFirmaUrl: '{{ $firma2Oficial["url"] ?? "" }}',
            codigo: 'PNY-{{ date("Y") }}-NUEVA'
        });

        const m = document.getElementById('modal-crear-plantilla');
        const b = document.getElementById('modal-crear-backdrop');
        const p = document.getElementById('modal-crear-panel');
        m.classList.remove('hidden');
        setTimeout(() => {
            b.classList.remove('opacity-0');
            p.classList.remove('scale-95', 'opacity-0');
        }, 10);
    }

    function cerrarModalCrearPlantilla() {
        const m = document.getElementById('modal-crear-plantilla');
        const b = document.getElementById('modal-crear-backdrop');
        const p = document.getElementById('modal-crear-panel');
        b.classList.add('opacity-0');
        p.classList.add('scale-95', 'opacity-0');
        setTimeout(() => m.classList.add('hidden'), 200);
    }

    // Modal Firma Jefe
    let canvas, ctx, dibujando = false;

    function abrirModalFirmaJefe(id, nombre, firmaNombre, firmaCargo, firmaImgUrl, actionUrl) {
        document.getElementById('form-firma-jefe').action = actionUrl;
        document.getElementById('modal-firma-subtitulo').textContent = `Plantilla: ${nombre}`;
        document.getElementById('input-firma-nombre').value = firmaNombre;
        document.getElementById('input-firma-cargo').value = firmaCargo;

        const previewBox = document.getElementById('preview-firma-actual-box');
        const previewImg = document.getElementById('preview-firma-actual-img');
        if (firmaImgUrl) {
            previewImg.src = firmaImgUrl;
            previewBox.classList.remove('hidden');
        } else {
            previewBox.classList.add('hidden');
        }

        cambiarModoFirma('archivo');
        limpiarCanvasFirma();

        const m = document.getElementById('modal-firma-jefe');
        const b = document.getElementById('modal-firma-backdrop');
        const p = document.getElementById('modal-firma-panel');
        m.classList.remove('hidden');
        setTimeout(() => {
            b.classList.remove('opacity-0');
            p.classList.remove('scale-95', 'opacity-0');
            initCanvas();
        }, 10);
    }

    function cerrarModalFirmaJefe() {
        const m = document.getElementById('modal-firma-jefe');
        const b = document.getElementById('modal-firma-backdrop');
        const p = document.getElementById('modal-firma-panel');
        b.classList.add('opacity-0');
        p.classList.add('scale-95', 'opacity-0');
        setTimeout(() => m.classList.add('hidden'), 200);
    }

    function initCanvas() {
        canvas = document.getElementById('canvas-firma');
        if (!canvas) return;
        ctx = canvas.getContext('2d');
        ctx.strokeStyle = '#0f172a';
        ctx.lineWidth = 2.5;
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';

        function getPos(e) {
            const rect = canvas.getBoundingClientRect();
            const clientX = e.touches ? e.touches[0].clientX : e.clientX;
            const clientY = e.touches ? e.touches[0].clientY : e.clientY;
            return {
                x: (clientX - rect.left) * (canvas.width / rect.width),
                y: (clientY - rect.top) * (canvas.height / rect.height)
            };
        }

        function start(e) {
            dibujando = true;
            const pos = getPos(e);
            ctx.beginPath();
            ctx.moveTo(pos.x, pos.y);
            e.preventDefault();
        }

        function move(e) {
            if (!dibujando) return;
            const pos = getPos(e);
            ctx.lineTo(pos.x, pos.y);
            ctx.stroke();
            e.preventDefault();
        }

        function stop() {
            if (dibujando) {
                dibujando = false;
                document.getElementById('input-firma-canvas').value = canvas.toDataURL('image/png');
            }
        }

        canvas.onmousedown = start;
        canvas.onmousemove = move;
        window.onmouseup = stop;

        canvas.ontouchstart = start;
        canvas.ontouchmove = move;
        window.ontouchend = stop;
    }

    function limpiarCanvasFirma() {
        if (canvas && ctx) {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            document.getElementById('input-firma-canvas').value = '';
        }
    }

    function cambiarModoFirma(modo) {
        const bArchivo = document.getElementById('bloque-firma-archivo');
        const bDibujar = document.getElementById('bloque-firma-dibujar');
        const btnArchivo = document.getElementById('btn-modo-archivo');
        const btnDibujar = document.getElementById('btn-modo-dibujar');

        if (modo === 'archivo') {
            bArchivo.classList.remove('hidden');
            bDibujar.classList.add('hidden');
            btnArchivo.classList.add('bg-white', 'text-brand-blue', 'shadow-2xs');
            btnArchivo.classList.remove('text-slate-600');
            btnDibujar.classList.remove('bg-white', 'text-brand-blue', 'shadow-2xs');
            btnDibujar.classList.add('text-slate-600');
            document.getElementById('input-firma-canvas').value = '';
        } else {
            bArchivo.classList.add('hidden');
            bDibujar.classList.remove('hidden');
            btnDibujar.classList.add('bg-white', 'text-brand-blue', 'shadow-2xs');
            btnDibujar.classList.remove('text-slate-600');
            btnArchivo.classList.remove('bg-white', 'text-brand-blue', 'shadow-2xs');
            btnArchivo.classList.add('text-slate-600');
            setTimeout(initCanvas, 50);
        }
    }

    // Modal Editar Propia
    document.querySelectorAll('.btn-editar-propia').forEach(btn => {
        btn.addEventListener('click', function() {
            const form = document.getElementById('form-editar-plantilla');
            form.action = this.dataset.action;
            document.getElementById('editar-nombre').value = this.dataset.nombre || '';
            document.getElementById('editar-descripcion').value = this.dataset.descripcion || '';
            document.getElementById('editar-organizacion').value = this.dataset.organizacion || 'C.I. Piscícola New York S.A.S.';
            document.getElementById('editar-texto').value = this.dataset.texto || '';
            document.getElementById('editar-firma-1-nombre').value = this.dataset.jefeNombre || '';
            document.getElementById('editar-firma-1-cargo').value = this.dataset.jefeCargo || '';

            const editF2NomLabel = document.getElementById('editar-firma-2-nombre-label');
            const editF2CarLabel = document.getElementById('editar-firma-2-cargo-label');
            const editF2Img = document.getElementById('editar-firma-2-img');
            const editF2Plh = document.getElementById('editar-firma-2-placeholder');

            if (editF2NomLabel) editF2NomLabel.textContent = this.dataset.gerenteNombre || 'Dra. Elena Ramos';
            if (editF2CarLabel) editF2CarLabel.textContent = this.dataset.gerenteCargo || 'Gerente General';
            if (editF2Img && editF2Plh) {
                if (this.dataset.gerenteFirmaUrl) {
                    editF2Img.src = this.dataset.gerenteFirmaUrl;
                    editF2Img.classList.remove('hidden');
                    editF2Plh.classList.add('hidden');
                } else {
                    editF2Img.classList.add('hidden');
                    editF2Plh.classList.remove('hidden');
                }
            }

            actualizarPreviewModal('editar', {
                organizacion: this.dataset.organizacion || 'C.I. Piscícola New York S.A.S.',
                subtitulo: 'Gestión de Talento Humano & Capacitación Continuada',
                titulo: 'CERTIFICADO DE CAPACITACIÓN Y APROBACIÓN',
                texto: this.dataset.texto || 'Por haber completado satisfactoriamente el programa de capacitación oficial con calificación destacada.',
                capacitacion: 'Manejo Integral y Bioseguridad en Producción Piscícola',
                ciudadFecha: 'Rivera - Huila, {{ date("d M Y") }}',
                participante: 'Juan Pérez Gómez',
                documento: 'C.C. 1.117.811.618',
                jefeNombre: this.dataset.jefeNombre || '{{ $usuario->nombre_completo }}',
                jefeCargo: this.dataset.jefeCargo || 'Jefe de Área {{ $area->nombre ?? "" }}',
                jefeFirmaUrl: this.dataset.jefeFirmaUrl || '',
                gerenteNombre: this.dataset.gerenteNombre || 'Dra. Elena Ramos',
                gerenteCargo: this.dataset.gerenteCargo || 'Gerente General',
                gerenteFirmaUrl: this.dataset.gerenteFirmaUrl || '',
                plantillaUrl: this.dataset.plantillaUrl || '',
                codigo: 'PNY-{{ date("Y") }}-' + String(this.dataset.id || '1').padStart(4, '0')
            });

            const m = document.getElementById('modal-editar-plantilla');
            const b = document.getElementById('modal-editar-backdrop');
            const p = document.getElementById('modal-editar-panel');
            m.classList.remove('hidden');
            setTimeout(() => {
                b.classList.remove('opacity-0');
                p.classList.remove('scale-95', 'opacity-0');
            }, 10);
        });
    });

    function cerrarModalEditarPlantilla() {
        const m = document.getElementById('modal-editar-plantilla');
        const b = document.getElementById('modal-editar-backdrop');
        const p = document.getElementById('modal-editar-panel');
        b.classList.add('opacity-0');
        p.classList.add('scale-95', 'opacity-0');
        setTimeout(() => m.classList.add('hidden'), 200);
    }

    // ========================================================
    // 6. INICIALIZACIÓN Y AUTO-APERTURAS
    // ========================================================
    document.addEventListener('DOMContentLoaded', function() {
        // Enlazar listeners en tiempo real para modales
        vincularEscuchaEnTiempoReal('crear');
        vincularEscuchaEnTiempoReal('editar');

        // Si solo hay una plantilla en la lista, desglosarla automáticamente para conveniencia del usuario
        const cards = document.querySelectorAll('.plantilla-card');
        if (cards.length === 1) {
            const soloId = cards[0].getAttribute('data-id');
            if (soloId) toggleDesglosePlantilla(soloId);
        }

        // Auto-apertura si viene del dropdown de notificaciones (?firmar=ID)
        const urlParams = new URLSearchParams(window.location.search);
        const firmarId = urlParams.get('firmar');
        if (firmarId) {
            // Asegurar que se desglose la tarjeta correspondiente
            toggleDesglosePlantilla(firmarId);
            const card = document.querySelector(`.plantilla-card[data-id="${firmarId}"]`) || document.querySelector(`.plantilla-card`);
            const btnFirmar = card ? card.querySelector('button[onclick*="abrirModalFirmaJefe"]') : null;
            if (btnFirmar) {
                btnFirmar.click();
            }
        }
    });
</script>
@endpush
@endsection
