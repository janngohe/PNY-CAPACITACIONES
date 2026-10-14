@extends('layouts.admin')

@section('title', 'Certificado ' . $certificado->codigo . ' · C.I. Piscícola New York')
@section('page_title', 'Visualización de Certificado')

@section('content')
@php
    $plantilla = $certificado->plantillaCertificado ?? \App\Models\PlantillaCertificado::where('estado', true)->first();
    $f1n = $plantilla->firma_1_nombre ?? 'Ing. Carlos Mendoza';
    $f1c = $plantilla->firma_1_cargo ?? 'Jefe de Área / Operaciones';
    $f1r = $plantilla->firma_1_ruta ?? null;

    // Si la plantilla no tiene firma estampada, buscar la firma registrada del Jefe de Área del colaborador
    if (empty($f1r) && $certificado->usuario?->area_id) {
        $jefeArea = \App\Models\Usuario::where('area_id', $certificado->usuario->area_id)
            ->where('rol', 'JEFE_AREA')
            ->whereNotNull('firma_digital')
            ->first();
        if ($jefeArea && $jefeArea->tieneFirmaRegistrada()) {
            $f1n = $jefeArea->nombre_completo;
            $f1c = $jefeArea->cargoOficial();
            $f1r = $jefeArea->firma_digital;
        }
    }

    $f2n = $plantilla->firma_2_nombre ?? 'Dra. Elena Ramos';
    $f2c = $plantilla->firma_2_cargo ?? 'Gerente General';
    $f2r = $plantilla->firma_2_ruta ?? null;

    $org = $plantilla->nombre_organizacion ?? 'C.I. Piscícola New York S.A.S.';
    $textoCert = $plantilla->texto_certificado ?? ('La Dirección General de ' . $org . ' hace constar formalmente que:');
    $rutaPlantilla = $plantilla->ruta_plantilla ?? null;
    $duracionHoras = $certificado->capacitacion?->duracion_estimada ?? 4;
    $fechaRealizada = $certificado->fecha_emision ? $certificado->fecha_emision->format('d/m/Y') : date('d/m/Y');
@endphp

