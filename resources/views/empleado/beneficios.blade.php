@extends('layouts.empleado')

@section('title', 'Mis Puntos y Beneficios')
@section('page_title', 'Incentivos y Beneficios')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto px-4 sm:px-6 md:px-8 py-6">

    <!-- BANNER HERO DEL SISTEMA DE PUNTOS -->
    <div style="background: linear-gradient(135deg, #002d5e 0%, #003d80 50%, #0056b3 100%);"
         class="relative overflow-hidden rounded-3xl text-white p-5 sm:p-8 shadow-sm w-full max-w-full">
        <!-- Olas decorativas de fondo -->
        <div class="absolute -right-10 -bottom-10 opacity-10 pointer-events-none">
            <i class="fa-solid fa-gift text-9xl"></i>
        </div>
        <div class="absolute right-24 -top-8 opacity-10 pointer-events-none">
            <i class="fa-solid fa-star text-8xl"></i>
        </div>

        <div class="relative z-10 grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
            <div class="md:col-span-2 space-y-2.5 min-w-0">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-400/20 text-amber-300 border border-amber-400/30 text-xs font-bold uppercase tracking-wider">
                    <i class="fa-solid fa-star text-[11px]"></i>
                    Programa de Puntos Acumulables
                </span>
                <h1 class="font-heading font-extrabold text-xl sm:text-2xl md:text-3xl text-white leading-tight break-words">
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
                        $canjeRealizado = $canjes->firstWhere('beneficio_id', $b->id)
                            ?? \App\Models\CanjeBeneficio::where('usuario_id', $usuario->id)->where('beneficio_id', $b->id)->latest()->first();
                    @endphp
                    <div class="bg-white rounded-3xl border {{ $estado === 'DISPONIBLE' ? 'border-amber-400 ring-2 ring-amber-300/40' : ($estado === 'CANJEADO' ? 'border-emerald-300 bg-emerald-50/20' : ($estado === 'SOLICITADO' ? 'border-blue-300 ring-2 ring-blue-300/40 bg-blue-50/20' : 'border-line/80')) }} p-5 shadow-2xs flex flex-col justify-between transition-all duration-200 hover:shadow-xs">
                        <div class="space-y-3">
                            <!-- Cabecera de la tarjeta -->
                            <div class="flex items-start justify-between gap-3">
                                <div class="w-12 h-12 rounded-2xl {{ $estado === 'CANJEADO' ? 'bg-emerald-100 text-emerald-700' : ($estado === 'SOLICITADO' ? 'bg-blue-100 text-blue-700' : ($estado === 'DISPONIBLE' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-500')) }} flex items-center justify-center text-xl shrink-0">
                                    <i class="fa-solid {{ $b->icono ?: 'fa-gift' }}"></i>
                                </div>

                                <!-- Badge de estado -->
                                @if($estado === 'CANJEADO')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        <i class="fa-solid fa-circle-check text-[10px]"></i>
                                        Canjeado
                                    </span>
                                @elseif($estado === 'SOLICITADO')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-blue-100 text-blue-800 border border-blue-200 animate-pulse">
                                        <i class="fa-solid fa-clock text-[10px]"></i>
                                        Por Reclamar
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
                                        @if($estado === 'CANJEADO' || $estado === 'SOLICITADO')
                                            {{ $b->puntos_requeridos }} de {{ $b->puntos_requeridos }} puntos
                                        @else
                                            {{ min($usuario->puntos_saldo, $b->puntos_requeridos) }} de {{ $b->puntos_requeridos }} puntos
                                        @endif
                                    </span>
                                    <span class="font-bold {{ $porcentaje >= 100 ? 'text-emerald-600' : 'text-slate-600' }}">
                                        {{ $porcentaje }}%
                                    </span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                                    <div class="h-2.5 rounded-full transition-all duration-500 {{ $porcentaje >= 100 ? 'bg-linear-to-r from-emerald-500 to-teal-500' : 'bg-linear-to-r from-brand-blue to-amber-500' }}"
                                         style="width: {{ $porcentaje }}%"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Acción o Estado final -->
                        <div class="pt-4 mt-3 border-t border-line/60">
                            @if($estado === 'CANJEADO' && $canjeRealizado)
                                <div class="bg-emerald-50/90 border border-emerald-200 rounded-2xl p-3.5 text-center space-y-2.5 shadow-2xs">
                                    <div class="flex items-center justify-center gap-1.5 text-emerald-800 text-[11px] font-bold uppercase tracking-wider">
                                        <i class="fa-solid fa-circle-check text-emerald-600 text-xs"></i>
                                        <span>Beneficio Canjeado</span>
                                    </div>
                                    <button type="button"
                                            onclick="mostrarQrPorCanje({{ $canjeRealizado->id }})"
                                            style="background-color: #059669 !important; color: #ffffff !important;"
                                            class="w-full py-2.5 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-heading font-extrabold text-xs shadow-xs hover:shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer">
                                        <i class="fa-solid fa-qrcode text-sm text-white"></i>
                                        <span class="text-white font-bold">Ver mi Código QR</span>
                                    </button>
                                    <span class="block text-[10px] text-emerald-700 font-medium">
                                        Recibido el {{ $canjeRealizado->fecha_entrega ? $canjeRealizado->fecha_entrega->format('d/m/Y H:i') : ($canjeRealizado->fecha_canje ? $canjeRealizado->fecha_canje->format('d/m/Y H:i') : '—') }}
                                    </span>
                                </div>
                            @elseif($estado === 'SOLICITADO' && $canjeRealizado)
                                <div class="bg-blue-50/90 border border-blue-200 rounded-2xl p-3.5 text-center space-y-2.5 shadow-2xs">
                                    <div class="flex items-center justify-center gap-1.5 text-blue-900 text-[11px] font-bold uppercase tracking-wider">
                                        <i class="fa-solid fa-clock text-brand-blue text-xs"></i>
                                        <span>Pendiente de Entrega</span>
                                    </div>
                                    <button type="button"
                                            onclick="mostrarQrPorCanje({{ $canjeRealizado->id }})"
                                            style="background-color: #0056b3 !important; color: #ffffff !important;"
                                            class="w-full py-2.5 px-3 rounded-xl bg-brand-blue hover:bg-brand-dark text-white font-heading font-extrabold text-xs shadow-xs hover:shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer">
                                        <i class="fa-solid fa-qrcode text-sm text-white"></i>
                                        <span class="text-white font-bold">Presentar mi Código QR</span>
                                    </button>
                                    <span class="block text-[10.5px] text-blue-800 font-medium">Presenta este QR a Gestión Humana para confirmar</span>
                                </div>
                            @elseif($estado === 'DISPONIBLE')
                                <button type="button"
                                        onclick="abrirModalCanje('{{ $b->id }}', '{{ addslashes($b->nombre) }}', {{ $b->puntos_requeridos }})"
                                        style="background-color: #d97706 !important; color: #ffffff !important;"
                                        class="w-full py-2.5 px-4 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-heading font-extrabold text-xs shadow-xs hover:shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer">
                                    <i class="fa-solid fa-gift text-sm text-white"></i>
                                    <span class="text-white font-bold">Reclamar Beneficio</span>
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

