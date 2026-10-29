@extends('layouts.psicologia')

@section('title', 'Historial de Pruebas · Psicología')
@section('page_title', 'Historial de Pruebas')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    <!-- CABECERA DE LA SECCIÓN -->
    <div class="bg-white rounded-2xl p-6 border border-line/70 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md bg-indigo-50 text-indigo-600 text-[11px] font-bold uppercase tracking-wider mb-2">
                <i class="fa-solid fa-clock-rotate-left"></i>
                <span>Módulo de Historial</span>
            </div>
            <h2 class="text-xl font-heading font-extrabold text-brand-dark">Historial de Pruebas</h2>
            <p class="text-xs text-muted mt-1">Registro histórico de pruebas realizadas, aspirantes evaluados y estado de aptitud.</p>
        </div>
    </div>

    <!-- CONTENEDOR EN ESPERA DE REQUERIMIENTOS -->
    <div class="bg-white rounded-3xl p-10 border-2 border-dashed border-line text-center flex flex-col items-center justify-center min-h-[360px]">
        <div class="w-16 h-16 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-2xl shadow-xs mb-4">
            <i class="fa-solid fa-clock-rotate-left"></i>
        </div>
        <h3 class="font-heading font-bold text-lg text-brand-dark">Módulo: Historial de Pruebas</h3>
        <p class="text-xs text-muted max-w-md mt-1.5 leading-relaxed">
            Este módulo se encuentra configurado y listo en el menú. Aquí colocaremos el listado cronológico con buscador, filtros y detalles de cada prueba según tus indicaciones.
        </p>
        <span class="mt-4 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-600 border border-indigo-200">
            <i class="fa-solid fa-pen-ruler mr-1.5"></i> Pendiente de especificación de campos
        </span>
    </div>

</div>
@endsection

