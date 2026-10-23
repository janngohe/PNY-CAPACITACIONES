@extends('layouts.jefe')

@section('title', 'Inducciones y Código PIN')

@section('content')
<div class="p-6 lg:p-8 max-w-7xl mx-auto space-y-6">

    <!-- Encabezado -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-line">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-cyan-50 text-cyan-800 border border-cyan-200 uppercase tracking-wider">
                    <i class="fa-solid fa-chalkboard-user mr-1"></i> Módulo de Inducciones
                </span>
            </div>
            <h1 class="text-2xl font-black text-brand-dark tracking-tight font-serif">Inducciones Presenciales y Código PIN</h1>
            <p class="text-xs text-slate-500">Consulta el PIN de acceso vigente para coordinar inducciones en tu área o proyectarlas en sala.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('induccion.index') }}"
               target="_blank"
               class="px-4 py-2.5 rounded-xl bg-brand-blue hover:bg-brand-dark text-white font-bold text-xs tracking-wider uppercase transition-all shadow-md flex items-center gap-2">
                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                <span>Abrir Portal Facilitador</span>
            </a>
        </div>
    </div>

    <!-- Tarjeta Destacada con el PIN de 4 Dígitos -->
    <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-48 h-48 rounded-full bg-cyan-500/10 blur-3xl pointer-events-none"></div>

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
            <div class="max-w-xl">
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 mb-3">
                    <i class="fa-solid fa-key"></i> Clave de Facilitador
                </div>
                <h2 class="text-xl sm:text-2xl font-black text-white tracking-tight">PIN Vigente para Inducciones</h2>
                <p class="text-xs text-slate-300 mt-1.5 leading-relaxed">
                    Comparte este código de 4 dígitos con cualquier colaborador o facilitador de tu equipo que vaya a dictar una charla o inducción.
                    Solo necesitarán ingresar su <strong>cédula</strong> y este <strong>PIN</strong> en la pantalla de inducción.
                </p>
            </div>

            <!-- Caja Visual del PIN -->
            <div class="flex items-center gap-4 bg-slate-950/70 p-4 sm:p-5 rounded-2xl border border-slate-700/80">
                <div>
                    <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">PIN Oficial Actual</div>
                    <div class="font-mono text-3xl font-black tracking-widest text-cyan-400 select-all mt-1">
                        {{ $pinActual }}
                    </div>
                </div>

                <div class="border-l border-slate-800 pl-4">
                    <button type="button" onclick="copiarPin('{{ $pinActual }}')"
                            id="btnCopiarPin"
                            class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold transition-colors flex items-center gap-1.5 cursor-pointer">
                        <i class="fa-regular fa-copy"></i>
                        <span>Copiar</span>
                    </button>
                    <div class="text-[9px] text-slate-500 mt-1 text-center">4 dígitos</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Catálogo de Inducciones Aplicables a tu Área -->
    <div class="bg-white rounded-3xl border border-line p-6 shadow-2xs space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-layer-group text-brand-blue"></i> Inducciones Institucionales Disponibles
                </h3>
                <p class="text-xs text-slate-500">Charlas y presentaciones oficiales disponibles para proyectar con código QR</p>
            </div>
            <span class="text-xs font-bold px-3 py-1 rounded-full bg-slate-100 text-slate-600">
                {{ $inducciones->count() }} activas
            </span>
        </div>

        @if($inducciones->isEmpty())
            <div class="p-8 text-center text-xs text-slate-400">
                No hay inducciones disponibles en este momento.
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($inducciones as $ind)
                    <div class="p-4 rounded-2xl border border-slate-200 hover:border-cyan-500/50 bg-slate-50/50 transition-all flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-cyan-100 text-cyan-800">
                                    Inducción
                                </span>
                                <div class="flex items-center gap-1 text-xs text-slate-400">
                                    @if($ind->video_url)
                                        <i class="fa-brands fa-youtube text-red-500" title="Contiene video"></i>
                                    @endif
                                    @if($ind->archivo_presentacion)
                                        <i class="fa-solid fa-file-pdf text-red-600" title="Contiene PDF"></i>
                                    @endif
                                </div>
                            </div>
                            <h4 class="font-bold text-slate-800 text-sm mb-1">{{ $ind->titulo }}</h4>
                            <p class="text-xs text-slate-500 line-clamp-2 mb-3">{{ $ind->descripcion }}</p>
                        </div>

                        <div class="pt-3 border-t border-slate-200 flex items-center justify-between text-xs">
                            <span class="text-[11px] text-slate-400">
                                Disponible para todas las sedes
                            </span>
                            <a href="{{ route('induccion.index') }}"
                               target="_blank"
                               class="text-xs font-bold text-brand-blue hover:text-brand-dark flex items-center gap-1">
                                <span>Dictar</span>
                                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Sesiones Recientes Dictadas -->
    @if($sesionesRecientes->isNotEmpty())
        <div class="bg-white rounded-3xl border border-line p-6 shadow-2xs space-y-4">
            <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left text-brand-blue"></i> Últimas Sesiones Dictadas
            </h3>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase font-bold text-[10px]">
                        <tr>
                            <th class="py-2.5 px-3">Código</th>
                            <th class="py-2.5 px-3">Inducción</th>
                            <th class="py-2.5 px-3">Facilitador</th>
                            <th class="py-2.5 px-3">Fecha</th>
                            <th class="py-2.5 px-3 text-center">Asistentes</th>
                            <th class="py-2.5 px-3 text-right">Acta</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-600">
                        @foreach($sesionesRecientes as $ses)
                            <tr>
                                <td class="py-2.5 px-3 font-mono font-bold text-cyan-700">{{ $ses->codigo_sesion }}</td>
                                <td class="py-2.5 px-3 font-medium text-slate-800">{{ $ses->induccion->titulo }}</td>
                                <td class="py-2.5 px-3 text-slate-600">{{ $ses->facilitador->nombre_completo ?? 'N/A' }}</td>
                                <td class="py-2.5 px-3 text-slate-500 font-mono">{{ $ses->fecha_inicio ? $ses->fecha_inicio->format('d/m/Y h:i A') : '' }}</td>
                                <td class="py-2.5 px-3 text-center font-bold">{{ $ses->asistencias_count }}</td>
                                <td class="py-2.5 px-3 text-right">
                                    <a href="{{ route('induccion.acta.pdf', $ses->codigo_sesion) }}"
                                       class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-emerald-600 hover:text-white text-slate-700 font-bold transition-colors">
                                        <i class="fa-solid fa-file-pdf text-red-500"></i> PDF
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>

<script>
    function copiarPin(pin) {
        navigator.clipboard.writeText(pin).then(() => {
            const btn = document.getElementById('btnCopiarPin');
            btn.innerHTML = '<i class="fa-solid fa-check text-emerald-400"></i> <span>Copiado</span>';
            setTimeout(() => {
                btn.innerHTML = '<i class="fa-regular fa-copy"></i> <span>Copiar</span>';
            }, 2000);
        });
    }
</script>
@endsection
