@extends('layouts.empleado')

@section('title', 'Mis Puntos y Beneficios')
@section('page_title', 'Incentivos y Beneficios')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto px-4 sm:px-6 md:px-8 py-6">

    <!-- BANNER HERO DEL SISTEMA DE PUNTOS -->
    <div class="relative overflow-hidden rounded-3xl bg-linear-to-r from-brand-dark via-brand-navy to-brand-blue text-white p-6 sm:p-8 shadow-sm">
        <!-- Olas decorativas de fondo -->
        <div class="absolute -right-10 -bottom-10 opacity-10 pointer-events-none">
            <i class="fa-solid fa-gift text-9xl"></i>
        </div>
        <div class="absolute right-24 -top-8 opacity-10 pointer-events-none">
            <i class="fa-solid fa-star text-8xl"></i>
        </div>

        <div class="relative z-10 grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
            <div class="md:col-span-2 space-y-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-400/20 text-amber-300 border border-amber-400/30 text-xs font-bold uppercase tracking-wider">
                    <i class="fa-solid fa-star text-[11px]"></i>
                    Programa de Puntos Acumulables
                </span>
                <h1 class="font-heading font-extrabold text-2xl sm:text-3xl text-white">
                    ¡Aprende, acumula puntos y desbloquea beneficios!
                </h1>
                <p class="text-xs sm:text-sm text-slate-200/90 max-w-2xl leading-relaxed">
                    Cada capacitación completada satisfactoriamente te otorga puntos automáticos. Acumúlalos para canjear recompensas e incentivos pensados para ti en C.I. Piscícola New York.
                </p>
            </div>

            <!-- TARJETA SALDO DESTACADA -->
            <div class="bg-white/10 backdrop-blur-md rounded-2xl border border-white/20 p-5 text-center sm:text-left flex flex-col justify-between shadow-lg">
                <span class="text-xs font-bold text-slate-300 uppercase tracking-wider block">Tu Saldo Disponible</span>
                <div class="my-2 flex items-baseline justify-center sm:justify-start gap-2">
                    <span class="font-heading font-extrabold text-4xl sm:text-5xl text-amber-300 tracking-tight">
                        {{ number_format($usuario->puntos_saldo) }}
                    </span>
                    <span class="text-sm font-bold text-amber-200 uppercase">Puntos</span>
                </div>
                <div class="pt-2 border-t border-white/15 flex items-center justify-between text-[11px] text-slate-300">
                    <span>Ganados: <strong>{{ number_format($puntosGanadosHistoricos) }}</strong></span>
                    <span>Canjeados: <strong>{{ number_format($puntosCanjeadosHistoricos) }}</strong></span>
                </div>
            </div>
        </div>
    </div>

    <!-- NOTIFICACIONES FLASH -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl flex items-center gap-3 text-sm">
            <i class="fa-solid fa-circle-check text-emerald-600 text-lg shrink-0"></i>
            <div>
                <span class="font-bold block">¡Operación exitosa!</span>
                <span class="text-xs">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl flex items-center gap-3 text-sm">
            <i class="fa-solid fa-triangle-exclamation text-rose-600 text-lg shrink-0"></i>
            <div>
                <span class="font-bold block">Atención</span>
                <span class="text-xs">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- CATÁLOGO DE BENEFICIOS -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-heading font-extrabold text-lg text-brand-dark flex items-center gap-2">
                    <i class="fa-solid fa-gift text-amber-500"></i>
                    Catálogo de Beneficios
                </h2>
                <p class="text-xs text-muted">Revisa tus beneficios disponibles y tu avance hacia los próximos desbloqueos.</p>
            </div>
            <span class="text-xs font-bold text-muted bg-slate-100 px-3 py-1.5 rounded-full">
                {{ $beneficios->count() }} beneficios activos
            </span>
        </div>

        @if($beneficios->isEmpty())
            <div class="bg-white rounded-3xl border border-line/80 p-8 text-center space-y-3">
                <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-2xl mx-auto">
                    <i class="fa-solid fa-box-open"></i>
                </div>
                <h3 class="font-heading font-bold text-base text-brand-dark">Próximamente beneficios disponibles</h3>
                <p class="text-xs text-muted max-w-sm mx-auto">La administración está configurando nuevos beneficios. ¡Sigue completando capacitaciones para acumular más puntos!</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($beneficios as $b)
                    @php
                        $estado = $b->estadoParaUsuario($usuario);
                        $porcentaje = $b->porcentajeProgreso($usuario);
                        $canjeRealizado = $canjes->firstWhere('beneficio_id', $b->id);
                    @endphp
                    <div class="bg-white rounded-3xl border {{ $estado === 'DISPONIBLE' ? 'border-amber-400 ring-2 ring-amber-300/40' : ($estado === 'CANJEADO' ? 'border-emerald-300 bg-emerald-50/20' : 'border-line/80') }} p-5 shadow-2xs flex flex-col justify-between transition-all duration-200 hover:shadow-xs">
                        <div class="space-y-3">
                            <!-- Cabecera de la tarjeta -->
                            <div class="flex items-start justify-between gap-3">
                                <div class="w-12 h-12 rounded-2xl {{ $estado === 'CANJEADO' ? 'bg-emerald-100 text-emerald-700' : ($estado === 'DISPONIBLE' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-500') }} flex items-center justify-center text-xl shrink-0">
                                    <i class="fa-solid {{ $b->icono ?: 'fa-gift' }}"></i>
                                </div>

                                <!-- Badge de estado -->
                                @if($estado === 'CANJEADO')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-emerald-100 text-emerald-800">
                                        <i class="fa-solid fa-circle-check text-[10px]"></i>
                                        Canjeado
                                    </span>
                                @elseif($estado === 'DISPONIBLE')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-amber-100 text-amber-800 animate-pulse">
                                        <i class="fa-solid fa-unlock text-[10px]"></i>
                                        ¡Disponible!
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-slate-100 text-slate-600">
                                        <i class="fa-solid fa-lock text-[10px]"></i>
                                        Pendiente
                                    </span>
                                @endif
                            </div>

                            <!-- Título y descripción -->
                            <div>
                                <h3 class="font-heading font-extrabold text-base text-brand-dark leading-snug">{{ $b->nombre }}</h3>
                                @if($b->descripcion)
                                    <p class="text-xs text-muted mt-1 leading-relaxed line-clamp-3">{{ $b->descripcion }}</p>
                                @endif
                            </div>

                            <!-- Requisito de puntos -->
                            <div class="flex items-center justify-between text-xs pt-1">
                                <span class="text-muted font-medium">Requisito:</span>
                                <span class="inline-flex items-center gap-1 font-heading font-extrabold text-brand-dark">
                                    <i class="fa-solid fa-star text-amber-500 text-xs"></i>
                                    {{ number_format($b->puntos_requeridos) }} pts
                                </span>
                            </div>

                            <!-- Barra visual de progreso -->
                            <div class="space-y-1.5 pt-1">
                                <div class="flex items-center justify-between text-[11px]">
                                    <span class="text-muted font-medium">
                                        {{ min($usuario->puntos_saldo, $b->puntos_requeridos) }} de {{ $b->puntos_requeridos }} puntos
                                    </span>
                                    <span class="font-bold {{ $porcentaje >= 100 ? 'text-emerald-600' : 'text-slate-600' }}">
                                        {{ $porcentaje }}%
                                    </span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                                    <div class="h-2.5 rounded-full transition-all duration-500 {{ $porcentaje >= 100 ? 'bg-linear-to-r from-amber-400 to-emerald-500' : 'bg-linear-to-r from-brand-blue to-amber-500' }}"
                                         style="width: {{ $porcentaje }}%"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Acción o Estado final -->
                        <div class="pt-4 mt-3 border-t border-line/60">
                            @if($estado === 'CANJEADO')
                                <div class="bg-emerald-50 border border-emerald-200/80 rounded-xl p-2.5 text-center">
                                    <span class="block text-[10px] uppercase font-bold text-emerald-800 tracking-wider">Código de Canje</span>
                                    <span class="font-mono font-extrabold text-xs text-emerald-900 tracking-widest">{{ $canjeRealizado->codigo_canje ?? 'CNJ-OK' }}</span>
                                    <span class="block text-[10px] text-emerald-700 mt-0.5">Presenta este código a Gestión Humana</span>
                                </div>
                            @elseif($estado === 'DISPONIBLE')
                                <button type="button"
                                        onclick="abrirModalCanje('{{ $b->id }}', '{{ addslashes($b->nombre) }}', {{ $b->puntos_requeridos }})"
                                        class="w-full py-2.5 px-4 rounded-xl bg-linear-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white font-heading font-extrabold text-xs shadow-2xs hover:shadow-xs transition transform hover:scale-101 flex items-center justify-center gap-2 cursor-pointer">
                                    <i class="fa-solid fa-gift"></i>
                                    <span>Reclamar Beneficio</span>
                                </button>
                            @else
                                <div class="text-center py-2 px-3 rounded-xl bg-slate-50 border border-slate-200/60 text-slate-500 text-xs font-medium flex items-center justify-center gap-1.5">
                                    <i class="fa-solid fa-lock text-[11px]"></i>
                                    <span>Te faltan {{ $b->puntos_requeridos - $usuario->puntos_saldo }} pts</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- SECCIÓN INFERIOR: HISTORIAL DE PUNTOS Y CURSOS COMPLETADOS -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">

        <!-- 1. CAPACITACIONES COMPLETADAS Y PUNTOS OBTENIDOS -->
        <div class="bg-white rounded-3xl border border-line/80 shadow-2xs p-5 space-y-4">
            <div class="flex items-center justify-between border-b border-line/60 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-blue-50 text-brand-blue flex items-center justify-center text-sm font-bold">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                    <div>
                        <h3 class="font-heading font-extrabold text-sm text-brand-dark">Capacitaciones Completadas</h3>
                        <p class="text-[11px] text-muted">Puntos ganados por cada capacitación culminada</p>
                    </div>
                </div>
                <span class="text-xs font-bold text-brand-blue bg-blue-50 px-2.5 py-1 rounded-full">
                    {{ $capacitacionesCompletadas->count() }} cursos
                </span>
            </div>

            @if($capacitacionesCompletadas->isEmpty())
                <div class="text-center py-8 text-muted space-y-2">
                    <i class="fa-solid fa-circle-info text-2xl text-slate-300"></i>
                    <p class="text-xs">Aún no has completado capacitaciones.<br>Inicia tus capacitaciones asignadas para ganar puntos.</p>
                </div>
            @else
                <div class="divide-y divide-line/60 max-h-96 overflow-y-auto pr-1">
                    @foreach($capacitacionesCompletadas as $cc)
                        <div class="py-3 flex items-center justify-between gap-3 hover:bg-slate-50/60 rounded-xl px-2 transition">
                            <div class="min-w-0 flex-1">
                                <h4 class="font-heading font-bold text-xs text-brand-dark truncate">
                                    {{ $cc->capacitacion->titulo ?? ($cc->capacitacion->nombre ?? 'Capacitación') }}
                                </h4>
                                <div class="flex items-center gap-2 text-[10px] text-muted mt-0.5">
                                    <span><i class="fa-regular fa-calendar-check text-[10px]"></i> {{ $cc->fecha_finalizacion ? $cc->fecha_finalizacion->format('d/m/Y') : ($cc->fecha_completado ? $cc->fecha_completado->format('d/m/Y') : ($cc->updated_at ? $cc->updated_at->format('d/m/Y') : 'Completado')) }}</span>
                                    <span>•</span>
                                    <span>{{ $cc->capacitacion->categoria ?? 'General' }}</span>
                                </div>
                            </div>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 text-xs font-heading font-extrabold shrink-0 border border-emerald-200/60">
                                <i class="fa-solid fa-plus text-[10px]"></i>
                                {{ $cc->capacitacion->puntos ?? 20 }} pts
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- 2. REGISTRO AUDITADO DE MOVIMIENTOS DE PUNTOS -->
        <div class="bg-white rounded-3xl border border-line/80 shadow-2xs p-5 space-y-4">
            <div class="flex items-center justify-between border-b border-line/60 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-sm font-bold">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </div>
                    <div>
                        <h3 class="font-heading font-extrabold text-sm text-brand-dark">Historial de Movimientos</h3>
                        <p class="text-[11px] text-muted">Acreditaciones y canjes con registro de saldo</p>
                    </div>
                </div>
                <span class="text-xs font-bold text-purple-600 bg-purple-50 px-2.5 py-1 rounded-full">
                    Auditoría
                </span>
            </div>

            @if($movimientos->isEmpty())
                <div class="text-center py-8 text-muted space-y-2">
                    <i class="fa-solid fa-receipt text-2xl text-slate-300"></i>
                    <p class="text-xs">No hay movimientos registrados en tu cuenta aún.</p>
                </div>
            @else
                <div class="divide-y divide-line/60 max-h-96 overflow-y-auto pr-1">
                    @foreach($movimientos as $m)
                        <div class="py-3 flex items-center justify-between gap-3 hover:bg-slate-50/60 rounded-xl px-2 transition">
                            <div class="flex items-start gap-2.5 min-w-0 flex-1">
                                <div class="w-8 h-8 rounded-lg {{ $m->tipo === 'ACREDITACION' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }} flex items-center justify-center text-xs shrink-0 mt-0.5">
                                    <i class="fa-solid {{ $m->tipo === 'ACREDITACION' ? 'fa-arrow-down' : 'fa-arrow-up' }}"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-bold text-brand-dark leading-snug truncate">{{ $m->motivo }}</p>
                                    <p class="text-[10px] text-muted mt-0.5">
                                        {{ $m->created_at->format('d/m/Y H:i') }} • Saldo resultante: <strong>{{ $m->saldo_nuevo }} pts</strong>
                                    </p>
                                </div>
                            </div>

                            <span class="font-heading font-extrabold text-xs shrink-0 {{ $m->tipo === 'ACREDITACION' ? 'text-emerald-600' : 'text-amber-600' }}">
                                {{ $m->tipo === 'ACREDITACION' ? '+' : '-' }}{{ $m->puntos }} pts
                            </span>
                        </div>
                    @endforeach
                </div>

                @if($movimientos->hasPages())
                    <div class="pt-2 border-t border-line/60">
                        {{ $movimientos->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>

<!-- MODAL DE CONFIRMACIÓN DE CANJE -->
<div id="modal-canje" class="fixed inset-0 z-50 hidden bg-brand-dark/50 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-line space-y-4 animate-in fade-in zoom-in-95 duration-150">
        <div class="text-center space-y-2">
            <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-2xl mx-auto shadow-2xs">
                <i class="fa-solid fa-gift"></i>
            </div>
            <h3 class="font-heading font-extrabold text-lg text-brand-dark">Confirmar Canje de Beneficio</h3>
            <p class="text-xs text-muted">Estás a punto de canjear tus puntos acumulados por este beneficio.</p>
        </div>

        <div class="bg-slate-50 rounded-2xl p-4 border border-line/80 space-y-2">
            <div class="flex items-center justify-between text-xs">
                <span class="text-muted">Beneficio:</span>
                <span id="modal-canje-nombre" class="font-bold text-brand-dark text-right"></span>
            </div>
            <div class="flex items-center justify-between text-xs">
                <span class="text-muted">Puntos a descontar:</span>
                <span id="modal-canje-puntos" class="font-bold text-amber-600"></span>
            </div>
            <div class="flex items-center justify-between text-xs pt-2 border-t border-line/60">
                <span class="text-muted">Tu saldo después del canje:</span>
                <span id="modal-canje-saldo-restante" class="font-bold text-brand-blue"></span>
            </div>
        </div>

        <form id="form-canje-confirmar" method="POST" action="">
            @csrf
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="cerrarModalCanje()" class="flex-1 py-2.5 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                    Cancelar
                </button>
                <button type="submit" class="flex-1 py-2.5 px-4 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-heading font-extrabold text-xs shadow-2xs transition flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-check"></i>
                    <span>Confirmar Canje</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const saldoActual = {{ (int) $usuario->puntos_saldo }};

function abrirModalCanje(beneficioId, nombre, puntos) {
    document.getElementById('modal-canje-nombre').textContent = nombre;
    document.getElementById('modal-canje-puntos').textContent = '-' + puntos + ' pts';
    const restante = Math.max(0, saldoActual - puntos);
    document.getElementById('modal-canje-saldo-restante').textContent = restante + ' pts';

    const form = document.getElementById('form-canje-confirmar');
    form.action = "{{ url('empleado/beneficios') }}/" + beneficioId + "/canjear";

    document.getElementById('modal-canje').classList.remove('hidden');
}

function cerrarModalCanje() {
    document.getElementById('modal-canje').classList.add('hidden');
}

// Cerrar con Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        cerrarModalCanje();
    }
});
</script>
@endsection