<!-- MODAL DE VISUALIZACIÓN DE CÓDIGO QR -->
<div id="modal-qr-canje" class="fixed inset-0 z-50 hidden bg-brand-dark/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-sm sm:max-w-md w-full p-6 shadow-2xl border border-line space-y-4 animate-in fade-in zoom-in-95 duration-150 relative max-h-[92vh] overflow-y-auto">
        <!-- Botón cerrar X -->
        <button type="button" onclick="cerrarModalQrCanje()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 transition p-2">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>

        <div class="text-center space-y-1.5">
            <div class="w-14 h-14 rounded-2xl bg-linear-to-br from-emerald-500 to-teal-600 text-white flex items-center justify-center text-2xl mx-auto shadow-md">
                <i class="fa-solid fa-qrcode"></i>
            </div>
            <h3 class="font-heading font-extrabold text-lg text-brand-dark">Código QR del Beneficio</h3>
            <p class="text-xs text-muted max-w-xs mx-auto">
                Escanéalo con la cámara de cualquier teléfono celular o preséntalo directamente en Gestión Humana.
            </p>
        </div>

        <!-- Contenedor del QR -->
        <div class="flex flex-col items-center justify-center p-5 bg-white rounded-2xl border-2 border-dashed border-emerald-300 shadow-inner">
            <div id="qr-target" class="flex items-center justify-center bg-white p-2 rounded-xl"></div>
            <div class="mt-3 flex items-center gap-1.5 text-[11px] font-bold text-emerald-700 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-200">
                <i class="fa-solid fa-circle-check text-[10px]"></i>
                <span id="modal-qr-codigo-label">QR Único</span>
            </div>

            <!-- Indicador de URL pública / Ngrok para escaneo móvil -->
            <div class="mt-2.5 flex items-center justify-between gap-2 px-3 py-1.5 bg-slate-50 border border-slate-200/90 rounded-xl text-[10.5px] w-full max-w-[320px]">
                <div class="flex items-center gap-1.5 min-w-0">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse shrink-0"></span>
                    <span class="font-bold text-slate-700 shrink-0">URL:</span>
                    <span id="modal-qr-url-badge" class="text-slate-600 truncate font-mono text-[10px]"></span>
                </div>
                <button type="button" onclick="configurarNgrokCanje()" class="text-cyan-700 hover:text-cyan-900 font-bold shrink-0 text-[10px] flex items-center gap-1 hover:underline cursor-pointer" title="Configurar o cambiar URL pública de Ngrok">
                    <i class="fa-solid fa-bolt text-amber-500"></i>
                    <span>Túnel</span>
                </button>
            </div>
        </div>

        <!-- Ficha de detalles del canje codificados en el QR -->
        <div class="bg-slate-50 rounded-2xl p-4 border border-line/80 space-y-2 text-xs">
            <div class="flex items-start justify-between gap-3">
                <span class="text-muted font-medium shrink-0">Colaborador:</span>
                <span id="modal-qr-colaborador" class="font-bold text-brand-dark text-right"></span>
            </div>
            <div class="flex items-start justify-between gap-3">
                <span class="text-muted font-medium shrink-0">Área:</span>
                <span id="modal-qr-area" class="font-bold text-brand-dark text-right"></span>
            </div>
            <div class="flex items-start justify-between gap-3">
                <span class="text-muted font-medium shrink-0">Beneficio:</span>
                <span id="modal-qr-beneficio" class="font-bold text-emerald-700 text-right"></span>
            </div>
            <div class="flex items-start justify-between gap-3 pt-2 border-t border-line/60">
                <span class="text-muted font-medium shrink-0">Fecha de Canje:</span>
                <span id="modal-qr-fecha" class="font-semibold text-slate-600 text-right"></span>
            </div>
            <div class="flex items-start justify-between gap-3 pt-2 border-t border-line/60">
                <span class="text-muted font-medium shrink-0">Estado:</span>
                <span id="modal-qr-estado-badge" class="font-bold text-right"></span>
            </div>
        </div>

        <!-- Botones de Acción -->
        <div class="flex flex-col sm:flex-row gap-2.5 pt-1">
            <button type="button" onclick="descargarImagenQr()" class="flex-1 py-2.5 px-4 rounded-xl bg-linear-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-heading font-extrabold text-xs shadow-2xs hover:shadow-xs transition flex items-center justify-center gap-2 cursor-pointer">
                <i class="fa-solid fa-download"></i>
                <span>Descargar Imagen QR</span>
            </button>
            <button type="button" onclick="cerrarModalQrCanje()" class="py-2.5 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                Cerrar
            </button>
        </div>
    </div>
