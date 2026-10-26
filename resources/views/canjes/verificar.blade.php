<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Canje · C.I. Piscícola New York</title>
    <link rel="icon" type="image/png" href="{{ asset('images/Logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full font-sans text-slate-800 bg-linear-to-br from-slate-50 via-sky-50/40 to-slate-100 flex flex-col justify-between p-4 sm:p-6 antialiased selection:bg-cyan-500/30 selection:text-cyan-900">

    <div class="max-w-lg w-full mx-auto my-auto space-y-4">
        
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-300 text-emerald-900 rounded-2xl p-4 shadow-sm flex items-start gap-3 animate-in fade-in zoom-in-95 duration-200">
                <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 mt-0.5">
                    <i class="fa-solid fa-circle-check text-base"></i>
                </div>
                <div class="text-xs">
                    <strong class="font-heading font-extrabold block text-sm text-emerald-950">¡Operación Exitosa!</strong>
                    <p class="mt-0.5 leading-relaxed">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('info'))
            <div class="bg-amber-50 border border-amber-300 text-amber-900 rounded-2xl p-4 shadow-sm flex items-start gap-3">
                <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0 mt-0.5">
                    <i class="fa-solid fa-circle-info text-base"></i>
                </div>
                <div class="text-xs">
                    <strong class="font-heading font-extrabold block text-sm text-amber-950">Información</strong>
                    <p class="mt-0.5 leading-relaxed">{{ session('info') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-rose-50 border border-rose-300 text-rose-900 rounded-2xl p-4 shadow-sm flex items-start gap-3">
                <div class="w-8 h-8 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center shrink-0 mt-0.5">
                    <i class="fa-solid fa-triangle-exclamation text-base"></i>
                </div>
                <div class="text-xs">
                    <strong class="font-heading font-extrabold block text-sm text-rose-950">Atención</strong>
                    <p class="mt-0.5 leading-relaxed">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <!-- Tarjeta Principal de Verificación -->
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xl overflow-hidden">
            
            <!-- Cabecera Corporativa -->
            <div class="bg-linear-to-r from-[#002d5e] via-[#004085] to-[#0284c7] p-6 text-white text-center relative overflow-hidden">
                <div class="absolute -right-10 -bottom-10 w-36 h-36 rounded-full bg-white/10 pointer-events-none blur-xl"></div>
                <div class="absolute -left-10 -top-10 w-36 h-36 rounded-full bg-cyan-400/20 pointer-events-none blur-xl"></div>

                <div class="relative z-10 flex flex-col items-center">
                    <div class="h-14 w-14 p-1.5 bg-white rounded-2xl shadow-md flex items-center justify-center mb-3">
                        <img src="{{ asset('images/Logo.png') }}" alt="Logo PNY" class="h-full w-full object-contain">
                    </div>
                    <span class="text-[11px] font-extrabold uppercase tracking-widest text-cyan-200">C.I. Piscícola New York</span>
                    <h1 class="font-heading font-extrabold text-xl sm:text-2xl mt-0.5">Validación de Canje</h1>
                    <p class="text-xs text-white/80 mt-1">Portal Oficial de Capacitaciones e Incentivos</p>
                </div>
            </div>

            <!-- Contenido de Validación -->
            <div class="p-6 space-y-5">
                @if($canje)
                    
                    @if($canje->estado === 'CANJEADO')
                        <!-- ESTADO: YA CANJEADO / RECIBIDO PREVIAMENTE -->
                        <div class="bg-amber-50/90 border-2 border-amber-300 rounded-3xl p-5 text-center space-y-2 shadow-xs">
                            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-amber-100 text-amber-700 text-2xl shadow-xs mb-1">
                                <i class="fa-solid fa-box-archive"></i>
                            </div>
                            <span class="inline-block px-3 py-1 rounded-full text-[10.5px] font-extrabold uppercase bg-amber-200 text-amber-900 tracking-wider">
                                Código Ya Procesado
                            </span>
                            <h2 class="font-heading font-extrabold text-amber-950 text-lg sm:text-xl">
                                Este incentivo ya fue canjeado o recibido
                            </h2>
                            <p class="text-xs text-amber-800 leading-relaxed max-w-sm mx-auto">
                                Este beneficio fue entregado y no puede ser canjeado nuevamente. A continuación se presentan los detalles del registro oficial:
                            </p>

                            <!-- Badge con fecha y responsable de entrega -->
                            <div class="mt-3 pt-3 border-t border-amber-200 text-xs text-amber-900 space-y-2">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-amber-700 font-medium">Fecha de Entrega:</span>
                                    <strong class="font-bold">{{ $canje->fecha_entrega ? $canje->fecha_entrega->format('d/m/Y h:i A') : ($canje->updated_at ? $canje->updated_at->format('d/m/Y h:i A') : 'Registrado') }}</strong>
                                </div>

                                @if($canje->responsable)
                                    <div class="bg-white/80 rounded-xl p-3 border border-amber-200/90 text-left space-y-1 shadow-2xs">
                                        <div class="flex items-center justify-between gap-2 border-b border-amber-100 pb-1.5">
                                            <span class="text-[10px] font-extrabold uppercase tracking-wide text-amber-800">
                                                <i class="fa-solid fa-user-check text-amber-600"></i> Responsable de Entrega
                                            </span>
                                            <span class="font-mono text-cyan-800 font-extrabold text-[11px] bg-cyan-50 px-2 py-0.5 rounded-md border border-cyan-100">
                                                CC {{ $canje->responsable->identificacion }}
                                            </span>
                                        </div>
                                        <div class="font-bold text-slate-800 text-xs">{{ $canje->responsable->nombre_completo }}</div>
                                        <div class="text-[11px] text-slate-600 flex flex-wrap gap-x-3 gap-y-1 pt-0.5">
                                            <span><i class="fa-solid fa-briefcase text-slate-400 text-[10px]"></i> {{ $canje->responsable->cargoOficial() }}</span>
                                            <span><i class="fa-solid fa-layer-group text-slate-400 text-[10px]"></i> {{ $canje->responsable->area?->nombre ?? 'Sin área' }}</span>
                                            <span><i class="fa-solid fa-location-dot text-slate-400 text-[10px]"></i> {{ $canje->responsable->sedeActual()?->nombre ?? 'Sede Principal' }}</span>
                                        </div>
                                    </div>
                                @elseif($canje->entregado_por)
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-amber-700 font-medium shrink-0">Entregado por:</span>
                                        <strong class="font-bold text-right">{{ $canje->entregado_por }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div>

                    @else
                        <!-- ESTADO: PENDIENTE DE ENTREGA (SOLICITADO) -->
                        <div class="bg-emerald-50 border-2 border-emerald-300 rounded-3xl p-5 text-center space-y-2 shadow-xs">
                            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-emerald-100 text-emerald-600 text-2xl shadow-xs mb-1">
                                <i class="fa-solid fa-gift"></i>
                            </div>
                            <span class="inline-block px-3 py-1 rounded-full text-[10.5px] font-extrabold uppercase bg-emerald-200 text-emerald-900 tracking-wider">
                                Canje Autorizado
                            </span>
                            <h2 class="font-heading font-extrabold text-emerald-950 text-lg sm:text-xl">
                                ¡Listo para Entrega de Incentivo!
                            </h2>
                            <p class="text-xs text-emerald-800 leading-relaxed max-w-sm mx-auto">
                                El colaborador cuenta con los puntos necesarios y ha presentado su código de canje. Presiona el botón inferior para confirmar la entrega.
                            </p>
                        </div>
                    @endif

                    <!-- Ficha de Datos Oficiales -->
                    <div class="bg-slate-50/90 rounded-2xl p-4 border border-slate-200/90 divide-y divide-slate-200/70 text-xs shadow-inner">
                        <div class="py-2.5 flex items-start justify-between gap-3">
                            <span class="text-slate-500 font-medium shrink-0">Colaborador:</span>
                            <span class="font-bold text-slate-800 text-right">{{ $canje->usuario?->nombre_completo ?? 'No registrado' }}</span>
                        </div>
                        <div class="py-2.5 flex items-start justify-between gap-3">
                            <span class="text-slate-500 font-medium shrink-0">Identificación:</span>
                            <span class="font-bold text-slate-800 text-right">{{ $canje->usuario?->identificacion ? 'CC ' . $canje->usuario->identificacion : '—' }}</span>
                        </div>
                        <div class="py-2.5 flex items-start justify-between gap-3">
                            <span class="text-slate-500 font-medium shrink-0">Área:</span>
                            <span class="font-semibold text-slate-700 text-right">{{ $canje->usuario?->area?->nombre ?? ($canje->usuario?->sedeActual()?->nombre ?? 'Sin área asignada') }}</span>
                        </div>
                        @if($canje->usuario?->sedeActual())
                            <div class="py-2.5 flex items-start justify-between gap-3">
                                <span class="text-slate-500 font-medium shrink-0">Sede:</span>
                                <span class="font-semibold text-slate-700 text-right">{{ $canje->usuario->sedeActual()->nombre }}</span>
                            </div>
                        @endif
                        <div class="py-3 flex items-start justify-between gap-3 bg-linear-to-r from-cyan-50 to-sky-50 -mx-4 px-4 rounded-xl my-1 border border-cyan-100">
                            <span class="text-cyan-800 font-bold shrink-0">Beneficio Solicitado:</span>
                            <span class="font-extrabold text-cyan-950 text-right text-sm">{{ $canje->beneficio?->nombre ?? 'Beneficio' }}</span>
                        </div>
                        <div class="py-2.5 flex items-start justify-between gap-3">
                            <span class="text-slate-500 font-medium shrink-0">Puntos Redimidos:</span>
                            <span class="font-bold text-amber-600 text-right">{{ number_format($canje->puntos_utilizados) }} pts</span>
                        </div>
                        <div class="py-2.5 flex items-start justify-between gap-3">
                            <span class="text-slate-500 font-medium shrink-0">Código Único:</span>
                            <span class="font-mono font-bold text-brand-blue text-right">{{ $canje->codigo_canje }}</span>
                        </div>
                        <div class="py-2.5 flex items-start justify-between gap-3">
                            <span class="text-slate-500 font-medium shrink-0">Fecha de Solicitud:</span>
                            <span class="font-medium text-slate-600 text-right">{{ $canje->fecha_canje ? $canje->fecha_canje->format('d/m/Y h:i A') : '—' }}</span>
                        </div>
                        <div class="py-2.5 flex items-start justify-between gap-3">
                            <span class="text-slate-500 font-medium shrink-0">Estado del Canje:</span>
                            @if($canje->estado === 'CANJEADO')
                                <span class="inline-flex items-center gap-1 font-extrabold px-3 py-1 rounded-full text-[11px] bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    <i class="fa-solid fa-circle-check text-[10px]"></i>
                                    CANJEADO Y RECIBIDO
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 font-extrabold px-3 py-1 rounded-full text-[11px] bg-amber-100 text-amber-800 border border-amber-200 animate-pulse">
                                    <i class="fa-solid fa-clock text-[10px]"></i>
                                    PENDIENTE DE ENTREGA
                                </span>
                            @endif
                        </div>
                    </div>

                    @if($canje->estado !== 'CANJEADO')
                        <!-- BOTÓN Y ACCIÓN DE CONFIRMAR CANJE -->
                        <div class="bg-linear-to-br from-emerald-50/80 via-teal-50/50 to-white rounded-2xl p-4 sm:p-5 border-2 border-emerald-300 shadow-sm space-y-3.5">
                            <div class="flex items-center gap-2 text-emerald-900 font-bold text-xs uppercase tracking-wide">
                                <i class="fa-solid fa-hand-holding-hand text-emerald-600 text-sm"></i>
                                <span>Acción Requerida</span>
                            </div>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                Escribe tu número de cédula para identificarte como el responsable que entrega el incentivo. Tu nombre, área y sede se cargarán automáticamente.
                            </p>

                            <form id="form-confirmar-canje" method="POST" action="{{ route('canje.confirmar', $canje->codigo_canje) }}" class="space-y-3 pt-1">
                                @csrf

                                <!-- Campo para Cédula del Responsable de Entrega -->
                                <div class="text-left space-y-2">
                                    <div class="flex items-center justify-between gap-2">
                                        <label for="responsable_cc" class="block text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                            <i class="fa-solid fa-id-card text-brand-blue"></i>
                                            <span>Cédula de quien entrega:</span>
                                            <span class="text-rose-500 font-bold">*</span>
                                        </label>
                                        <span class="text-[10px] text-slate-400 font-medium">Búsqueda automática</span>
                                    </div>

                                    <div class="relative">
                                        <input type="text"
                                               name="responsable_cc"
                                               id="responsable_cc"
                                               inputmode="numeric"
                                               autocomplete="off"
                                               value="{{ auth()->check() ? auth()->user()->identificacion : '' }}"
                                               placeholder="Escribe tu número de cédula..."
                                               class="w-full text-xs sm:text-sm font-mono font-bold py-2.5 sm:py-3 pl-10 pr-10 rounded-xl border border-slate-300 focus:outline-hidden focus:border-cyan-600 focus:ring-2 focus:ring-cyan-600/20 bg-white shadow-2xs transition-all">
                                        <i class="fa-solid fa-id-card absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                                        
                                        <!-- Estado visual en tiempo real dentro del input -->
                                        <div class="absolute right-3.5 top-1/2 -translate-y-1/2 flex items-center justify-center pointer-events-none">
                                            <i id="responsable-icon-loading" class="fa-solid fa-circle-notch fa-spin text-cyan-600 hidden text-sm"></i>
                                            <i id="responsable-icon-success" class="fa-solid fa-circle-check text-emerald-600 hidden text-sm"></i>
                                        </div>
                                    </div>

                                    <!-- Indicador de carga -->
                                    <div id="responsable-loading" class="hidden text-[11px] text-cyan-800 bg-cyan-50/80 p-2.5 rounded-xl border border-cyan-200 flex items-center gap-2">
                                        <i class="fa-solid fa-circle-notch fa-spin text-brand-blue"></i>
                                        <span>Consultando datos del colaborador...</span>
                                    </div>

                                    <!-- Mensaje de error si la CC no existe -->
                                    <div id="responsable-error" class="hidden text-[11px] text-rose-700 bg-rose-50 p-2.5 rounded-xl border border-rose-200 flex items-center gap-2">
                                        <i class="fa-solid fa-circle-exclamation text-rose-500 shrink-0"></i>
                                        <span id="responsable-error-msg">No se encontró ningún colaborador activo con el número de cédula ingresado.</span>
                                    </div>

                                    <!-- Tarjeta con la información cargada en columnita (sin mostrar rol) -->
                                    <div id="responsable-card" class="hidden bg-slate-50 border border-emerald-300/80 rounded-2xl p-3.5 space-y-2.5 shadow-2xs transition-all animate-in fade-in">
                                        <div class="flex items-center gap-1.5 text-[10.5px] font-extrabold uppercase tracking-wide text-emerald-800 border-b border-slate-200/60 pb-1.5">
                                            <i class="fa-solid fa-circle-check text-emerald-600 text-xs"></i>
                                            <span>Responsable de Entrega Identificado</span>
                                        </div>

                                        <!-- Información vertical en columna -->
                                        <div class="flex flex-col gap-1.5 text-xs">
                                            <div class="bg-white p-2.5 rounded-xl border border-slate-200/80">
                                                <span class="text-[9.5px] font-bold uppercase tracking-wider text-slate-400 block mb-0.5">Nombre Completo</span>
                                                <span id="card-responsable-nombre" class="font-bold text-slate-900 block leading-snug"></span>
                                            </div>
                                            <div class="bg-white p-2.5 rounded-xl border border-slate-200/80">
                                                <span class="text-[9.5px] font-bold uppercase tracking-wider text-slate-400 block mb-0.5">Cargo</span>
                                                <span id="card-responsable-cargo-text" class="font-semibold text-slate-800 block"></span>
                                            </div>
                                            <div class="bg-white p-2.5 rounded-xl border border-slate-200/80">
                                                <span class="text-[9.5px] font-bold uppercase tracking-wider text-slate-400 block mb-0.5">Área</span>
                                                <span id="card-responsable-area" class="font-semibold text-slate-800 block"></span>
                                            </div>
                                            <div class="bg-white p-2.5 rounded-xl border border-slate-200/80">
                                                <span class="text-[9.5px] font-bold uppercase tracking-wider text-slate-400 block mb-0.5">Sede</span>
                                                <span id="card-responsable-sede" class="font-semibold text-slate-800 block"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Campos ocultos del formulario -->
                                    <input type="hidden" name="responsable_usuario_id" id="input_responsable_usuario_id" value="">
                                    <input type="hidden" name="entregado_por" id="input_entregado_por" value="">
                                </div>

                                <button type="button"
                                        onclick="abrirModalConfirmacion()"
                                        id="btn-confirmar-canje"
                                        class="w-full py-3.5 px-4 rounded-xl bg-linear-to-r from-emerald-600 via-teal-600 to-emerald-700 hover:from-emerald-700 hover:to-teal-800 text-white font-heading font-extrabold text-sm sm:text-base shadow-md hover:shadow-lg transition-all duration-200 transform hover:scale-[1.01] flex items-center justify-center gap-2.5 cursor-pointer">
                                    <i class="fa-solid fa-circle-check text-lg"></i>
                                    <span>Confirmar Canje</span>
                                </button>
                            </form>
                        </div>
                    @else
                        <!-- MENSAJE DE SEGURIDAD / AUDITORÍA CUANDO YA FUE CANJEADO -->
                        <div class="text-[11px] text-amber-800 bg-amber-50/70 p-3.5 rounded-xl border border-amber-200/80 flex items-start gap-2.5">
                            <i class="fa-solid fa-shield-halved text-amber-600 text-base shrink-0 mt-0.5"></i>
                            <div class="leading-relaxed">
                                <strong>Registro Protegido:</strong> Este código QR es de uso único y fue invalidado automáticamente al momento de la entrega. No es posible canjearlo nuevamente.
                            </div>
                        </div>
                    @endif

                    <!-- Nota de Autenticidad -->
                    <div class="text-[11px] text-slate-500 flex items-start gap-2 bg-slate-50 p-3 rounded-xl border border-slate-200/60">
                        <i class="fa-solid fa-circle-info text-cyan-600 text-sm shrink-0 mt-0.5"></i>
                        <p class="leading-relaxed">
                            Sistema digital oficial de incentivos de C.I. Piscícola New York S.A. Todos los movimientos quedan auditados con marca de tiempo.
                        </p>
                    </div>

                @else
                    <!-- Estado de Canje No Encontrado -->
                    <div class="bg-rose-50 border border-rose-200 rounded-2xl p-6 text-center space-y-2">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-rose-100 text-rose-600 text-2xl shadow-xs">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                        <h2 class="font-heading font-extrabold text-rose-900 text-base">Código no encontrado</h2>
                        <p class="text-xs text-rose-700 leading-relaxed max-w-xs mx-auto">
                            El código <span class="font-mono font-bold">{{ $codigo }}</span> no corresponde a un canje registrado en el sistema o fue revocado.
                        </p>
                    </div>
                @endif
            </div>

            <!-- Footer de la tarjeta -->
            <div class="bg-slate-50 p-4 border-t border-slate-200/80 text-center text-[11px] text-slate-400">
                &copy; {{ date('Y') }} C.I. Piscícola New York S.A. · Todos los derechos reservados.
            </div>
        </div>
    </div>

    <!-- MODAL DE DIÁLOGO DE CONFIRMACIÓN DE ENTREGA -->
    @if($canje && $canje->estado !== 'CANJEADO')
        <div id="modal-confirmacion-canje" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200 space-y-4 animate-in fade-in zoom-in-95 duration-150">
                <div class="text-center space-y-2">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-2xl mx-auto shadow-xs">
                        <i class="fa-solid fa-hand-holding-dollar"></i>
                    </div>
                    <h3 class="font-heading font-extrabold text-lg text-slate-900">¿Confirmar entrega del beneficio?</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Estás a punto de confirmar la entrega del incentivo <strong class="text-slate-900">"{{ $canje->beneficio?->nombre }}"</strong> al colaborador <strong class="text-slate-900">{{ $canje->usuario?->nombre_completo }}</strong>.
                    </p>
                </div>

                <div class="bg-slate-50 p-3 rounded-xl border border-slate-200 text-[11px] text-slate-600 space-y-1.5">
                    <div><strong>Identificación del beneficiario:</strong> {{ $canje->usuario?->identificacion ? 'CC ' . $canje->usuario->identificacion : '—' }}</div>
                    <div><strong>Código QR:</strong> <span class="font-mono font-bold text-cyan-800">{{ $canje->codigo_canje }}</span></div>

                    <!-- Datos del Responsable que entrega -->
                    <div id="modal-responsable-detalle" class="pt-2 mt-1.5 border-t border-slate-200/80 text-slate-700 hidden">
                        <span class="text-slate-500 font-semibold block text-[10px] uppercase tracking-wider">
                            <i class="fa-solid fa-user-check text-emerald-600"></i> Entrega registrada por:
                        </span>
                        <div class="font-bold text-slate-900 text-xs mt-0.5" id="modal-resp-nombre"></div>
                        <div class="text-[10.5px] text-slate-600 flex flex-wrap items-center gap-x-2 mt-0.5">
                            <span class="inline-flex items-center gap-1 font-medium text-brand-blue">
                                <i class="fa-solid fa-diagram-project text-[9px]"></i>
                                <span id="modal-resp-area"></span>
                            </span>
                            <span class="text-slate-300">&bull;</span>
                            <span class="inline-flex items-center gap-1 font-medium text-slate-600">
                                <i class="fa-solid fa-location-dot text-[9px] text-rose-500"></i>
                                <span id="modal-resp-sede"></span>
                            </span>
                        </div>
                    </div>

                    <div class="text-emerald-700 font-semibold pt-1">
                        <i class="fa-solid fa-info-circle"></i> Al confirmar, este código no podrá ser escaneado ni reutilizado.
                    </div>
                </div>

                <div class="flex gap-2.5 pt-2">
                    <button type="button"
                            onclick="cerrarModalConfirmacion()"
                            class="flex-1 py-2.5 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition cursor-pointer">
                        Cancelar
                    </button>
                    <button type="button"
                            onclick="ejecutarConfirmacion()"
                            id="btn-modal-ejecutar"
                            class="flex-1 py-2.5 px-4 rounded-xl bg-linear-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-heading font-extrabold text-xs shadow-md transition flex items-center justify-center gap-1.5 cursor-pointer">
                        <i class="fa-solid fa-check"></i>
                        <span>Sí, Confirmar Canje</span>
                    </button>
                </div>
            </div>
        </div>

        <script>
            const urlBuscarResponsable = "{{ route('canje.buscar_responsable') }}";
            let responsableVerificado = false;
            let debounceTimer = null;

            async function buscarResponsablePorCc(cc) {
                cc = (cc || '').trim();
                const loading = document.getElementById('responsable-loading');
                const errorBox = document.getElementById('responsable-error');
                const errorMsg = document.getElementById('responsable-error-msg');
                const card = document.getElementById('responsable-card');
                const inputCc = document.getElementById('responsable_cc');
                const iconLoading = document.getElementById('responsable-icon-loading');
                const iconSuccess = document.getElementById('responsable-icon-success');

                if (!cc) {
                    resetResponsableUI();
                    return;
                }

                if (loading) loading.classList.remove('hidden');
                if (iconLoading) iconLoading.classList.remove('hidden');
                if (iconSuccess) iconSuccess.classList.add('hidden');
                if (errorBox) errorBox.classList.add('hidden');

                try {
                    const res = await fetch(`${urlBuscarResponsable}?identificacion=${encodeURIComponent(cc)}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const data = await res.json();

                    if (loading) loading.classList.add('hidden');
                    if (iconLoading) iconLoading.classList.add('hidden');

                    if (res.ok && data.encontrado && data.usuario) {
                        const u = data.usuario;
                        responsableVerificado = true;

                        // Actualizar card con datos en columna
                        document.getElementById('card-responsable-nombre').textContent = u.nombre_completo;
                        document.getElementById('card-responsable-cargo-text').textContent = u.cargo || 'Colaborador';
                        document.getElementById('card-responsable-area').textContent = u.area;
                        document.getElementById('card-responsable-sede').textContent = u.sede;

                        // Actualizar campos ocultos para el envío del formulario
                        document.getElementById('input_responsable_usuario_id').value = u.id;
                        document.getElementById('input_entregado_por').value = `${u.nombre_completo} (CC ${u.identificacion} · ${u.area} - ${u.sede})`;

                        // Actualizar modal
                        document.getElementById('modal-resp-nombre').textContent = `${u.nombre_completo} (CC ${u.identificacion})`;
                        document.getElementById('modal-resp-area').textContent = u.area;
                        document.getElementById('modal-resp-sede').textContent = u.sede;
                        const modalDet = document.getElementById('modal-responsable-detalle');
                        if (modalDet) modalDet.classList.remove('hidden');

                        if (card) card.classList.remove('hidden');
                        if (errorBox) errorBox.classList.add('hidden');
                        if (iconSuccess) iconSuccess.classList.remove('hidden');
                        if (inputCc) inputCc.classList.remove('border-rose-500', 'ring-2', 'ring-rose-200');
                    } else {
                        responsableVerificado = false;
                        if (card) card.classList.add('hidden');
                        if (iconSuccess) iconSuccess.classList.add('hidden');
                        if (errorBox) {
                            errorMsg.textContent = data.mensaje || 'No se encontró ningún colaborador activo con esa cédula.';
                            errorBox.classList.remove('hidden');
                        }
                        document.getElementById('input_responsable_usuario_id').value = '';
                        document.getElementById('input_entregado_por').value = '';
                        const modalDet = document.getElementById('modal-responsable-detalle');
                        if (modalDet) modalDet.classList.add('hidden');
                    }
                } catch (err) {
                    console.error('Error al buscar responsable:', err);
                    if (loading) loading.classList.add('hidden');
                    if (iconLoading) iconLoading.classList.add('hidden');
                    if (iconSuccess) iconSuccess.classList.add('hidden');
                    if (errorBox) {
                        errorMsg.textContent = 'Ocurrió un error al consultar el documento. Intenta nuevamente.';
                        errorBox.classList.remove('hidden');
                    }
                }
            }

            function resetResponsableUI() {
                responsableVerificado = false;
                const card = document.getElementById('responsable-card');
                const errorBox = document.getElementById('responsable-error');
                const loading = document.getElementById('responsable-loading');
                const iconLoading = document.getElementById('responsable-icon-loading');
                const iconSuccess = document.getElementById('responsable-icon-success');
                if (card) card.classList.add('hidden');
                if (errorBox) errorBox.classList.add('hidden');
                if (loading) loading.classList.add('hidden');
                if (iconLoading) iconLoading.classList.add('hidden');
                if (iconSuccess) iconSuccess.classList.add('hidden');
                const inputUsuarioId = document.getElementById('input_responsable_usuario_id');
                const inputEntregado = document.getElementById('input_entregado_por');
                if (inputUsuarioId) inputUsuarioId.value = '';
                if (inputEntregado) inputEntregado.value = '';
                const modalDet = document.getElementById('modal-responsable-detalle');
                if (modalDet) modalDet.classList.add('hidden');
            }

            document.addEventListener('DOMContentLoaded', function() {
                const inputCc = document.getElementById('responsable_cc');
                if (inputCc) {
                    inputCc.addEventListener('input', function() {
                        clearTimeout(debounceTimer);
                        const val = this.value.trim();
                        if (val.length >= 5) {
                            debounceTimer = setTimeout(() => {
                                buscarResponsablePorCc(val);
                            }, 350);
                        } else if (val.length === 0) {
                            resetResponsableUI();
                        }
                    });

                    inputCc.addEventListener('blur', function() {
                        const val = this.value.trim();
                        if (val.length >= 5 && !responsableVerificado) {
                            buscarResponsablePorCc(val);
                        }
                    });

                    inputCc.addEventListener('keydown', function(e) {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            clearTimeout(debounceTimer);
                            buscarResponsablePorCc(this.value.trim());
                        }
                    });

                    // Si ya viene pre-llenado (por ejemplo, usuario autenticado)
                    if (inputCc.value.trim()) {
                        buscarResponsablePorCc(inputCc.value.trim());
                    }
                }
            });

            function abrirModalConfirmacion() {
                const inputCc = document.getElementById('responsable_cc');
                const val = inputCc ? inputCc.value.trim() : '';

                if (!val) {
                    if (inputCc) {
                        inputCc.focus();
                        inputCc.classList.add('border-rose-500', 'ring-2', 'ring-rose-200');
                    }
                    const errorBox = document.getElementById('responsable-error');
                    const errorMsg = document.getElementById('responsable-error-msg');
                    if (errorBox && errorMsg) {
                        errorMsg.textContent = 'Por favor escribe tu número de cédula para identificarte como el responsable de entrega.';
                        errorBox.classList.remove('hidden');
                    }
                    return;
                }

                if (!responsableVerificado) {
                    // Intento de búsqueda inmediata
                    buscarResponsablePorCc(val).then(() => {
                        if (responsableVerificado) {
                            const modal = document.getElementById('modal-confirmacion-canje');
                            if (modal) modal.classList.remove('hidden');
                        }
                    });
                    return;
                }

                const modal = document.getElementById('modal-confirmacion-canje');
                if (modal) modal.classList.remove('hidden');
            }

            function cerrarModalConfirmacion() {
                const modal = document.getElementById('modal-confirmacion-canje');
                if (modal) modal.classList.add('hidden');
            }

            function ejecutarConfirmacion() {
                const btn = document.getElementById('btn-modal-ejecutar');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Confirmando...';
                }
                const form = document.getElementById('form-confirmar-canje');
                if (form) {
                    form.submit();
                }
            }
        </script>
    @endif

</body>
</html>
