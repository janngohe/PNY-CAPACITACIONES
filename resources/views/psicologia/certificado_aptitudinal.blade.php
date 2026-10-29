@extends('layouts.psicologia')

@section('title', 'Certificado Aptitudinal · Psicología')
@section('page_title', 'Certificado Aptitudinal')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    <!-- CABECERA DE LA SECCIÓN -->
    <div class="bg-white rounded-2xl p-6 border border-line/70 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-700 text-[11px] font-bold uppercase tracking-wider mb-2">
                <i class="fa-solid fa-file-circle-check"></i>
                <span>Módulo de Certificación</span>
            </div>
            <h2 class="text-xl font-heading font-extrabold text-brand-dark">Certificado Aptitudinal</h2>
            <p class="text-xs text-muted mt-1">Emisión oficial, firma psicológica y descarga de certificados de aptitud laboral.</p>
        </div>
    </div>

    <!-- CONTENEDOR EN ESPERA DE REQUERIMIENTOS -->
    <div class="bg-white rounded-3xl p-10 border-2 border-dashed border-line text-center flex flex-col items-center justify-center min-h-[360px]">
        <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl shadow-xs mb-4">
            <i class="fa-solid fa-file-circle-check"></i>
        </div>
        <h3 class="font-heading font-bold text-lg text-brand-dark">Módulo: Certificado Aptitudinal</h3>
        <p class="text-xs text-muted max-w-md mt-1.5 leading-relaxed">
            Este módulo se encuentra configurado y listo en el menú. Aquí colocaremos la generación, vista previa y descarga en PDF del concepto o certificado aptitudinal según tus indicaciones.
        </p>
        <span class="mt-4 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
            <i class="fa-solid fa-pen-ruler mr-1.5"></i> Pendiente de especificación de campos
        </span>
    </div>

</div>
@endsection

