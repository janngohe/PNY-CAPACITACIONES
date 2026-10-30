@extends('layouts.psicologia')

@section('title', 'Consentimiento ' . $consentimiento->codigo_documental)
@section('page_title', 'Consentimiento Informado')
@section('page_subtitle', $consentimiento->candidato->nombre_completo)

@section('content')
@php $badge = $consentimiento->badgeEstado(); @endphp

<div class="max-w-4xl mx-auto space-y-6">

    <!-- Encabezado con estado y navegación -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <h1 class="text-xl font-bold text-slate-900">Documento {{ $consentimiento->codigo_documental }}</h1>
                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-bold border {{ $badge['clase'] }}">
                    <i class="fa-solid {{ $badge['icono'] }}"></i>
                    {{ $badge['texto'] }}
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1">
                Aspirante: <strong>{{ $consentimiento->candidato->nombre_completo }}</strong> (CC {{ $consentimiento->candidato->numero_identificacion }})
            </p>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <a href="{{ route('psicologia.pruebas.historial') }}" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 px-3.5 py-2 text-xs font-semibold text-slate-600 transition-colors">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                Volver al Historial
            </a>
        </div>
    </div>

    <!-- Tarjeta Principal con Información y Descargas -->
    <div class="rounded-2xl border border-slate-200/80 bg-white p-6 md:p-8 shadow-2xs space-y-6">
        
        <!-- Ficha de Datos del Documento -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 p-4 rounded-xl bg-slate-50 border border-slate-200/60 text-xs">
            <div>
                <span class="text-slate-400 block font-medium">Formato:</span>
                <span class="font-bold text-slate-800">{{ $consentimiento->codigo_documental }} Ver {{ $consentimiento->version_documental }}</span>
            </div>
            <div>
                <span class="text-slate-400 block font-medium">Fecha Documental:</span>
                <span class="font-bold text-slate-800">{{ $consentimiento->fecha_consentimiento->format('d/m/Y') }}</span>
            </div>
            <div>
                <span class="text-slate-400 block font-medium">Ciudad/Municipio:</span>
                <span class="font-bold text-slate-800">{{ $consentimiento->ciudad_municipio }}</span>
            </div>
            <div>
                <span class="text-slate-400 block font-medium">Firma Física:</span>
                @if($consentimiento->fecha_firma_fisica)
                    <span class="font-bold text-emerald-700">Otorgada ({{ $consentimiento->fecha_firma_fisica->format('d/m/Y') }})</span>
                @else
                    <span class="font-bold text-amber-700">Pendiente de registro</span>
                @endif
            </div>
        </div>

        <!-- Opciones de Descarga Oficial -->
        <div>
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3 flex items-center gap-2">
                <i class="fa-solid fa-download text-brand-blue"></i> Descargas Oficiales del Formato
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Descargar DOCX -->
                <div class="flex items-center justify-between p-4 rounded-xl border border-blue-100 bg-blue-50/30 hover:bg-blue-50/60 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100 text-blue-700">
                            <i class="fa-solid fa-file-word text-lg"></i>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-slate-900 block">Formato Word (.DOCX)</span>
                            <span class="text-[11px] text-slate-500">Plantilla oficial con tablas y encabezados</span>
                        </div>
                    </div>
                    <a href="{{ route('psicologia.consentimientos.docx', $consentimiento) }}" class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 px-3 py-1.5 text-xs font-bold text-white shadow-2xs transition-all">
                        <i class="fa-solid fa-arrow-down text-xs"></i> Descargar
                    </a>
                </div>

                <!-- Descargar PDF -->
                <div class="flex items-center justify-between p-4 rounded-xl border border-rose-100 bg-rose-50/30 hover:bg-rose-50/60 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-rose-100 text-rose-700">
                            <i class="fa-solid fa-file-pdf text-lg"></i>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-slate-900 block">Formato PDF Oficial</span>
                            <span class="text-[11px] text-slate-500">Diseño institucional listo para impresión</span>
                        </div>
                    </div>
                    <a href="{{ route('psicologia.consentimientos.pdf', $consentimiento) }}" target="_blank" class="inline-flex items-center gap-1.5 rounded-lg bg-rose-600 hover:bg-rose-700 px-3 py-1.5 text-xs font-bold text-white shadow-2xs transition-all">
                        <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i> Abrir / Imprimir
                    </a>
                </div>
            </div>
        </div>

        <hr class="border-slate-100">

        <!-- Sección de Auditoría y Verificación de Firma Física -->
        <div class="rounded-xl border border-slate-200 bg-slate-50/70 p-5">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-2 flex items-center gap-2">
                <i class="fa-solid fa-signature text-emerald-600"></i> Registro de Firma Física y Aceptación
            </h2>
            <p class="text-xs text-slate-500 mb-4 leading-relaxed">
                El consentimiento no se asume firmado automáticamente con la sola descarga del documento. Debe registrarse explícitamente cuándo el aspirante otorgó su firma física sobre el documento impreso.
            </p>

            @if($consentimiento->estaFirmado())
                <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-900 space-y-1">
                    <p class="font-bold flex items-center gap-1.5">
                        <i class="fa-solid fa-circle-check text-emerald-600"></i>
                        Consentimiento otorgado y verificado en el sistema.
                    </p>
                    <p>Fecha de otorgamiento físico: <strong>{{ $consentimiento->fecha_firma_fisica->format('d/m/Y') }}</strong></p>
                    @if($consentimiento->observaciones)
                        <p class="mt-1 text-emerald-800">Observaciones: <em>{{ $consentimiento->observaciones }}</em></p>
                    @endif
                </div>

                <div class="mt-4 pt-4 border-t border-slate-200/60 flex items-center justify-between">
                    <span class="text-xs text-slate-500 font-medium">Requisito cumplido: El evaluado puede iniciar pruebas.</span>
                    <a href="{{ route('psicologia.pruebas.historial') }}" class="inline-flex items-center gap-2 rounded-xl bg-brand-blue hover:bg-blue-600 px-4 py-2 text-xs font-bold text-white shadow-md transition-all">
                        <i class="fa-solid fa-clock-rotate-left text-xs"></i>
                        Ir al Historial de Pruebas
                    </a>
                </div>
            @else
                <form action="{{ route('psicologia.consentimientos.marcar_firmado', $consentimiento) }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="fecha_firma_fisica" class="block text-xs font-bold text-slate-700 mb-1">
                                Fecha de Firma Física <span class="text-rose-500">*</span>
                            </label>
                            <input type="date"
                                   name="fecha_firma_fisica"
                                   id="fecha_firma_fisica"
                                   value="{{ now()->toDateString() }}"
                                   required
                                   class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs text-slate-800 focus:border-brand-blue focus:outline-none focus:ring-2 focus:ring-brand-blue/20">
                        </div>

                        <div>
                            <label for="observaciones" class="block text-xs font-bold text-slate-700 mb-1">
                                Observaciones de Archivo <span class="text-slate-400 font-normal">(Opcional)</span>
                            </label>
                            <input type="text"
                                   name="observaciones"
                                   id="observaciones"
                                   placeholder="Ej: Original firmado y archivado en custodia física."
                                   class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs text-slate-800 focus:border-brand-blue focus:outline-none focus:ring-2 focus:ring-brand-blue/20">
                        </div>
                    </div>

                    <div class="flex items-center justify-end pt-2">
                        <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 px-4 py-2 text-xs font-bold text-white shadow-md transition-all">
                            <i class="fa-solid fa-check text-xs"></i>
                            Confirmar Otorgamiento de Consentimiento
                        </button>
                    </div>
                </form>
            @endif
        </div>

    </div>

</div>
@endsection
