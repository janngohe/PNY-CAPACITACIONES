@extends('layouts.empleado')

@section('title', 'Certificado Oficial ' . $certificado->codigo . ' · C.I. Piscícola New York')
@section('page_title', 'Diploma Oficial de Capacitación')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto print:max-w-none print:p-0">

    <!-- BARRA SUPERIOR DE ACCIONES -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-4 sm:p-5 rounded-2xl border border-line/70 shadow-2xs print:hidden">
        <a href="{{ route('empleado.certificados') }}" class="inline-flex items-center gap-2 text-xs font-bold text-brand-blue hover:text-brand-deep transition-colors">
            <i class="fa-solid fa-arrow-left text-xs"></i>
            <span>Volver a Mis Certificados</span>
        </a>

        <div class="flex items-center gap-3">
            <a href="{{ route('empleado.certificados.pdf', $certificado) }}" id="btn-descargar-pdf"
                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-gradient-to-r from-brand-blue via-brand-deep to-brand-dark hover:brightness-110 text-white font-heading font-extrabold text-xs shadow-md hover:-translate-y-0.5 transition-all cursor-pointer">
                <i class="fa-solid fa-file-pdf text-sm"></i>
                <span id="btn-text">Descargar Certificado (PDF)</span>
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
                            C.I. PISCÍCOLA <span class="text-cyan-300">NEW YORK</span> S.A.S.
                        </h2>
                        <p class="text-[11px] text-slate-200/90 font-medium">
                            Sistema Institucional de Gestión del Talento Humano & Capacitación Continuada
                        </p>
                    </div>
                </div>

                <div class="text-center sm:text-right shrink-0 bg-white/10 backdrop-blur-md px-4 py-2 rounded-2xl border border-white/20">
                    <span class="block text-[9px] uppercase font-bold tracking-widest text-brand-sky">Código de Autenticidad</span>
                    <span class="font-mono font-extrabold text-xs sm:text-sm text-white tracking-wider">
                        {{ $certificado->codigo }}
                    </span>
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

            <!-- Marca de agua central suave y difuminada -->
            <div class="absolute inset-0 flex items-center justify-center pointer-events-none z-0">
                <img src="{{ asset('images/Logo.png') }}" 
                     alt="Watermark Logo" 
                     class="w-[550px] max-w-[85vw] h-auto object-contain select-none"
                     style="opacity: 0.04; filter: grayscale(100%) opacity(0.5);">
            </div>

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
                    La Dirección General de C.I. Piscícola New York S.A.S. hace constar formalmente que:
                </p>
            </div>

            <!-- Datos del Colaborador -->
            <div class="text-center space-y-4 py-2 relative z-10">
                <div class="inline-block relative">
                    <h2 class="font-heading font-black text-2xl sm:text-4xl text-brand-dark tracking-tight px-4">
                        {{ $certificado->nombre_empleado }}
                    </h2>
                    <div class="h-1 w-full bg-gradient-to-r from-transparent via-brand-blue to-transparent mt-1"></div>
                </div>

                <p class="text-xs sm:text-sm text-slate-600 font-semibold">
                    Con documento de identificación Nº <strong class="text-brand-dark font-extrabold">{{ $certificado->identificacion }}</strong>, adscrito al área de <strong class="text-brand-blue font-extrabold">{{ $certificado->area_nombre ?? 'Producción Piscícola' }}</strong>.
                </p>
            </div>

            <!-- Detalles del Curso / Capacitación -->
            <div class="max-w-2xl mx-auto bg-white p-6 rounded-3xl border border-line/80 shadow-xs text-center space-y-3 relative z-10">
                <span class="text-[10px] uppercase font-extrabold tracking-widest text-brand-sky block">
                    HA CUMPLIDO SATISFACTORIAMENTE EL PROGRAMA FORMATIVO:
                </span>
                <h3 class="font-heading font-extrabold text-xl sm:text-2xl text-brand-dark leading-snug">
                    {{ $certificado->nombre_capacitacion }}
                </h3>
                
                <div class="pt-2 flex flex-wrap items-center justify-center gap-4 text-xs">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-800 font-bold border border-emerald-200">
                        <i class="fa-solid fa-square-check text-emerald-600"></i> Calificación: {{ rtrim(rtrim(number_format((float)$certificado->porcentaje, 2), '0'), '.') }}% (Aprobado)
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 text-brand-blue font-bold border border-blue-200">
                        <i class="fa-regular fa-calendar-check"></i> Fecha de Emisión: {{ $certificado->fecha_emision ? $certificado->fecha_emision->format('d/m/Y') : date('d/m/Y') }}
                    </span>
                </div>
            </div>

            <!-- FIRMAS SIMULADAS DE DEMOSTRACIÓN -->
            <div class="pt-8 border-t border-line/70 grid grid-cols-1 sm:grid-cols-2 gap-8 text-center text-xs relative z-10">
                
                <!-- Firma 1 -->
                <div class="space-y-2">
                    <div class="h-16 flex items-end justify-center pb-1">
                        <div class="text-center">
                            <span class="font-serif italic text-lg sm:text-xl text-brand-dark font-extrabold tracking-widest block transform -rotate-3 select-none">
                                C. Mendoza
                            </span>
                            <span class="text-[9px] font-mono text-slate-400 block -mt-1">Digital Sign: #PNY-AUTH-8821</span>
                        </div>
                    </div>
                    <div class="w-56 mx-auto border-t-2 border-brand-dark/40 pt-1.5">
                        <p class="font-heading font-extrabold text-sm text-brand-dark">Ing. Carlos Mendoza</p>
                        <p class="text-[11px] text-muted font-medium">Director de Gestión Humana & Bioseguridad</p>
                        <p class="text-[10px] text-slate-400">C.I. Piscícola New York S.A.S.</p>
                    </div>
                </div>

                <!-- Firma 2 -->
                <div class="space-y-2">
                    <div class="h-16 flex items-end justify-center pb-1">
                        <div class="text-center">
                            <span class="font-serif italic text-lg sm:text-xl text-brand-blue font-extrabold tracking-widest block transform rotate-2 select-none">
                                E. Ramos H.
                            </span>
                            <span class="text-[9px] font-mono text-slate-400 block -mt-1">Digital Sign: #PNY-QUAL-9943</span>
                        </div>
                    </div>
                    <div class="w-56 mx-auto border-t-2 border-brand-dark/40 pt-1.5">
                        <p class="font-heading font-extrabold text-sm text-brand-dark">Dra. Elena Ramos</p>
                        <p class="text-[11px] text-muted font-medium">Gerente de Calidad & Procesos</p>
                        <p class="text-[10px] text-slate-400">C.I. Piscícola New York S.A.S.</p>
                    </div>
                </div>

            </div>

        </div>

        <!-- BANNER DE PIE DE PÁGINA CON OLAS -->
        <div class="relative bg-slate-900 text-white p-4 text-center text-[11px] text-slate-400 border-t border-slate-800">
            <p>Documento de Acreditación Interna emitido por el Sistema de Capacitación de C.I. Piscícola New York S.A.S.</p>
        </div>

    </div>

</div>

@endsection
