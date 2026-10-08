@extends('layouts.empleado')

@section('title', 'Anexo Certificados')
@section('page_title', 'Anexo de Certificados Externos')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    
    <!-- Encabezado de la Sección con botón para subir -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-line/70 shadow-2xs">
        <div>
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-brand-light text-brand-blue text-xs font-bold mb-2">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m6.75 12l-3-3m0 0l-3 3m3-3v6m-1.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                </svg>
                <span>Validación de Saberes Externos</span>
            </div>
            <h1 class="font-heading font-extrabold text-xl sm:text-2xl text-brand-dark">
                Certificados y Cursos Externos
            </h1>
            <p class="text-xs sm:text-sm text-muted mt-0.5">
                Adjunta diplomas, títulos y constancias (SENA, ICA, Bomberos, etc.) para acreditación en tu hoja de vida en C.I. Piscícola New York.
            </p>
        </div>

        <div>
            <button type="button" 
                    onclick="document.getElementById('modal-upload').classList.remove('hidden')"
                    class="py-3 px-5 rounded-xl bg-brand-blue hover:bg-brand-deep text-white font-heading font-bold text-xs sm:text-sm shadow-xs transition-colors flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>Anexar Nuevo Certificado</span>
            </button>
        </div>
    </div>

    <!-- Lista de Certificados Externos Radicados -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 sm:gap-6">
        @forelse($certificadosExternos as $ext)
        @php
            $entidad = $ext->entidad_emisora ?? ($ext['entidad_emisora'] ?? 'Entidad Externa');
            $fecha = is_object($ext->fecha_emision ?? null) ? $ext->fecha_emision->format('d M Y') : ($ext->fecha_emision ?? ($ext['fecha_emision'] ?? '---'));
            $estado = $ext->estado ?? ($ext['estado'] ?? 'PENDIENTE');
            $archivo = $ext->ruta_archivo ?? ($ext['archivo_nombre'] ?? 'documento.pdf');
        @endphp
        <div class="bg-white rounded-3xl border border-line/80 shadow-xs p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-brand-blue flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m6.75 12l-3-3m0 0l-3 3m3-3v6m-1.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                        </div>
                        <div>
                            <span class="text-[11px] font-bold text-muted uppercase">
                                {{ $entidad }}
                            </span>
                            <h3 class="font-heading font-extrabold text-base text-brand-dark leading-snug">
                                Certificado Externo Acreditado
                            </h3>
                        </div>
                    </div>

                    <!-- Estado del certificado -->
                    <div>
                        @if($estado === 'APROBADO')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                Aprobado
                            </span>
                        @elseif($estado === 'PENDIENTE')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                                En Revisión
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-red-100 text-red-800">
                                Rechazado
                            </span>
                        @endif
                    </div>
                </div>

                <div class="mt-4 p-3.5 bg-slate-50 rounded-2xl border border-line/60 text-xs space-y-1.5 text-slate-600">
                    <div class="flex justify-between">
                        <span class="text-muted">Fecha de expedición:</span>
                        <span class="font-semibold text-slate-700">{{ $fecha }}</span>
                    </div>
                    <div class="flex justify-between items-center pt-1 border-t border-line/40">
                        <span class="text-muted">Archivo soporte:</span>
                        <span class="font-mono text-[11px] text-brand-blue truncate max-w-[180px]">{{ basename($archivo) }}</span>
                    </div>
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-line/60 flex items-center justify-between text-xs">
                <span class="text-muted text-[11px]">Validado por Talento Humano PNY</span>
                <a href="{{ asset('storage/' . $archivo) }}" target="_blank" class="text-brand-blue hover:text-brand-deep font-bold hover:underline flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                    </svg>
                    <span>Ver documento</span>
                </a>
            </div>
        </div>
        @empty
        <div class="col-span-full bg-white rounded-3xl border border-line/80 p-10 text-center space-y-3">
            <div class="w-14 h-14 mx-auto rounded-2xl bg-blue-50 text-brand-blue flex items-center justify-center">
                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m6.75 12l-3-3m0 0l-3 3m3-3v6m-1.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                </svg>
            </div>
            <h3 class="font-heading font-extrabold text-base text-brand-dark">No has anexado certificados externos</h3>
            <p class="text-xs text-muted max-w-sm mx-auto">
                Si cuentas con constancias del SENA, ICA u otras entidades, adjúntalas haciendo clic en "Anexar Nuevo Certificado".
            </p>
        </div>
        @endforelse
    </div>

    <!-- MODAL DE CARGA DE CERTIFICADOS EXTERNOS CON SUBIDA REAL -->
    <div id="modal-upload" class="fixed inset-0 bg-brand-dark/50 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-line relative animate-in fade-in zoom-in-95 duration-200">
            
            <div class="flex items-center justify-between mb-5 pb-3 border-b border-line">
                <div>
                    <h3 class="font-heading font-extrabold text-lg text-brand-dark">
                        Anexar Certificado Externo
                    </h3>
                    <p class="text-xs text-muted">Sube tu constancia en formato PDF o imagen.</p>
                </div>
                <button type="button" 
                        onclick="document.getElementById('modal-upload').classList.add('hidden')"
                        class="p-2 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('empleado.anexo.guardar') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-brand-dark mb-1">Entidad Emisora</label>
                    <input type="text" name="entidad_emisora" placeholder="Ej: SENA, ICA, Cruz Roja..." required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-line text-xs focus:outline-none focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20">
                </div>

                <div>
                    <label class="block text-xs font-bold text-brand-dark mb-1">Fecha de Emisión</label>
                    <input type="date" name="fecha_emision" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-line text-xs focus:outline-none focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20">
                </div>

                <div>
                    <label class="block text-xs font-bold text-brand-dark mb-1">Archivo de Soporte (PDF o Imagen, máx 10MB)</label>
                    <div class="border-2 border-dashed border-line hover:border-brand-blue rounded-2xl p-6 text-center cursor-pointer transition-colors bg-slate-50/50" onclick="document.getElementById('archivo_input').click()">
                        <svg class="w-8 h-8 text-brand-blue mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
                        </svg>
                        <p id="archivo_nombre_display" class="text-xs font-semibold text-slate-700">Haz clic aquí para seleccionar tu archivo</p>
                        <p class="text-[10px] text-muted mt-0.5">Formatos permitidos: PDF, JPG, PNG</p>
                        <input type="file" name="archivo" id="archivo_input" accept=".pdf,.jpg,.jpeg,.png" required class="hidden" onchange="document.getElementById('archivo_nombre_display').textContent = this.files[0] ? this.files[0].name : 'Haz clic aquí para seleccionar tu archivo'">
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-line">
                    <button type="button" 
                            onclick="document.getElementById('modal-upload').classList.add('hidden')"
                            class="px-4 py-2.5 rounded-xl border border-line text-xs font-semibold text-slate-600 hover:bg-slate-50">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="px-5 py-2.5 rounded-xl bg-brand-blue hover:bg-brand-deep text-white text-xs font-bold font-heading shadow-xs">
                        Radicar Certificado
                    </button>
                </div>
            </form>

        </div>
    </div>

</div>
@endsection