<div class="space-y-6 max-w-5xl mx-auto print:max-w-none print:p-0">

    <!-- BARRA SUPERIOR DE ACCIONES -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-4 sm:p-5 rounded-3xl border border-line/80 shadow-2xs print:hidden">
        <a href="{{ route('admin.certificados.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-brand-blue hover:text-brand-deep transition-colors">
            <i class="fa-solid fa-arrow-left text-xs"></i>
            <span>Volver a Gestión de Certificados</span>
        </a>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('admin.certificados.pdf', $certificado) }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-brand-blue to-brand-deep hover:brightness-110 text-white font-heading font-extrabold text-xs shadow-xs hover:-translate-y-0.5 transition-all cursor-pointer">
                <i class="fa-solid fa-file-pdf text-sm"></i>
                <span>Descargar PDF</span>
            </a>
        </div>
    </div>

    <!-- CONTENEDOR PRINCIPAL DEL DIPLOMA OFICIAL -->
    <div id="certificado-documento" class="bg-white rounded-3xl border-8 border-slate-100 shadow-2xl relative overflow-hidden print:border-0 print:shadow-none print:p-0">
        
        <!-- BANNER DE OLAS EN LA PARTE SUPERIOR -->
        <div class="relative bg-gradient-to-r from-brand-dark via-brand-deep to-[#0056b3] text-white p-6 sm:p-8 overflow-hidden">
            <div class="absolute -right-10 -top-10 w-64 h-64 rounded-full bg-brand-sky/20 blur-2xl pointer-events-none"></div>
            <div class="absolute left-1/3 -bottom-16 w-80 h-80 rounded-full bg-brand-blue/30 blur-3xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col sm:flex-row items-center justify-between gap-4 text-center sm:text-left">
                <div class="flex items-center gap-4">
                    <div class="bg-white/95 p-2 rounded-2xl border border-white/40 shadow-md shrink-0">
                        <img src="{{ asset('images/Logo.png') }}" 
                             alt="Logo C.I. Piscícola New York" 
                             class="h-11 sm:h-13 w-auto object-contain">
                    </div>
                    <div>
                        <h2 class="font-heading font-black text-lg sm:text-xl text-white tracking-wide uppercase">
                            {{ $org }}
                        </h2>
                        <p class="text-[11px] text-slate-200/90 font-medium">
                            Sistema Institucional de Gestión del Talento Humano & Capacitación Continuada
                        </p>
                    </div>
                </div>

                <div class="text-right sm:text-right text-xs bg-white/10 backdrop-blur-md px-3.5 py-1.5 rounded-xl border border-white/15">
                    <span class="text-[10px] uppercase font-bold text-cyan-300 block">Código Oficial</span>
                    <span class="font-mono font-black text-white text-sm">{{ $certificado->codigo }}</span>
                </div>
            </div>

            <!-- SVG Wave Motif Decorativo -->
            <div class="absolute bottom-0 left-0 w-full overflow-hidden leading-none pointer-events-none opacity-25">
                <svg class="relative block w-full h-6 text-white" viewBox="0 0 1200 40" preserveAspectRatio="none">
                    <path d="M0,0 C150,35 350,10 500,25 C650,40 850,5 1000,20 C1100,30 1160,15 1200,25 L1200,40 L0,40 Z" fill="currentColor"/>
                </svg>
            </div>
        </div>

        <!-- CUERPO INTERNO DEL CERTIFICADO CON MARCO DORADO/AZUL -->
        <div class="p-8 sm:p-12 md:p-14 space-y-8 bg-gradient-to-b from-white via-slate-50/50 to-white relative">

            <!-- Fondo de plantilla personalizada si fue subida -->
            @if($rutaPlantilla)
                <div class="absolute inset-0 flex items-center justify-center pointer-events-none z-0 opacity-15 overflow-hidden">
                    <img src="{{ asset('storage/' . $rutaPlantilla) }}" 
                         alt="Fondo de Plantilla" 
                         class="w-full h-full object-cover select-none">
                </div>
            @else
                <!-- Marca de agua central suave y difuminada -->
                <div class="absolute inset-0 flex items-center justify-center pointer-events-none z-0">
                    <img src="{{ asset('images/Logo.png') }}" 
                         alt="Watermark Logo" 
                         class="w-[550px] max-w-[85vw] h-auto object-contain select-none"
                         style="opacity: 0.04; filter: grayscale(100%) opacity(0.5);">
                </div>
            @endif

            <!-- Encabezado de Acreditación con Logo Principal -->
            <div class="text-center space-y-3 relative z-10">
                <div class="flex items-center justify-center mb-2">
                    <img src="{{ asset('images/Logo.png') }}" 
                         alt="Logo C.I. Piscícola New York" 
                         class="h-16 sm:h-20 w-auto object-contain drop-shadow-sm">
                </div>

                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-amber-50 text-amber-800 text-xs font-bold border border-amber-200/80 shadow-2xs">
                    <i class="fa-solid fa-certificate text-amber-500 text-sm"></i>
                    <span>Acreditación Institucional Oficial</span>
                </div>

                <h1 class="font-heading font-black text-2xl sm:text-4xl text-brand-dark tracking-tight uppercase leading-tight">
                    CERTIFICADO DE CAPACITACIÓN Y APROBACIÓN
                </h1>

                <p class="text-xs sm:text-sm text-slate-500 max-w-xl mx-auto font-medium">
                    {{ $textoCert }}
                </p>
            </div>

            <!-- Datos del Colaborador -->
            <div class="text-center space-y-4 py-2 relative z-10">
                <div class="inline-block relative">
                    <h2 class="font-heading font-black text-2xl sm:text-4xl text-brand-dark tracking-tight px-4 uppercase">
                        {{ $certificado->nombre_empleado }}
                    </h2>
                    <div class="h-1 w-full bg-gradient-to-r from-transparent via-brand-blue to-transparent mt-1"></div>
                </div>

                <p class="text-xs sm:text-sm text-slate-600 font-semibold">
                    Con documento de identificación N.º <strong class="text-brand-dark font-extrabold">{{ $certificado->identificacion }}</strong>, adscrito al área de <strong class="text-brand-blue font-extrabold">{{ $certificado->area_nombre ?? 'Producción Piscícola' }}</strong>,
                </p>
            </div>

            <!-- Detalles del Curso / Capacitación y Acreditación -->
            <div class="max-w-3xl mx-auto bg-white p-6 sm:p-8 rounded-3xl border border-line/80 shadow-xs text-center space-y-4 relative z-10">
                <span class="text-[11px] uppercase font-extrabold tracking-widest text-brand-sky block">
                    HA CUMPLIDO SATISFACTORIAMENTE LA CAPACITACIÓN FORMATIVA EN:
                </span>
                
                <h3 class="font-heading font-black text-xl sm:text-2xl text-brand-dark leading-snug uppercase">
                    {{ $certificado->nombre_capacitacion }}
                </h3>
                
                <p class="text-xs sm:text-sm text-slate-600 font-medium leading-relaxed max-w-2xl mx-auto">
                    Con una intensidad horaria de <strong class="text-brand-dark font-bold">{{ $duracionHoras }} {{ Str::plural('hora', $duracionHoras) }}</strong>, realizada el día <strong class="text-brand-dark font-bold">{{ $fechaRealizada }}</strong>, con el propósito de fortalecer sus conocimientos y competencias en el área correspondiente.
                </p>

                <p class="text-xs text-slate-500 italic max-w-xl mx-auto pt-1">
                    Se expide el presente certificado como constancia de su participación y cumplimiento de la actividad formativa.
                </p>

                <div class="pt-2 flex flex-wrap items-center justify-center gap-3 text-xs">
                    <span class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full bg-slate-100 text-slate-700 font-bold border border-slate-200">
                        <i class="fa-solid fa-location-dot text-brand-blue"></i> Rivera - Huila, {{ $fechaRealizada }}
                    </span>
                </div>
            </div>

            <!-- FIRMAS DINÁMICAS (JEFE INMEDIATO & GERENCIA) -->
            <div class="pt-8 border-t border-line/70 grid grid-cols-1 sm:grid-cols-2 gap-8 text-center text-xs relative z-10">
                
                <!-- Firma 1: Jefe Inmediato -->
                <div class="space-y-2">
                    <div class="h-16 flex items-end justify-center pb-1">
                        @if($f1r)
                            <img src="{{ asset('storage/' . $f1r) }}" alt="Firma {{ $f1n }}" class="max-h-14 max-w-[170px] object-contain mx-auto">
                        @else
                            <div class="text-center">
                                <span class="font-serif italic text-lg sm:text-xl text-brand-dark font-extrabold tracking-widest block transform -rotate-3 select-none">
                                    {{ Str::limit($f1n, 20) }}
                                </span>
                                <span class="text-[9px] font-mono text-slate-400 block -mt-1">Digital Sign: #PNY-JEFE-{{ substr(md5($f1n), 0, 5) }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="w-56 mx-auto border-t-2 border-brand-dark/40 pt-1.5">
                        <p class="font-heading font-extrabold text-sm text-brand-dark">{{ $f1n }}</p>
                        <p class="text-[11px] text-muted font-medium">{{ $f1c }}</p>
                        <span class="inline-block text-[10px] font-bold text-brand-blue uppercase mt-0.5 tracking-wider">Jefe Inmediato</span>
                    </div>
                </div>

                <!-- Firma 2: Gerente -->
                <div class="space-y-2">
                    <div class="h-16 flex items-end justify-center pb-1">
                        @if($f2r)
                            <img src="{{ asset('storage/' . $f2r) }}" alt="Firma {{ $f2n }}" class="max-h-14 max-w-[170px] object-contain mx-auto">
                        @else
                            <div class="text-center">
                                <span class="font-serif italic text-lg sm:text-xl text-brand-blue font-extrabold tracking-widest block transform rotate-2 select-none">
                                    {{ Str::limit($f2n, 20) }}
                                </span>
                                <span class="text-[9px] font-mono text-slate-400 block -mt-1">Digital Sign: #PNY-GER-{{ substr(md5($f2n), 0, 5) }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="w-56 mx-auto border-t-2 border-brand-dark/40 pt-1.5">
                        <p class="font-heading font-extrabold text-sm text-brand-dark">{{ $f2n }}</p>
                        <p class="text-[11px] text-muted font-medium">{{ $f2c }}</p>
                        <span class="inline-block text-[10px] font-bold text-cyan-700 uppercase mt-0.5 tracking-wider">Gerencia General</span>
                    </div>
                </div>

            </div>

        </div>

        <!-- BANNER DE PIE DE PÁGINA CON OLAS -->
        <div class="relative bg-slate-900 text-white p-4 text-center text-[11px] text-slate-400 border-t border-slate-800">
            <p>Documento de Acreditación Oficial emitido por el Sistema de Capacitación de {{ $org }} · Código Autenticidad: {{ $certificado->codigo }}</p>
        </div>

    </div>

</div>

<style>
@media print {
    body {
        background: white !important;
    }
    aside, header, nav, .print\:hidden {
        display: none !important;
    }
    #certificado-documento {
        border: none !important;
        box-shadow: none !important;
        max-width: 100% !important;
        margin: 0 !important;
    }
}
</style>
@endsection
