<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Autorización de Pruebas Psicotécnicas · C.I. Piscícola New York</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/Logo.png') }}">

    <!-- Google Fonts & Font Awesome -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400..700;1,9..40,400..700&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Tailwind CSS Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Estilos de impresión y formato institucional */
        @media print {
            body { background: white !important; }
            header, footer, #btnEnviarForm, #btnLimpiar, .no-print { display: none !important; }
            .formato-documento { box-shadow: none !important; border: none !important; padding: 0 !important; }
        }
    </style>
</head>
<body class="min-h-full font-sans text-ink bg-[#f8fafc] antialiased flex flex-col justify-between selection:bg-brand-blue/20 selection:text-brand-dark">

    <!-- CABECERA INSTITUCIONAL CORPORATIVA -->
    <header class="w-full bg-white border-b border-line/80 shadow-2xs sticky top-0 z-30">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 py-3.5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 p-1 bg-white rounded-xl shadow-xs border border-line/60 flex items-center justify-center shrink-0">
                    <img src="{{ asset('images/Logo.png') }}" alt="Logo C.I. Piscícola New York" class="h-full w-full object-contain">
                </div>
                <div>
                    <span class="block font-heading font-extrabold text-xs sm:text-sm tracking-wider uppercase text-brand-dark leading-tight">
                        C.I. Piscícola New York S.A.
                    </span>
                    <span class="block text-[10px] sm:text-[11px] font-semibold text-brand-blue tracking-wide uppercase">
                        Gestión Humana & Psicología Organizacional
                    </span>
                </div>
            </div>

            <!-- Código de sesión activa -->
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-brand-light text-brand-blue font-mono font-bold text-xs border border-brand-blue/20">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Sesión: {{ $codigo }}</span>
                </span>
            </div>
        </div>
    </header>

    <!-- CONTENEDOR PRINCIPAL -->
    <main class="flex-1 w-full max-w-3xl mx-auto px-4 sm:px-6 py-6 sm:py-10">

        <!-- STEPPER DE 3 PASOS: INDICADOR DE PROGRESO -->
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-line/80 shadow-2xs mb-6 sm:mb-8">
            <div class="flex items-center justify-between text-center relative max-w-md mx-auto">
                <!-- Línea conectora de fondo -->
                <div class="absolute top-4 left-8 right-8 h-0.5 bg-slate-200 -z-0"></div>

                <!-- Paso 1: Autorización (Activo) -->
                <div class="relative z-10 flex flex-col items-center">
                    <div class="w-9 h-9 rounded-full bg-brand-blue text-white flex items-center justify-center text-xs font-bold shadow-xs ring-4 ring-brand-light">
                        <i class="fa-solid fa-file-signature text-xs"></i>
                    </div>
                    <span class="text-[11px] font-bold text-brand-blue mt-1.5">1. Autorización</span>
                </div>

                <!-- Paso 2: 1ª Prueba (Pendiente) -->
                <div class="relative z-10 flex flex-col items-center opacity-60">
                    <div class="w-9 h-9 rounded-full bg-slate-100 text-slate-500 border border-slate-300 flex items-center justify-center text-xs font-bold">
                        2
                    </div>
                    <span class="text-[11px] font-semibold text-slate-500 mt-1.5">2. Prueba 1</span>
                </div>

                <!-- Paso 3: 2ª Prueba (Pendiente) -->
                <div class="relative z-10 flex flex-col items-center opacity-60">
                    <div class="w-9 h-9 rounded-full bg-slate-100 text-slate-500 border border-slate-300 flex items-center justify-center text-xs font-bold">
                        3
                    </div>
                    <span class="text-[11px] font-semibold text-slate-500 mt-1.5">3. Prueba 2</span>
                </div>
            </div>
        </div>

        <!-- FORMULARIO PRINCIPAL DE AUTORIZACIÓN -->
        <form id="formConsentimiento"
              method="POST"
              action="{{ route('psicologia.evaluacion.guardar_consentimiento', ['codigo' => $codigo]) }}"
              onsubmit="return validarYEnviar(event)"
              class="space-y-6">
            @csrf

            <!-- TARJETA 1: REGISTRO DE DATOS PERSONALES DEL CANDIDATO -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-line/80 shadow-xs relative overflow-hidden">
                <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-brand-dark via-brand-deep to-brand-blue"></div>

                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-brand-light text-brand-blue flex items-center justify-center text-base shadow-2xs">
                        <i class="fa-solid fa-id-card"></i>
                    </div>
                    <div>
                        <h2 class="text-base sm:text-lg font-heading font-extrabold text-brand-dark">
                            Datos del Candidato(a)
                        </h2>
                        <p class="text-xs text-muted">Ingresa tus datos personales tal como aparecen en tu documento de identidad.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">

                    <!-- Nombre completo -->
                    <div class="sm:col-span-2">
                        <label for="nombre_completo" class="block font-heading font-bold text-slate-700 mb-1.5">
                            Nombre Completo <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <i class="fa-regular fa-user absolute left-3.5 top-3 text-slate-400 text-xs"></i>
                            <input type="text"
                                   id="nombre_completo"
                                   name="nombre_completo"
                                   required
                                   value="{{ old('nombre_completo') }}"
                                   placeholder="Ej. Juan Carlos Pérez Morales"
                                   oninput="actualizarDocumento()"
                                   class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-line focus:outline-none focus:border-brand-blue focus:ring-1 focus:ring-brand-blue text-xs text-slate-800 placeholder:text-slate-400">
                        </div>
                    </div>

                    <!-- Cédula de Ciudadanía -->
                    <div>
                        <label for="identificacion" class="block font-heading font-bold text-slate-700 mb-1.5">
                            Cédula de Ciudadanía No. <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <i class="fa-regular fa-id-badge absolute left-3.5 top-3 text-slate-400 text-xs"></i>
                            <input type="text"
                                   id="identificacion"
                                   name="identificacion"
                                   required
                                   value="{{ old('identificacion') }}"
                                   placeholder="Ej. 1075284910"
                                   oninput="actualizarDocumento()"
                                   class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-line focus:outline-none focus:border-brand-blue focus:ring-1 focus:ring-brand-blue text-xs font-mono text-slate-800 placeholder:text-slate-400">
                        </div>
                    </div>

                    <!-- Lugar de Expedición -->
                    <div>
                        <label for="lugar_expedicion" class="block font-heading font-bold text-slate-700 mb-1.5">
                            Lugar de Expedición de la Cédula <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <i class="fa-solid fa-location-dot absolute left-3.5 top-3 text-slate-400 text-xs"></i>
                            <input type="text"
                                   id="lugar_expedicion"
                                   name="lugar_expedicion"
                                   required
                                   value="{{ old('lugar_expedicion') }}"
                                   placeholder="Ej. Neiva (Huila)"
                                   oninput="actualizarDocumento()"
                                   class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-line focus:outline-none focus:border-brand-blue focus:ring-1 focus:ring-brand-blue text-xs text-slate-800 placeholder:text-slate-400">
                        </div>
                    </div>

                    <!-- Fecha de Nacimiento -->
                    <div>
                        <label for="fecha_nacimiento" class="block font-heading font-bold text-slate-700 mb-1.5">
                            Fecha de Nacimiento <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <i class="fa-regular fa-calendar absolute left-3.5 top-3 text-slate-400 text-xs"></i>
                            <input type="date"
                                   id="fecha_nacimiento"
                                   name="fecha_nacimiento"
                                   required
                                   value="{{ old('fecha_nacimiento') }}"
                                   onchange="calcularEdad()"
                                   class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-line focus:outline-none focus:border-brand-blue focus:ring-1 focus:ring-brand-blue text-xs text-slate-800">
                        </div>
                    </div>

                    <!-- Edad (Auto calculada) -->
                    <div>
                        <label for="edad" class="block font-heading font-bold text-slate-700 mb-1.5">
                            Edad (Años) <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <i class="fa-solid fa-cake-candles absolute left-3.5 top-3 text-slate-400 text-xs"></i>
                            <input type="number"
                                   id="edad"
                                   name="edad"
                                   min="16"
                                   max="99"
                                   required
                                   value="{{ old('edad') }}"
                                   placeholder="Ej. 28"
                                   class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-line focus:outline-none focus:border-brand-blue focus:ring-1 focus:ring-brand-blue text-xs font-mono text-slate-800">
                        </div>
                    </div>

                    <!-- Cargo al que aspira (Texto escrito libre) -->
                    <div class="sm:col-span-2">
                        <label for="cargo_aspira" class="block font-heading font-bold text-slate-700 mb-1.5">
                            Cargo al que Aspira <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <i class="fa-solid fa-briefcase absolute left-3.5 top-3 text-slate-400 text-xs"></i>
                            <input type="text"
                                   id="cargo_aspira"
                                   name="cargo_aspira"
                                   required
                                   value="{{ old('cargo_aspira') }}"
                                   placeholder="Ej. Operario de Cultivo Piscícola / Auxiliar Administrativo"
                                   class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-line focus:outline-none focus:border-brand-blue focus:ring-1 focus:ring-brand-blue text-xs text-slate-800 placeholder:text-slate-400">
                        </div>
                    </div>

                </div>
            </div>

            <!-- TARJETA 2: DOCUMENTO INSTITUCIONAL DE AUTORIZACIÓN (FORMATO OFICIAL) -->
            <div class="bg-white rounded-3xl p-6 sm:p-9 border-2 border-line/90 shadow-md formato-documento relative">
                
                <!-- Encabezado del Formato Institucional -->
                <div class="border-b-2 border-slate-200 pb-5 mb-6 text-center">
                    <span class="inline-block text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">
                        Formato Institucional de Gestión Humana · Código GH-F-042
                    <span class="inline-block text-[10.5px] font-bold uppercase tracking-widest text-slate-500 mb-1 font-mono">
                        CÓDIGO: GH-FO-025
                    </span>
                    <h3 class="font-heading font-black text-base sm:text-lg text-brand-dark uppercase tracking-tight">
                        Autorización para la Realización de Pruebas Psicotécnicas
                    </h3>
                    <p class="text-[11px] font-bold text-brand-blue uppercase mt-0.5">
                        C.I. Piscícola New York S.A.
                    </p>
                </div>

                <!-- Cuerpo Oficial del Documento con Respeto Exacto a la Redacción -->
                <div class="bg-slate-50/80 rounded-2xl p-5 sm:p-7 border border-slate-200 text-slate-800 leading-relaxed text-xs sm:text-sm text-justify space-y-4 shadow-inner">
                    <p>
                        Yo, <span id="docNombre" class="font-bold underline decoration-brand-blue decoration-2 underline-offset-4 text-brand-dark">____________________________________________</span>, 
                        identificado(a) con cédula de ciudadanía No. <span id="docCedula" class="font-bold font-mono underline decoration-brand-blue decoration-2 underline-offset-4 text-brand-dark">______________________</span>, 
                        expedida en <span id="docLugar" class="font-bold underline decoration-brand-blue decoration-2 underline-offset-4 text-brand-dark">____________________________</span>, 
                    <p class="break-words">
                        Yo, <span id="docNombre" class="font-bold underline decoration-brand-blue decoration-2 underline-offset-4 text-brand-dark break-words">___</span>, 
                        identificado(a) con cédula de ciudadanía No. <span id="docCedula" class="font-bold font-mono underline decoration-brand-blue decoration-2 underline-offset-4 text-brand-dark break-words">___</span>, 
                        expedida en <span id="docLugar" class="font-bold underline decoration-brand-blue decoration-2 underline-offset-4 text-brand-dark break-words">___</span>, 
                        autorizo realizar las pruebas psicotécnicas como proceso de selección y contratación de personal en la empresa <strong>C.I. PISCÍCOLA NEW YORK S.A.</strong>
                    </p>

                    <p class="font-medium text-slate-700 bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-2xs">
                        <i class="fa-solid fa-shield-halved text-brand-blue mr-1.5"></i>
                        El resultado de la aplicación de los instrumentos de evaluación psicotécnicos es de total confidencialidad.
                    </p>
                </div>

                <!-- Casilla de Aceptación Expresa Obligatoria -->
                <div class="mt-6 p-4 rounded-2xl bg-brand-light/60 border border-brand-blue/30">
                    <label class="flex items-start gap-3 cursor-pointer select-none">
                        <input type="checkbox"
                               id="acepta_terminos"
                               name="acepta_terminos"
                               required
                               class="mt-1 h-4 w-4 rounded border-slate-300 text-brand-blue focus:ring-brand-blue cursor-pointer">
                        <div class="text-xs text-slate-800">
                            <strong class="font-heading font-extrabold text-brand-dark block mb-0.5">
                                Aceptación Expresa de la Autorización <span class="text-rose-500">*</span>
                            </strong>
                            <span>He leído atentamente, comprendo el alcance y autorizo de forma libre y voluntaria la aplicación de las pruebas psicotécnicas para el proceso de selección en C.I. Piscícola New York S.A.</span>
                        </div>
                    </label>
                </div>

                <!-- SECCIÓN DE FIRMA DIGITAL TÁCTIL / MOUSE (AUTORIZA) -->
                <div class="mt-8 pt-6 border-t-2 border-slate-200">
                    <div class="text-center mb-4">
                        <span class="block font-heading font-black text-xs sm:text-sm text-brand-dark uppercase tracking-wider">
                            AUTORIZA:
                        </span>
                    </div>

                    <!-- Canvas para la Firma -->
                    <div class="max-w-md mx-auto">
                        <div class="flex items-center justify-between mb-2">
                            <label class="font-heading font-bold text-xs text-slate-700 flex items-center gap-1.5">
                                <i class="fa-solid fa-signature text-brand-blue"></i>
                                <span>Firma del Candidato(a) <span class="text-rose-500">*</span></span>
                            </label>
                            <button type="button"
                                    id="btnLimpiar"
                                    onclick="limpiarFirma()"
                                    class="text-[11px] font-bold text-rose-600 hover:text-rose-800 bg-rose-50 hover:bg-rose-100 px-2.5 py-1 rounded-lg border border-rose-200 transition cursor-pointer flex items-center gap-1">
                                <i class="fa-solid fa-eraser"></i>
                                <span>Limpiar firma</span>
                            </button>
                        </div>

                        <!-- Recuadro visible para dibujar -->
                        <div class="relative w-full h-40 bg-white rounded-2xl border-2 border-dashed border-slate-300 hover:border-brand-blue shadow-inner overflow-hidden flex items-center justify-center cursor-crosshair">
                            <canvas id="canvasFirma" class="w-full h-full block touch-none"></canvas>

                            <!-- Placeholder guía mientras no se firme -->
                            <div id="firmaPlaceholder" class="pointer-events-none absolute inset-0 flex flex-col items-center justify-center text-slate-400 select-none">
                                <i class="fa-solid fa-pen-nib text-lg mb-1 text-slate-300"></i>
                                <span class="text-xs font-medium">Dibuja tu firma con el dedo o con el mouse aquí</span>
                            </div>
                        </div>
                        <input type="hidden" id="firma_digital_input" name="firma_digital" required>

                        <!-- Datos del pie de firma -->
                        <div class="mt-4 pt-3 border-t border-slate-200 grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs font-mono text-slate-600">
                            <div>
                                <span class="text-slate-400">Cédula:</span>
                                <strong id="docFirmaCedula" class="text-brand-dark font-bold">____________________</strong>
                                <strong id="docFirmaCedula" class="text-brand-dark font-bold break-words">___</strong>
                            </div>
                            <div class="sm:text-right">
                                <span class="text-slate-400">Fecha y Hora:</span>
                                <strong class="text-brand-blue font-bold">{{ $fechaHoraActual->format('d/m/Y h:i A') }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ALERTA DE ERRORES DE VALIDACIÓN -->
            <div id="alertaError" class="hidden p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center gap-2.5">
                <i class="fa-solid fa-circle-exclamation text-rose-600 text-base shrink-0"></i>
                <span id="mensajeError">Por favor completa todos los campos requeridos antes de continuar.</span>
            </div>

            <!-- BOTÓN PRINCIPAL DE ENVÍO -->
            <div class="pt-2">
                <button type="submit"
                        id="btnEnviarForm"
                        class="w-full py-4 px-6 rounded-2xl bg-gradient-to-r from-brand-dark via-brand-deep to-brand-blue hover:from-brand-deep hover:to-brand-cyan text-white font-heading font-extrabold text-sm sm:text-base shadow-lg hover:shadow-xl transition-all duration-200 flex items-center justify-center gap-2.5 cursor-pointer">
                    <span>Aceptar y Continuar</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </button>
                <p class="text-[11px] text-muted text-center mt-2.5">
                    Al presionar "Aceptar y continuar", tu autorización quedará formalmente registrada y avanzarás al Paso 2.
                </p>
            </div>

        </form>

    </main>

    <!-- PIE DE PÁGINA INSTITUCIONAL -->
    <footer class="w-full py-4 text-center text-xs text-muted border-t border-line/70 bg-white">
        &copy; {{ date('Y') }} C.I. Piscícola New York S.A.S. · Todos los derechos reservados.
    </footer>

    <!-- SCRIPT DE FIRMA CANVAS TÁCTIL / MOUSE Y VALIDACIONES -->
    <script>
        // Actualizar en tiempo real el texto del documento formal
        function actualizarDocumento() {
            const nombre = document.getElementById('nombre_completo').value.trim();
            const cedula = document.getElementById('identificacion').value.trim();
            const lugar = document.getElementById('lugar_expedicion').value.trim();

            document.getElementById('docNombre').textContent = nombre || '____________________________________________';
            document.getElementById('docCedula').textContent = cedula || '______________________';
            document.getElementById('docLugar').textContent = lugar || '____________________________';
            document.getElementById('docFirmaCedula').textContent = cedula || '____________________';
            document.getElementById('docNombre').textContent = nombre || '___';
            document.getElementById('docCedula').textContent = cedula || '___';
            document.getElementById('docLugar').textContent = lugar || '___';
            document.getElementById('docFirmaCedula').textContent = cedula || '___';
        }

        // Calcular edad automáticamente a partir de la fecha de nacimiento
        function calcularEdad() {
            const fechaInput = document.getElementById('fecha_nacimiento').value;
            if (!fechaInput) return;

            const nacimiento = new Date(fechaInput);
            const hoy = new Date();
            let edad = hoy.getFullYear() - nacimiento.getFullYear();
            const mes = hoy.getMonth() - nacimiento.getMonth();

            if (mes < 0 || (mes === 0 && hoy.getDate() < nacimiento.getDate())) {
                edad--;
            }

            if (edad >= 0 && edad <= 120) {
                document.getElementById('edad').value = edad;
            }
        }

        // Canvas de Firma Digital (Táctil y Mouse)
        // Canvas de Firma Digital (Táctil y Mouse) con protección de persistencia en scroll
        const canvas = document.getElementById('canvasFirma');
        let ctx = null;
        let dibujando = false;
        let firmaVacia = true;
        let canvasInicializado = false;
        let canvasAnchoActual = 0;

        if (canvas) {
            ctx = canvas.getContext('2d');

            function redimensionarCanvas() {
                const rect = canvas.getBoundingClientRect();
                if (rect.width === 0) return;

                // Si ya fue inicializado y el ancho no cambió (scroll en móvil al ocultar/mostrar barra de direcciones)
                if (canvasInicializado && Math.abs(rect.width - canvasAnchoActual) < 6) {
                    return; // No borrar la firma
                }

                // Guardar trazo previo si existía antes del redimensionamiento (ej. rotación de pantalla)
                const trazoPrevio = (!firmaVacia && canvas.width > 0) ? canvas.toDataURL('image/png') : null;

                canvasAnchoActual = rect.width;
                canvas.width = rect.width * 2;
                canvas.height = rect.height * 2;
                ctx.scale(2, 2);
                ctx.strokeStyle = '#002d5e'; // Tinta azul marino oficial
                ctx.lineWidth = 2.5;
                ctx.lineCap = 'round';
                ctx.lineJoin = 'round';

                canvasInicializado = true;

                // Restaurar trazo dibujado
                if (trazoPrevio) {
                    const img = new Image();
                    img.onload = () => {
                        ctx.drawImage(img, 0, 0, rect.width, rect.height);
                    };
                    img.src = trazoPrevio;
                    document.getElementById('firmaPlaceholder')?.classList.add('hidden');
                }
            }

            window.addEventListener('resize', redimensionarCanvas);
            setTimeout(redimensionarCanvas, 150);

            function obtenerCoordenadas(evento) {
                const rect = canvas.getBoundingClientRect();
                return {
                    x: evento.clientX - rect.left,
                    y: evento.clientY - rect.top
                };
            }

            function registrarFirmaGuardada() {
                firmaVacia = false;
                const dataUrl = canvas.toDataURL('image/png');
                document.getElementById('firma_digital_input').value = dataUrl;
            }

            // Eventos Táctiles (Móviles / Tablets)
            canvas.addEventListener('touchstart', (e) => {
                e.preventDefault();
                dibujando = true;
                firmaVacia = false;
                document.getElementById('firmaPlaceholder')?.classList.add('hidden');
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
                registrarFirmaGuardada();
            }, { passive: false });

            // Eventos de Mouse (Desktop)
            canvas.addEventListener('mousedown', (e) => {
                dibujando = true;
                firmaVacia = false;
                document.getElementById('firmaPlaceholder')?.classList.add('hidden');
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
                if (dibujando) {
                    dibujando = false;
                    registrarFirmaGuardada();
                }
            });
        }

        function limpiarFirma() {
            if (!ctx || !canvas) return;
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            firmaVacia = true;
            document.getElementById('firma_digital_input').value = '';
            document.getElementById('firmaPlaceholder')?.classList.remove('hidden');
        }

        // Validación y Enviar
        function validarYEnviar(e) {
            const alerta = document.getElementById('alertaError');
            const msg = document.getElementById('mensajeError');

            function mostrarError(texto) {
                msg.textContent = texto;
                alerta.classList.remove('hidden');
                alerta.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }

            const nombre = document.getElementById('nombre_completo').value.trim();
            if (!nombre) {
                e.preventDefault();
                mostrarError('Por favor ingresa tu nombre completo.');
                document.getElementById('nombre_completo').focus();
                return false;
            }

            const cedula = document.getElementById('identificacion').value.trim();
            if (!cedula) {
                e.preventDefault();
                mostrarError('Por favor ingresa tu número de cédula de ciudadanía.');
                document.getElementById('identificacion').focus();
                return false;
            }

            const lugar = document.getElementById('lugar_expedicion').value.trim();
            if (!lugar) {
                e.preventDefault();
                mostrarError('Por favor ingresa el lugar de expedición de tu cédula.');
                document.getElementById('lugar_expedicion').focus();
                return false;
            }

            const fechaNac = document.getElementById('fecha_nacimiento').value;
            if (!fechaNac) {
                e.preventDefault();
                mostrarError('Por favor selecciona tu fecha de nacimiento.');
                document.getElementById('fecha_nacimiento').focus();
                return false;
            }

            const edad = parseInt(document.getElementById('edad').value, 10);
            if (isNaN(edad) || edad < 16) {
                e.preventDefault();
                mostrarError('Por favor ingresa una edad válida (mínimo 16 años).');
                document.getElementById('edad').focus();
                return false;
            }

            const cargo = document.getElementById('cargo_aspira').value.trim();
            if (!cargo) {
                e.preventDefault();
                mostrarError('Por favor escribe el cargo al que aspiras.');
                document.getElementById('cargo_aspira').focus();
                return false;
            }

            const acepta = document.getElementById('acepta_terminos').checked;
            if (!acepta) {
                e.preventDefault();
                mostrarError('Debes marcar la casilla de aceptación expresa de la autorización para continuar.');
                document.getElementById('acepta_terminos').focus();
                return false;
            }

            if (firmaVacia) {
                e.preventDefault();
                mostrarError('Por favor dibuja tu firma digital en el recuadro antes de continuar.');
                canvas.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return false;
            }

            // Exportar firma a base64
            const dataUrl = canvas.toDataURL('image/png');
            document.getElementById('firma_digital_input').value = dataUrl;

            // Deshabilitar botón para evitar doble envío
            const btn = document.getElementById('btnEnviarForm');
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i> Guardando autorización...';

            return true;
        }

        document.addEventListener('DOMContentLoaded', () => {
            actualizarDocumento();
        });
    </script>
</body>
</html>