</div>

<!-- Script QRCode -->
<script src="{{ asset('qrcode.min.js') }}"></script>
<script>
if (typeof QRCode === 'undefined') {
    document.write('<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"><\/script>');
}
</script>

<script>
const saldoActual = {{ (int) $usuario->puntos_saldo }};
let currentQrInstance = null;
let currentQrData = '';
let currentQrNombre = '';
let currentCanjeActivo = null;

// Base Ngrok detectada automáticamente por el servidor o guardada en local
let URL_NGROK_BASE = localStorage.getItem('pny_ngrok_url') || @json($urlNgrokBase) || '';

// Si estamos navegando a través de un túnel ngrok directamente en el navegador
if (window.location.hostname.includes('ngrok')) {
    URL_NGROK_BASE = window.location.origin;
}

// Almacén seguro de canjes para evitar errores de sintaxis en atributos HTML
window.canjesUsuario = {
    @foreach($canjes as $c)
        {{ $c->id }}: {
            id: {{ $c->id }},
            beneficio: @js($c->beneficio?->nombre ?? 'Beneficio'),
            codigo: @js($c->codigo_canje),
            url_verificacion: @js($c->url_verificacion),
            colaborador: @js($usuario->nombre_completo),
            area: @js($usuario->area?->nombre ?? ($usuario->sedeActual()?->nombre ?? 'Sin área asignada')),
            fecha: @js($c->fecha_canje ? $c->fecha_canje->format('d/m/Y H:i') : now()->format('d/m/Y H:i')),
            estado: @js($c->estado),
            fecha_entrega: @js($c->fecha_entrega ? $c->fecha_entrega->format('d/m/Y H:i') : null)
        },
    @endforeach
};

function resolverUrlCanje(codigo, fallbackUrl) {
    if (URL_NGROK_BASE) {
        return URL_NGROK_BASE.replace(/\/+$/, '') + '/canje/verificar/' + codigo;
    }
    if (window.location.hostname.includes('ngrok')) {
        return window.location.origin + '/canje/verificar/' + codigo;
    }
    if (fallbackUrl && !fallbackUrl.includes('.test')) {
        return fallbackUrl;
    }
    return window.location.origin + '/canje/verificar/' + codigo;
}

function mostrarQrPorCanje(canjeId) {
    const data = window.canjesUsuario ? window.canjesUsuario[canjeId] : null;
    if (!data) {
        console.error('Canje no encontrado para ID:', canjeId);
        return;
    }
    currentCanjeActivo = data;
    const urlFinal = resolverUrlCanje(data.codigo, data.url_verificacion);
    abrirModalQrCanje(
        data.beneficio,
        data.codigo,
        urlFinal,
        data.colaborador,
        data.area,
        data.fecha,
        data.estado,
        data.fecha_entrega
    );
}

