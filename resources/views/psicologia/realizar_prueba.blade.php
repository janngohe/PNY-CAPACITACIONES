@extends('layouts.psicologia')

@section('title', 'Realizar Prueba · Psicología')
@section('page_title', 'Realizar Prueba')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    <!-- CABECERA DE LA SECCIÓN -->
    <div class="bg-white rounded-2xl p-6 border border-line/70 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md bg-brand-light text-brand-blue text-[11px] font-bold uppercase tracking-wider mb-2">
                <i class="fa-solid fa-clipboard-question"></i>
                <span>Módulo de Evaluación</span>
            </div>
            <h2 class="text-xl font-heading font-extrabold text-brand-dark">Realizar Prueba Psicológica</h2>
            <p class="text-xs text-muted mt-1">Aplica pruebas psicotécnicas, cuestionarios de personalidad o evaluaciones de competencias laborales.</p>
        </div>
    </div>

    <!-- CONTENEDOR EN ESPERA DE REQUERIMIENTOS -->
    <div class="bg-white rounded-3xl p-10 border-2 border-dashed border-line text-center flex flex-col items-center justify-center min-h-[360px]">
        <div class="w-16 h-16 rounded-2xl bg-brand-light text-brand-blue flex items-center justify-center text-2xl shadow-xs mb-4">
            <i class="fa-solid fa-clipboard-question"></i>
        </div>
        <h3 class="font-heading font-bold text-lg text-brand-dark">Módulo: Realizar Prueba</h3>
        <p class="text-xs text-muted max-w-md mt-1.5 leading-relaxed">
            Este módulo se encuentra configurado y listo en el menú. Aquí colocaremos la prueba, cuestionarios o formulario de aplicación según tus indicaciones.
        </p>
        <span class="mt-4 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-brand-light text-brand-blue border border-brand-blue/20">
            <i class="fa-solid fa-pen-ruler mr-1.5"></i> Pendiente de especificación de campos
        </span>
    </div>

</div>
@endsection

