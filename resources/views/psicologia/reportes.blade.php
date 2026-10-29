@extends('layouts.psicologia')

@section('title', 'Reportes · Psicología')
@section('page_title', 'Reportes')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    <!-- CABECERA DE LA SECCIÓN -->
    <div class="bg-white rounded-2xl p-6 border border-line/70 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md bg-sky-50 text-brand-sky text-[11px] font-bold uppercase tracking-wider mb-2">
                <i class="fa-solid fa-chart-column"></i>
                <span>Módulo de Analítica</span>
            </div>
            <h2 class="text-xl font-heading font-extrabold text-brand-dark">Reportes Psicológicos</h2>
            <p class="text-xs text-muted mt-1">Generación de informes consolidados, estadísticas por sede/área y análisis de resultados.</p>
        </div>
    </div>

    <!-- CONTENEDOR EN ESPERA DE REQUERIMIENTOS -->
    <div class="bg-white rounded-3xl p-10 border-2 border-dashed border-line text-center flex flex-col items-center justify-center min-h-[360px]">
        <div class="w-16 h-16 rounded-2xl bg-sky-50 text-brand-sky flex items-center justify-center text-2xl shadow-xs mb-4">
            <i class="fa-solid fa-chart-column"></i>
        </div>
        <h3 class="font-heading font-bold text-lg text-brand-dark">Módulo: Reportes</h3>
        <p class="text-xs text-muted max-w-md mt-1.5 leading-relaxed">
            Este módulo se encuentra configurado y listo en el menú. Aquí colocaremos los filtros de descarga, tablas dinámicas y gráficos analíticos según tus indicaciones.
        </p>
        <span class="mt-4 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-sky-50 text-brand-cyan border border-brand-cyan/20">
            <i class="fa-solid fa-pen-ruler mr-1.5"></i> Pendiente de especificación de campos
        </span>
    </div>

</div>
@endsection