function configurarNgrokCanje() {
    const actual = URL_NGROK_BASE || 'https://xxxx.ngrok-free.dev';
    const nueva = prompt('Ingresa la URL pública de Ngrok para escanear con celulares:', actual);
    if (nueva !== null) {
        const limpia = nueva.trim().replace(/\/+$/, '');
        if (limpia) {
            URL_NGROK_BASE = limpia;
            localStorage.setItem('pny_ngrok_url', limpia);
        } else {
            URL_NGROK_BASE = '';
            localStorage.removeItem('pny_ngrok_url');
        }
        if (currentCanjeActivo) {
            mostrarQrPorCanje(currentCanjeActivo.id);
        }
    }
}

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

function abrirModalQrCanje(beneficioNombre, codigo, qrUrl, colaborador, area, fecha, estado, fechaEntrega) {
    document.getElementById('modal-qr-beneficio').textContent = beneficioNombre;
    document.getElementById('modal-qr-codigo-label').textContent = codigo;
    document.getElementById('modal-qr-colaborador').textContent = colaborador;
    document.getElementById('modal-qr-area').textContent = area;
    document.getElementById('modal-qr-fecha').textContent = fecha;

    const estadoBadge = document.getElementById('modal-qr-estado-badge');
    if (estadoBadge) {
        if (estado === 'CANJEADO') {
            const fechaTxt = fechaEntrega ? ` (${fechaEntrega})` : '';
            estadoBadge.innerHTML = `<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10.5px] bg-emerald-100 text-emerald-800 font-extrabold border border-emerald-200"><i class="fa-solid fa-circle-check"></i> Canjeado y Recibido${fechaTxt}</span>`;
        } else {
            estadoBadge.innerHTML = `<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10.5px] bg-blue-100 text-blue-800 font-extrabold border border-blue-200 animate-pulse"><i class="fa-solid fa-clock"></i> Pendiente de Entrega</span>`;
        }
    }

    const urlBadge = document.getElementById('modal-qr-url-badge');
    if (urlBadge) {
        urlBadge.textContent = qrUrl;
        urlBadge.title = qrUrl;
    }

    currentQrData = qrUrl;
    currentQrNombre = beneficioNombre;

    // Mostrar modal primero para que el contenedor esté visible y renderice correctamente
    document.getElementById('modal-qr-canje').classList.remove('hidden');

    const qrContainer = document.getElementById('qr-target');
    qrContainer.innerHTML = '';

    // Generar el código QR con la URL pública
    if (typeof QRCode !== 'undefined') {
        try {
            currentQrInstance = new QRCode(qrContainer, {
                text: qrUrl,
                width: 200,
                height: 200,
                colorDark: "#002d5e",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.M
            });
        } catch (e1) {
            console.warn('Error en QRCode local, usando renderizador alternativo:', e1);
            qrContainer.innerHTML = `<img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(qrUrl)}" alt="Código QR Canje" class="w-[200px] h-[200px] object-contain rounded-lg">`;
        }
    } else {
        qrContainer.innerHTML = `<img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(qrUrl)}" alt="Código QR Canje" class="w-[200px] h-[200px] object-contain rounded-lg">`;
    }
}

function cerrarModalQrCanje() {
    document.getElementById('modal-qr-canje').classList.add('hidden');
}

function descargarImagenQr() {
    const qrContainer = document.getElementById('qr-target');
    const img = qrContainer.querySelector('img');
    const canvas = qrContainer.querySelector('canvas');
    let dataUrl = null;

    if (canvas) {
        dataUrl = canvas.toDataURL("image/png");
    } else if (img && img.src) {
        dataUrl = img.src;
    }

    if (dataUrl) {
        const link = document.createElement('a');
        link.download = 'Canje-QR-' + (currentQrNombre.replace(/\s+/g, '-')) + '.png';
        link.href = dataUrl;
        link.click();
    }
}

// Cerrar con Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        cerrarModalCanje();
        cerrarModalQrCanje();
    }
});

// Si recién se confirmó un canje con éxito, abrir inmediatamente el modal con el QR generado
@if(session('canje_exitoso'))
document.addEventListener('DOMContentLoaded', function() {
    const c = @js(session('canje_exitoso'));
    if (c) {
        currentCanjeActivo = c;
        const urlFinal = resolverUrlCanje(c.codigo, c.url_verificacion);
        abrirModalQrCanje(
            c.beneficio,
            c.codigo,
            urlFinal,
            c.nombre,
            c.area,
            c.fecha
        );
    }
});
@endif
</script>
@endsection
