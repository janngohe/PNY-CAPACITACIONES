<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Registro de Asistencia a Inducción · C.I. Piscícola New York</title>

    <link rel="icon" type="image/png" href="{{ asset('images/Logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400..700;1,9..40,400..700&family=Manrope:wght@500;600;700;800&family=JetBrains+Mono:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Desactivar scroll cuando se dibuja en el canvas */
        .touch-none {
            touch-action: none;
        }
    </style>
</head>
<body class="min-h-full font-sans antialiased bg-slate-950 text-slate-100 flex flex-col justify-between p-4 sm:p-6">

    <!-- Encabezado con Logo y Datos de la Empresa -->
    <header class="text-center pt-2 pb-4">
        <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-white/5 border border-white/10 p-2 shadow-lg mb-2">
            <img src="{{ asset('images/Logo.png') }}" alt="C.I. Piscícola New York" class="w-full h-full object-contain">
        </div>
        <div class="text-[11px] font-bold uppercase tracking-widest text-cyan-400">
            C.I. Piscícola New York S.A.
        </div>
        <h1 class="text-lg font-black text-white font-serif">Registro de Asistencia</h1>
    </header>

    <!-- Contenedor Principal del Formulario -->
    <main class="w-full max-w-md mx-auto flex-1 flex flex-col justify-center">

        @if($errorMensaje)
            <!-- Alerta si la sesión no existe o ya cerró -->
            <div class="bg-slate-900 border border-rose-500/30 rounded-3xl p-6 text-center shadow-2xl">
                <div class="w-16 h-16 rounded-full bg-rose-500/10 text-rose-400 flex items-center justify-center text-2xl mx-auto mb-4">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <h2 class="text-base font-bold text-white mb-2">Sesión no disponible</h2>
                <p class="text-xs text-slate-400 mb-6">{{ $errorMensaje }}</p>

                <a href="{{ route('induccion.asistencia.form') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs transition-colors">
                    <i class="fa-solid fa-rotate-left"></i> Probar con otro código
                </a>
            </div>

        @elseif(!$sesion)
            <!-- Pantalla para ingresar el Código Manual si no vino por QR -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-2xl">
                <div class="text-center mb-6">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 mb-2">
                        <i class="fa-solid fa-keyboard"></i> Ingreso Manual
                    </span>
                    <h2 class="text-base font-bold text-white">Escribe el Código de Sesión</h2>
                    <p class="text-xs text-slate-400 mt-1">Escribe el código que proyecta el facilitador en la pantalla (ej. IND-4821)</p>
                </div>

                <form onsubmit="irASesion(event)" class="space-y-4">
                    <div>
                        <label for="codigoInput" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                            Código de Inducción
                        </label>
                        <input type="text"
                               id="codigoInput"
                               placeholder="IND-XXXX"
                               required
                               class="w-full text-center tracking-widest uppercase font-mono text-xl font-bold px-4 py-3 bg-slate-800 border border-slate-700 rounded-2xl text-cyan-400 placeholder-slate-600 focus:outline-none focus:border-cyan-400">
                    </div>
                    <button type="submit"
                            class="w-full py-3.5 px-4 rounded-2xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-bold text-xs uppercase tracking-wider transition-all shadow-md">
                        Continuar
                    </button>
                </form>
            </div>

        @else
            <!-- Tarjeta de Información de la Sesión Activa -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 sm:p-6 shadow-2xl space-y-5 relative overflow-hidden">

                <!-- Header de la Inducción -->
                <div class="border-b border-slate-800 pb-4">
                    <div class="flex items-center justify-between gap-2 mb-1.5">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-cyan-500/10 text-cyan-400 border border-cyan-500/20">
                            {{ $sesion->induccion->tipo }}
                        </span>
                        <span class="text-[11px] font-mono text-slate-400 font-bold">
                            {{ $sesion->codigo_sesion }}
                        </span>
                    </div>
                    <h2 class="text-base font-bold text-white leading-snug">
                        {{ $sesion->induccion->titulo }}
                    </h2>
                    <div class="flex items-center gap-3 text-[11px] text-slate-400 mt-1">
                        <span><i class="fa-solid fa-location-dot text-slate-500 mr-1"></i>{{ $sesion->sede->nombre }}</span>
                        <span><i class="fa-solid fa-user-tie text-slate-500 mr-1"></i>{{ $sesion->facilitador->nombre_completo }}</span>
                    </div>
                </div>

                <!-- Formulario de Asistencia con AJAX -->
                <form id="formAsistencia" onsubmit="enviarAsistencia(event)" class="space-y-4">
                    @csrf
                    <input type="hidden" name="codigo_sesion" value="{{ $sesion->codigo_sesion }}">
                    <input type="hidden" id="es_externo_input" name="es_externo" value="0">
                    <input type="hidden" id="firma_digital_input" name="firma_digital" value="">

                    <!-- Paso 1: Número de Cédula -->
                    <div>
                        <label for="identificacion" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                            <i class="fa-solid fa-id-card text-cyan-400 mr-1"></i> Número de Cédula (CC) <span class="text-rose-400">*</span>
                        </label>
                        <div class="relative">
                            <input type="tel"
                                   name="identificacion"
                                   id="identificacion"
                                   inputmode="numeric"
                                   placeholder="Digita tu número de documento"
                                   required
                                   autocomplete="off"
                                   oninput="verificarIdentificacionDebounce()"
                                   class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-2xl text-white font-mono text-base placeholder-slate-500 focus:outline-none focus:border-cyan-400">
                            <div id="identificacionSpinner" class="hidden absolute right-3.5 top-3.5 text-cyan-400">
                                <i class="fa-solid fa-spinner fa-spin"></i>
                            </div>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-1">
                            Al escribir tu documento, el sistema verificará si perteneces a la empresa.
                        </p>
                    </div>

                    <!-- Mensaje si ya registró asistencia -->
                    <div id="alertaYaRegistrado" class="hidden p-3 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-amber-300 text-xs">
                        <i class="fa-solid fa-circle-exclamation mr-1.5"></i>
                        <span id="textoYaRegistrado">Ya registraste tu asistencia para esta sesión.</span>
                    </div>

                    <!-- Caso A: Empleado Reconocido en Sistema -->
                    <div id="panelEmpleadoReconocido" class="hidden p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-xs space-y-1.5 animate-fade-in">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-emerald-400 uppercase text-[10px] tracking-wider">
                                <i class="fa-solid fa-circle-check mr-1"></i> Colaborador Identificado
                            </span>
                            <span id="empSedeBadge" class="text-[10px] text-emerald-300 bg-emerald-950/60 px-2 py-0.5 rounded-full"></span>
                        </div>
                        <div id="empNombre" class="font-bold text-white text-sm"></div>
                        <div class="text-slate-300 flex items-center gap-2">
                            <span id="empCargo"></span> &middot; <span id="empArea" class="text-slate-400"></span>
                        </div>
                    </div>

                    <!-- Caso B: Persona Externa (Contratista / Visitante / Pasante) -->
                    <div id="panelPersonaExterna" class="hidden space-y-3 p-4 rounded-2xl bg-slate-800/80 border border-slate-700 animate-fade-in">
                        <div class="flex items-center gap-1.5 text-amber-400 text-xs font-bold">
                            <i class="fa-solid fa-user-plus"></i>
                            <span>Registro de Persona Externa / Contratista</span>
                        </div>
                        <p class="text-[11px] text-slate-400">
                            Tu documento no está registrado en nómina. Completa estos datos básicos:
                        </p>

                        <div>
                            <label for="nombre_completo" class="block text-[11px] font-bold uppercase tracking-wider text-slate-300 mb-1">
                                Nombre Completo <span class="text-rose-400">*</span>
                            </label>
                            <input type="text"
                                   name="nombre_completo"
                                   id="nombre_completo"
                                   placeholder="Nombre y Apellidos completos"
                                   class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white text-xs focus:outline-none focus:border-cyan-400">
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label for="empresa_externa" class="block text-[11px] font-bold uppercase tracking-wider text-slate-300 mb-1">
                                    Empresa / Entidad
                                </label>
                                <input type="text"
                                       name="empresa_externa"
                                       id="empresa_externa"
                                       placeholder="Ej: Contratista, Sena..."
                                       class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-white text-xs focus:outline-none focus:border-cyan-400">
                            </div>
                            <div>
                                <label for="telefono" class="block text-[11px] font-bold uppercase tracking-wider text-slate-300 mb-1">
                                    Teléfono Móvil
                                </label>
                                <input type="tel"
                                       name="telefono"
                                       id="telefono"
                                       placeholder="3XXXXXXXXX"
                                       class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-white text-xs focus:outline-none focus:border-cyan-400">
                            </div>
                        </div>
                    </div>

                    <!-- Paso 2: Firma Digital Táctil (Canvas) -->
                    <div id="seccionFirma" class="pt-2">
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-300">
                                <i class="fa-solid fa-signature text-cyan-400 mr-1"></i> Firma Digital Táctil <span class="text-rose-400">*</span>
                            </label>
                            <button type="button" onclick="limpiarFirma()"
                                    class="text-[11px] text-slate-400 hover:text-rose-400 transition-colors flex items-center gap-1 cursor-pointer">
                                <i class="fa-solid fa-rotate-left"></i> Limpiar
                            </button>
                        </div>

                        <div class="relative bg-white rounded-2xl overflow-hidden shadow-inner border-2 border-slate-700">
                            <canvas id="canvasFirma"
                                    class="w-full h-36 touch-none bg-white cursor-crosshair block"></canvas>
                            <div id="firmaPlaceholder" class="pointer-events-none absolute inset-0 flex items-center justify-center text-xs text-slate-400 select-none">
                                <span><i class="fa-solid fa-pen-nib mr-1"></i> Dibuja tu firma con el dedo aquí</span>
                            </div>
                        </div>
                        <p class="text-[10px] text-slate-500 mt-1 text-center">
                            Firma directamente sobre la pantalla de tu teléfono móvil.
                        </p>
                    </div>

                    <!-- Botón de Envío -->
                    <div class="pt-2">
                        <button type="submit"
                                id="btnRegistrar"
                                class="w-full py-4 px-6 rounded-2xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white font-bold text-sm uppercase tracking-wider shadow-lg shadow-cyan-500/20 transition-all flex items-center justify-center gap-2 cursor-pointer">
                            <i class="fa-solid fa-check"></i>
                            <span>Confirmar Asistencia</span>
                        </button>
                    </div>

                </form>

            </div>
        @endif

    </main>

    <!-- Pantalla de Éxito Flotante / Modal -->
    <div id="modalExito" class="fixed inset-0 z-50 hidden bg-slate-950/95 flex items-center justify-center p-6 text-center">
        <div class="max-w-sm w-full bg-slate-900 border border-slate-800 rounded-3xl p-8 shadow-2xl animate-fade-in">
            <div class="w-20 h-20 rounded-full bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-4xl mx-auto mb-4 border border-emerald-500/20">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <h2 class="text-xl font-bold text-white mb-1">¡Asistencia Confirmada!</h2>
            <p id="exitoNombre" class="text-sm font-semibold text-cyan-400 mb-2"></p>
            <p class="text-xs text-slate-400 mb-6 leading-relaxed">
                Tu asistencia y firma digital han sido registradas en la planilla oficial de la sesión. Ya puedes cerrar esta ventana.
            </p>
            <div class="p-3 bg-slate-950 rounded-2xl border border-slate-800 text-[11px] text-slate-500 font-mono mb-6">
                Hora de Registro: <span id="exitoHora" class="text-slate-300 font-bold"></span>
            </div>
            <button type="button" onclick="window.close()"
                    class="w-full py-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold transition-colors">
                Finalizar
            </button>
        </div>
    </div>

    <!-- Pie de Página -->
    <footer class="text-center py-2 text-[10px] text-slate-600">
        C.I. Piscícola New York &middot; Seguridad y Calidad Acuícola
    </footer>

    <!-- Scripts de Dibujo Táctil y Verificación de Cédula -->
    <script>
        const CODIGO_SESION = @json($sesion?->codigo_sesion);

        function irASesion(e) {
            e.preventDefault();
            const cod = document.getElementById('codigoInput').value.trim();
            if (cod) {
                window.location.href = `/asistencia/${encodeURIComponent(cod)}`;
            }
        }

        // Lógica de Canvas de Firma Táctil
        const canvas = document.getElementById('canvasFirma');
        let ctx = null;
        let dibujando = false;
        let firmaVacia = true;

        if (canvas) {
            ctx = canvas.getContext('2d');

            function redimensionarCanvas() {
                const rect = canvas.getBoundingClientRect();
                canvas.width = rect.width * 2;
                canvas.height = rect.height * 2;
                ctx.scale(2, 2);
                ctx.strokeStyle = '#0f172a'; // Tinta oscura nítida
                ctx.lineWidth = 2.5;
                ctx.lineCap = 'round';
                ctx.lineJoin = 'round';
            }

            window.addEventListener('resize', redimensionarCanvas);
            setTimeout(redimensionarCanvas, 100);

            // Eventos Táctiles (Móviles)
            canvas.addEventListener('touchstart', (e) => {
                e.preventDefault();
                dibujando = true;
                firmaVacia = false;
                document.getElementById('firmaPlaceholder').classList.add('hidden');
                const pos = obtenerCoordenadas(e.touches[0]);
                ctx.beginPath();
                ctx.moveTo(pos.x, pos.y);
            }, { passive: false });

            canvas.addEventListener('touchmove', (e) => {
                e.preventDefault();
                if (!dibujando) return;
                const pos = obtenerCoordenadas(e.touches[0]);
                ctx.lineTo(pos.x, pos.y);
                ctx.stroke();
            }, { passive: false });

            canvas.addEventListener('touchend', (e) => {
                e.preventDefault();
                dibujando = false;
            }, { passive: false });

            // Eventos de Mouse (Desktop)
            canvas.addEventListener('mousedown', (e) => {
                dibujando = true;
                firmaVacia = false;
                document.getElementById('firmaPlaceholder').classList.add('hidden');
                const pos = obtenerCoordenadas(e);
                ctx.beginPath();
                ctx.moveTo(pos.x, pos.y);
            });

            canvas.addEventListener('mousemove', (e) => {
                if (!dibujando) return;
                const pos = obtenerCoordenadas(e);
                ctx.lineTo(pos.x, pos.y);
                ctx.stroke();
            });

            window.addEventListener('mouseup', () => {
                dibujando = false;
            });

            function obtenerCoordenadas(evento) {
                const rect = canvas.getBoundingClientRect();
                return {
                    x: evento.clientX - rect.left,
                    y: evento.clientY - rect.top
                };
            }
        }

        function limpiarFirma() {
            if (!ctx) return;
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            firmaVacia = true;
            document.getElementById('firmaPlaceholder').classList.remove('hidden');
        }

        // Búsqueda inteligente de Cédula (Debounce)
        let timeoutBusqueda = null;
        function verificarIdentificacionDebounce() {
            clearTimeout(timeoutBusqueda);
            const cc = document.getElementById('identificacion').value.trim();

            if (cc.length < 4) {
                ocultarPanelesVerificacion();
                return;
            }

            timeoutBusqueda = setTimeout(() => {
                verificarIdentificacion(cc);
            }, 450);
        }

        async function verificarIdentificacion(cc) {
            const spinner = document.getElementById('identificacionSpinner');
            if (spinner) spinner.classList.remove('hidden');

            try {
                const response = await fetch('/asistencia/buscar-identificacion', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        identificacion: cc,
                        codigo_sesion: CODIGO_SESION
                    })
                });

                const data = await response.json();
                if (spinner) spinner.classList.add('hidden');

                const alertaYa = document.getElementById('alertaYaRegistrado');
                const panelEmp = document.getElementById('panelEmpleadoReconocido');
                const panelExt = document.getElementById('panelPersonaExterna');
                const esExtInput = document.getElementById('es_externo_input');

                // Si ya firmó previamente
                if (data.ya_registrado) {
                    alertaYa.classList.remove('hidden');
                    document.getElementById('textoYaRegistrado').textContent = data.mensaje;
                    panelEmp.classList.add('hidden');
                    panelExt.classList.add('hidden');
                    document.getElementById('btnRegistrar').disabled = true;
                    return;
                } else {
                    alertaYa.classList.add('hidden');
                    document.getElementById('btnRegistrar').disabled = false;
                }

                if (data.found) {
                    // Es empleado interno
                    esExtInput.value = '0';
                    panelExt.classList.add('hidden');

                    document.getElementById('empNombre').textContent = data.nombre_completo;
                    document.getElementById('empCargo').textContent = data.cargo;
                    document.getElementById('empArea').textContent = data.area;
                    document.getElementById('empSedeBadge').textContent = data.sede;
                    panelEmp.classList.remove('hidden');

                    if (document.getElementById('nombre_completo')) {
                        document.getElementById('nombre_completo').value = data.nombre_completo;
                    }
                } else {
                    // Es externo
                    esExtInput.value = '1';
                    panelEmp.classList.add('hidden');
                    panelExt.classList.remove('hidden');
                }

            } catch (err) {
                console.error("Error al buscar cédula:", err);
                if (spinner) spinner.classList.add('hidden');
            }
        }

        function ocultarPanelesVerificacion() {
            document.getElementById('panelEmpleadoReconocido')?.classList.add('hidden');
            document.getElementById('panelPersonaExterna')?.classList.add('hidden');
            document.getElementById('alertaYaRegistrado')?.classList.add('hidden');
            document.getElementById('btnRegistrar').disabled = false;
        }

        // Envío final del Formulario de Asistencia
        async function enviarAsistencia(e) {
            e.preventDefault();

            if (firmaVacia) {
                alert("Por favor dibuja tu firma en el recuadro blanco para confirmar tu asistencia.");
                return;
            }

            const dataURL = canvas.toDataURL('image/png');
            document.getElementById('firma_digital_input').value = dataURL;

            const btn = document.getElementById('btnRegistrar');
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i> Registrando...';

            const form = document.getElementById('formAsistencia');
            const formData = new FormData(form);

            try {
                const response = await fetch(`/asistencia/${CODIGO_SESION}/registrar`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const res = await response.json();

                if (response.ok && res.success) {
                    document.getElementById('exitoNombre').textContent = res.nombre;
                    document.getElementById('exitoHora').textContent = res.hora;
                    document.getElementById('modalExito').classList.remove('hidden');
                } else {
                    alert(res.message || "Ocurrió un error al procesar tu asistencia.");
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fa-solid fa-check mr-2"></i> Confirmar Asistencia';
                }
            } catch (err) {
                console.error(err);
                alert("Ocurrió un error de conexión al enviar el registro.");
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-check mr-2"></i> Confirmar Asistencia';
            }
        }
    </script>

</body>
</html>
