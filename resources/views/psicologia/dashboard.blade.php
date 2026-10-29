@extends('layouts.psicologia')

@section('title', 'Inicio · Panel de Psicología')
@section('page_title', 'Inicio')

@section('content')
<div class="max-w-7xl mx-auto space-y-8">

    <!-- HERO DE BIENVENIDA CON COLORES DEL AGUA DE LA PISCÍCOLA -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-brand-dark via-brand-deep to-brand-blue p-6 sm:p-9 text-white shadow-md">
        <div class="absolute -right-10 -bottom-10 w-48 h-48 rounded-full bg-white/5 blur-xl"></div>
        <div class="absolute right-24 -top-10 w-40 h-40 rounded-full bg-brand-sky/15 blur-xl"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md text-brand-sky text-xs font-semibold mb-3 border border-white/15">
                    <i class="fa-solid fa-water"></i>
                    <span>C.I. Piscícola New York · Departamento de Psicología</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-heading font-extrabold tracking-tight text-white">
                    ¡Bienvenida/o, {{ $usuario->nombre_completo ?? 'Especialista' }}!
                </h2>
                <p class="mt-2 text-xs sm:text-sm text-slate-200 leading-relaxed">
                    Accede a los módulos de evaluación psicológica, emisión de certificados y generación de reportes aptitudinales de la compañía.
                </p>
            </div>

            <!-- Botón hacia capacitaciones del colaborador -->
            <div class="shrink-0">
                <a href="{{ route('empleado.capacitaciones') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/15 hover:bg-white/25 text-white border border-white/25 font-heading font-bold text-xs transition shadow-2xs">
                    <i class="fa-solid fa-graduation-cap text-brand-sky"></i>
                    <span>Mis Capacitaciones</span>
                </a>
            </div>
        </div>
    </div>

    <!-- SECCIÓN DE LOS 4 MÓDULOS PRINCIPALES -->
    <div>
        <div class="mb-5">
            <h3 class="font-heading font-bold text-base sm:text-lg text-brand-dark">Módulos de Gestión Psicológica</h3>
            <p class="text-xs text-muted">Selecciona una de las opciones para comenzar a trabajar:</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

            <!-- 1. Realizar Prueba -->
            <a href="{{ route('psicologia.pruebas.realizar') }}"
               class="group bg-white rounded-2xl p-6 border-2 border-line/80 hover:border-brand-blue shadow-xs hover:shadow-lg hover:-translate-y-1 transition-all duration-200 flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-brand-light text-brand-blue flex items-center justify-center text-xl mb-4 group-hover:bg-brand-blue group-hover:text-white transition-all shadow-2xs">
                        <i class="fa-solid fa-clipboard-question"></i>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-brand-blue bg-brand-light/70 px-2.5 py-0.5 rounded-full">
                        Módulo 01
                    </span>
                    <h4 class="font-heading font-bold text-base text-brand-dark group-hover:text-brand-blue transition-colors mt-2">
                        Realizar Prueba
                    </h4>
                    <p class="text-xs text-muted mt-1.5 leading-relaxed">
                        Aplicación y registro de pruebas psicológicas y cuestionarios de evaluación.
                    </p>
                </div>
                <div class="mt-6 pt-4 border-t border-line/60 flex items-center justify-between text-brand-blue font-heading font-bold text-xs">
                    <span>Acceder al módulo</span>
                    <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </div>
            </a>

            <!-- 2. Reportes -->
            <a href="{{ route('psicologia.reportes') }}"
               class="group bg-white rounded-2xl p-6 border-2 border-line/80 hover:border-brand-blue shadow-xs hover:shadow-lg hover:-translate-y-1 transition-all duration-200 flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-sky-50 text-brand-sky flex items-center justify-center text-xl mb-4 group-hover:bg-brand-sky group-hover:text-white transition-all shadow-2xs">
                        <i class="fa-solid fa-chart-column"></i>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-brand-cyan bg-sky-50 px-2.5 py-0.5 rounded-full">
                        Módulo 02
                    </span>
                    <h4 class="font-heading font-bold text-base text-brand-dark group-hover:text-brand-blue transition-colors mt-2">
                        Reportes
                    </h4>
                    <p class="text-xs text-muted mt-1.5 leading-relaxed">
                        Consultas analíticas, estadísticas de resultados y consolidación de datos.
                    </p>
                </div>
                <div class="mt-6 pt-4 border-t border-line/60 flex items-center justify-between text-brand-blue font-heading font-bold text-xs">
                    <span>Acceder al módulo</span>
                    <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </div>
            </a>

            <!-- 3. Historial de Pruebas -->
            <a href="{{ route('psicologia.pruebas.historial') }}"
               class="group bg-white rounded-2xl p-6 border-2 border-line/80 hover:border-brand-blue shadow-xs hover:shadow-lg hover:-translate-y-1 transition-all duration-200 flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl mb-4 group-hover:bg-indigo-600 group-hover:text-white transition-all shadow-2xs">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-600 bg-indigo-50 px-2.5 py-0.5 rounded-full">
                        Módulo 03
                    </span>
                    <h4 class="font-heading font-bold text-base text-brand-dark group-hover:text-brand-blue transition-colors mt-2">
                        Historial de Pruebas
                    </h4>
                    <p class="text-xs text-muted mt-1.5 leading-relaxed">
                        Registro cronológico de evaluaciones aplicadas a aspirantes y colaboradores.
                    </p>
                </div>
                <div class="mt-6 pt-4 border-t border-line/60 flex items-center justify-between text-brand-blue font-heading font-bold text-xs">
                    <span>Acceder al módulo</span>
                    <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </div>
            </a>

            <!-- 4. Certificado Aptitudinal -->
            <a href="{{ route('psicologia.certificado_aptitudinal') }}"
               class="group bg-white rounded-2xl p-6 border-2 border-line/80 hover:border-brand-blue shadow-xs hover:shadow-lg hover:-translate-y-1 transition-all duration-200 flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl mb-4 group-hover:bg-emerald-600 group-hover:text-white transition-all shadow-2xs">
                        <i class="fa-solid fa-file-circle-check"></i>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-full">
                        Módulo 04
                    </span>
                    <h4 class="font-heading font-bold text-base text-brand-dark group-hover:text-brand-blue transition-colors mt-2">
                        Certificado Aptitudinal
                    </h4>
                    <p class="text-xs text-muted mt-1.5 leading-relaxed">
                        Emisión oficial, firma y descarga de certificados de aptitud laboral.
                    </p>
                </div>
                <div class="mt-6 pt-4 border-t border-line/60 flex items-center justify-between text-brand-blue font-heading font-bold text-xs">
                    <span>Acceder al módulo</span>
                    <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </div>
            </a>

        </div>
    </div>

</div>
@endsection
