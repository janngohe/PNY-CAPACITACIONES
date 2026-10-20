@extends('layouts.admin')

@section('title', 'Gestión de Inducciones y PIN')

@section('content')
<div class="space-y-6">

    <!-- Encabezado de Página -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-cyan-100 text-cyan-800 border border-cyan-200 uppercase tracking-wider">
                    <i class="fa-solid fa-chalkboard-user mr-1"></i> Control de Inducciones
                </span>
            </div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight font-serif">Módulo de Inducciones Presenciales</h1>
            <p class="text-xs text-slate-500">Administra las inducciones presenciales, sesiones dictadas con QR y el código PIN de acceso para facilitadores.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('induccion.index') }}"
               target="_blank"
               class="px-4 py-2.5 rounded-xl bg-cyan-600 hover:bg-cyan-700 text-white font-bold text-xs tracking-wider uppercase transition-all shadow-md flex items-center gap-2">
                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                <span>Abrir Portal Facilitador</span>
            </a>
        </div>
    </div>

    <!-- Mensajes Flash -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2.5">
            <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center gap-2.5">
            <i class="fa-solid fa-circle-exclamation text-rose-600 text-sm"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Tarjeta Principal: Gestión y Regeneración de PIN de Facilitador -->
    <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-56 h-56 rounded-full bg-cyan-500/10 blur-3xl pointer-events-none"></div>

        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10">
            <div class="max-w-xl">
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 mb-3">
                    <i class="fa-solid fa-key"></i> Código de Acceso de Facilitadores
                </div>
                <h2 class="text-xl sm:text-2xl font-black text-white tracking-tight">PIN Institucional de Inducción</h2>
                <p class="text-xs text-slate-300 mt-1.5 leading-relaxed">
                    Este código de 4 dígitos permite que <strong>cualquier colaborador o facilitador autorizado</strong> ingrese con su cédula al portal de inducción sin alterar su rol ni su menú personal.
                    Todos los <strong>Jefes de Área</strong> pueden visualizar este PIN para coordinar charlas.
                </p>
            </div>

            <!-- Caja del PIN y Botón de Regenerar -->
            <div class="flex flex-col sm:flex-row items-center gap-4 bg-slate-950/60 p-4 sm:p-5 rounded-2xl border border-slate-700/80">
                <div class="text-center sm:text-left">
                    <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">PIN Actual Vigente</div>
                    <div class="flex items-center gap-3 mt-1">
                        <span id="pinDisplay" class="font-mono text-3xl font-black tracking-widest text-cyan-400 select-all">
                            {{ $pinActual }}
                        </span>
                        <button type="button" onclick="togglePinVisibilidad()"
                                class="p-1.5 text-slate-400 hover:text-white transition-colors"
                                title="Ocultar / Mostrar PIN">
                            <i id="pinEyeIcon" class="fa-solid fa-eye-slash text-sm"></i>
                        </button>
                    </div>
                </div>

                <div class="sm:border-l sm:border-slate-800 sm:pl-4">
                    <button type="button" onclick="abrirModalRegenerarPin()"
                            class="px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-md flex items-center gap-2 cursor-pointer">
                        <i class="fa-solid fa-arrows-rotate"></i>
                        <span>Regenerar PIN</span>
                    </button>
                    <div class="text-[10px] text-slate-400 mt-1 text-center sm:text-left">Requiere contraseña</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Métricas del Módulo de Inducciones -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs">
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Inducciones Creadas</div>
            <div class="text-2xl font-black text-slate-800 mt-1">{{ $estadisticas['total_inducciones'] }}</div>
            <div class="text-[11px] text-cyan-600 font-semibold mt-0.5">Catálogo activo</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs">
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Sesiones Dictadas</div>
            <div class="text-2xl font-black text-slate-800 mt-1">{{ $estadisticas['total_sesiones'] }}</div>
            <div class="text-[11px] text-slate-500 mt-0.5">Proyecciones con QR</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs">
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Total Asistencias</div>
            <div class="text-2xl font-black text-emerald-600 mt-1">{{ $estadisticas['total_asistencias'] }}</div>
            <div class="text-[11px] text-slate-500 mt-0.5">Firmas registradas</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs">
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Externos / Contratistas</div>
            <div class="text-2xl font-black text-amber-600 mt-1">{{ $estadisticas['asistencias_externos'] }}</div>
            <div class="text-[11px] text-slate-500 mt-0.5">Personal no de nómina</div>
        </div>
    </div>

    <!-- Historial de Sesiones en Vivo y Actas -->
    <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-2xs">
        <div class="p-5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-file-signature text-cyan-600"></i> Historial de Sesiones Dictadas y Actas Oficiales
                </h3>
                <p class="text-xs text-slate-500">Consulta las asistencias recolectadas y descarga las actas PDF con firmas para auditorías</p>
            </div>
        </div>

        @if($sesiones->isEmpty())
            <div class="p-12 text-center text-xs text-slate-400">
                Aún no se han dictado sesiones de inducción en la plataforma.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider text-[11px] border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">Código</th>
                            <th class="py-3 px-4">Inducción</th>
                            <th class="py-3 px-4">Facilitador</th>
                            <th class="py-3 px-4">Sede</th>
                            <th class="py-3 px-4">Fecha</th>
                            <th class="py-3 px-4 text-center">Asistentes</th>
                            <th class="py-3 px-4">Estado</th>
                            <th class="py-3 px-4 text-right">Acta PDF</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-600">
                        @foreach($sesiones as $s)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-3.5 px-4 font-mono font-bold text-cyan-700">
                                    {{ $s->codigo_sesion }}
                                </td>
                                <td class="py-3.5 px-4 font-bold text-slate-800">
                                    {{ $s->induccion->titulo }}
                                    <span class="block text-[10px] text-slate-400 font-normal">{{ $s->induccion->tipo }}</span>
                                </td>
                                <td class="py-3.5 px-4 text-slate-700 font-medium">
                                    {{ $s->facilitador->nombre_completo ?? 'N/A' }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-600">
                                    {{ $s->sede->nombre ?? 'Sede Principal' }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-500 font-mono text-[11px]">
                                    {{ $s->fecha_inicio ? $s->fecha_inicio->format('d/m/Y h:i A') : 'N/A' }}
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-cyan-50 text-cyan-800 border border-cyan-200">
                                        <i class="fa-solid fa-users text-[10px]"></i> {{ $s->asistencias_count }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4">
                                    @if($s->estaActiva())
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> En Curso
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                            Finalizada
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <a href="{{ route('admin.inducciones.acta_pdf', $s->id) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-emerald-600 hover:text-white text-slate-700 font-bold text-xs transition-colors shadow-2xs"
                                       title="Descargar Acta Oficial en PDF">
                                        <i class="fa-solid fa-file-pdf text-red-500"></i>
                                        <span>PDF</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-200">
                {{ $sesiones->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Modal para Regenerar PIN con Contraseña del Administrador -->
<div id="modalRegenerarPin" class="fixed inset-0 z-50 hidden bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl relative animate-fade-in">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2 text-rose-600 font-bold text-sm uppercase tracking-wider">
                <i class="fa-solid fa-shield-halved"></i> Seguridad de Acceso
            </div>
            <button type="button" onclick="cerrarModalRegenerarPin()" class="text-slate-400 hover:text-slate-700 text-lg">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <h3 class="text-lg font-black text-slate-900 mb-2">¿Regenerar el PIN de Inducción?</h3>
        <p class="text-xs text-slate-500 mb-6 leading-relaxed">
            Al regenerar el PIN, el código anterior quedará invalidado de inmediato. Cualquier facilitador deberá usar el nuevo PIN de 4 dígitos para acceder.
        </p>

        <form action="{{ route('admin.inducciones.regenerar_pin') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="password_confirmacion" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Contraseña del Administrador <span class="text-rose-500">*</span>
                </label>
                <input type="password"
                       name="password_confirmacion"
                       id="password_confirmacion"
                       placeholder="Ingresa tu contraseña actual"
                       required
                       class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-2xl text-slate-900 text-sm focus:outline-none focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20">
            </div>

            <div class="flex items-center justify-end gap-3 pt-3">
                <button type="button" onclick="cerrarModalRegenerarPin()"
                        class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors cursor-pointer">
                    Cancelar
                </button>
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold uppercase tracking-wider transition-all shadow-md flex items-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-arrows-rotate"></i>
                    <span>Confirmar y Regenerar</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let pinOculto = false;
    const pinReal = @json($pinActual);

    function togglePinVisibilidad() {
        const pinDisplay = document.getElementById('pinDisplay');
        const icon = document.getElementById('pinEyeIcon');

        if (pinOculto) {
            pinDisplay.textContent = pinReal;
            icon.className = "fa-solid fa-eye-slash text-sm";
            pinOculto = false;
        } else {
            pinDisplay.textContent = "••••";
            icon.className = "fa-solid fa-eye text-sm";
            pinOculto = true;
        }
    }

    function abrirModalRegenerarPin() {
        document.getElementById('modalRegenerarPin').classList.remove('hidden');
    }

    function cerrarModalRegenerarPin() {
        document.getElementById('modalRegenerarPin').classList.add('hidden');
    }
</script>
@endsection
