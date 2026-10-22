<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-50">
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
        .touch-none {
            touch-action: none;
        }
    </style>
</head>
<body class="min-h-full font-sans antialiased bg-slate-50 text-slate-800 flex flex-col justify-between p-4 sm:p-6">

    <!-- Encabezado con Logo y Datos de la Empresa -->
    <header class="text-center pt-2 pb-4">
        <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-white border border-slate-200 p-2 shadow-xs mb-2">
            <img src="{{ asset('images/Logo.png') }}" alt="C.I. Piscícola New York" class="w-full h-full object-contain">
        </div>
        <div class="text-[11px] font-bold uppercase tracking-widest text-brand-blue">
            C.I. Piscícola New York S.A.
        </div>
        <h1 class="text-lg font-black text-slate-900 font-serif">Registro de Asistencia</h1>
    </header>

    <!-- Contenedor Principal del Formulario -->
    <main class="w-full max-w-md mx-auto flex-1 flex flex-col justify-center">

        @if($errorMensaje)
            <!-- Alerta si la sesión no existe o ya cerró -->
            <div class="bg-white border border-rose-200 rounded-3xl p-6 text-center shadow-xs">
                <div class="w-16 h-16 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center text-2xl mx-auto mb-4 border border-rose-100">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <h2 class="text-base font-bold text-slate-900 mb-2 font-serif">Sesión no disponible</h2>
                <p class="text-xs text-slate-500 mb-6 leading-relaxed">{{ $errorMensaje }}</p>

                <a href="{{ route('induccion.asistencia.form') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-blue hover:bg-brand-dark text-white font-bold text-xs transition-colors shadow-2xs">
                    <i class="fa-solid fa-rotate-left"></i> Probar con otro código
                </a>
            </div>

        @elseif(!$sesion)
            <!-- Pantalla para ingresar el Código Manual si no vino por QR -->
            <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 shadow-xs">
                <div class="text-center mb-6">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-cyan-50 text-cyan-800 border border-cyan-200 mb-2">
                        <i class="fa-solid fa-keyboard"></i> Ingreso Manual
                    </span>
                    <h2 class="text-base font-bold text-slate-900 font-serif">Escribe el Código de Sesión</h2>
                    <p class="text-xs text-slate-500 mt-1">Escribe el código que proyecta el facilitador en pantalla (ej. IND-4821)</p>
                </div>

                <form onsubmit="irASesion(event)" class="space-y-4">
                    <div>
                        <label for="codigoInput" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                            Código de Inducción
                        </label>
                        <input type="text"
                               id="codigoInput"
                               placeholder="IND-XXXX"
                               required
                               class="w-full text-center tracking-widest uppercase font-mono text-xl font-bold px-4 py-3 bg-slate-50 border border-slate-300 rounded-2xl text-brand-blue placeholder-slate-400 focus:outline-none focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20">
                    </div>
                    <button type="submit"
                            class="w-full py-3.5 px-4 rounded-2xl bg-brand-blue hover:bg-brand-dark text-white font-bold text-xs uppercase tracking-wider transition-all shadow-md cursor-pointer">
                        Continuar
                    </button>
                </form>
            </div>

        @else
            <!-- Tarjeta de Información de la Sesión Activa -->
            <div class="bg-white border border-slate-200 rounded-3xl p-5 sm:p-6 shadow-xs space-y-5 relative overflow-hidden">

                <!-- Header de la Inducción -->
                <div class="border-b border-slate-100 pb-4">
                    <div class="flex items-center justify-between gap-2 mb-2">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-cyan-50 text-cyan-800 border border-cyan-200">
                            <i class="fa-solid fa-chalkboard-user mr-1"></i> Inducción
                        </span>
                        <span class="text-[11px] font-mono text-brand-blue font-bold">
                            {{ $sesion->codigo_sesion }}
                        </span>
                    </div>
                    <h2 class="text-base font-bold text-slate-900 leading-snug font-serif">
                        {{ $sesion->induccion->titulo }}
                    </h2>
                    <div class="flex items-center gap-3 text-[11px] text-slate-500 mt-1">
                        <span><i class="fa-solid fa-location-dot text-slate-400 mr-1"></i>{{ $sesion->sede->nombre }}</span>
                        <span><i class="fa-solid fa-user-tie text-slate-400 mr-1"></i>{{ $sesion->facilitador->nombre_completo }}</span>
                    </div>
                </div>

                <!-- Formulario de Asistencia con AJAX -->
                <form id="formAsistencia" onsubmit="enviarAsistencia(event)" class="space-y-4">
                    @csrf
                    <input type="hidden" name="codigo_sesion" value="{{ $sesion->codigo_sesion }}">
                    <input type="hidden" id="tipo_registro_input" name="tipo_registro" value="nuevo">
                    <input type="hidden" id="es_externo_input" name="es_externo" value="0">
                    <input type="hidden" id="firma_digital_input" name="firma_digital" value="">

                    <!-- Paso 1: Número de Cédula -->
                    <div>
                        <label for="identificacion" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            <i class="fa-solid fa-id-card text-brand-blue mr-1"></i> Número de Cédula (CC) <span class="text-rose-500">*</span>
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
                                   class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-2xl text-slate-900 font-mono text-base placeholder-slate-400 focus:outline-none focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20">
                            <div id="identificacionSpinner" class="hidden absolute right-3.5 top-3.5 text-brand-blue">
                                <i class="fa-solid fa-spinner fa-spin"></i>
                            </div>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">
                            Al escribir tu documento, el sistema verificará automáticamente tus datos.
                        </p>
                    </div>

                    <!-- Mensaje si ya registró asistencia -->
                    <div id="alertaYaRegistrado" class="hidden p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 text-xs animate-fade-in">
                        <i class="fa-solid fa-circle-exclamation mr-1.5 text-amber-600"></i>
                        <span id="textoYaRegistrado">Ya registraste tu asistencia para esta sesión.</span>
                    </div>

                    <!-- Mensaje si está bloqueado por área no convocada o externos no permitidos -->
                    <div id="alertaBloqueado" class="hidden p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs animate-fade-in space-y-1">
                        <div class="font-bold flex items-center gap-1.5 text-rose-900">
                            <i class="fa-solid fa-ban text-rose-600"></i> No puedes registrar asistencia en esta sesión
                        </div>
                        <p id="textoBloqueado" class="text-[11px] text-rose-700 leading-relaxed"></p>
                    </div>

                    <!-- Caso A: Empleado Reconocido en Sistema -->
                    <div id="panelEmpleadoReconocido" class="hidden p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs space-y-1.5 animate-fade-in">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-emerald-800 uppercase text-[10px] tracking-wider">
                                <i class="fa-solid fa-circle-check mr-1 text-emerald-600"></i> Colaborador Identificado
                            </span>
                            <span id="empSedeBadge" class="text-[10px] text-emerald-800 bg-emerald-100 px-2 py-0.5 rounded-full font-bold"></span>
                        </div>
                        <div id="empNombre" class="font-bold text-slate-900 text-sm"></div>
                        <div class="text-slate-600 flex items-center gap-2">
                            <span id="empCargo"></span> &middot; <span id="empArea" class="text-slate-500"></span>
                        </div>
                    </div>

                    <!-- Caso B: Persona no encontrada en Nómina (Selector: Personal Nuevo vs Persona Externa) -->
                    <div id="panelNoEncontrado" class="hidden space-y-3.5 p-4.5 rounded-2xl bg-slate-50 border border-slate-200 animate-fade-in">
                        <div class="flex items-center justify-between gap-2">
                            <span class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-800">
                                <i class="fa-solid fa-user-tag text-brand-blue"></i> Tipo de Participante
                            </span>
                            <span id="badgePermisoExterno" class="text-[10px] text-slate-400 font-medium"></span>
                        </div>
                        <p class="text-[11px] text-slate-500 leading-snug">
                            No estás en el censo registrado de nómina. Selecciona tu condición:
                        </p>

                        <!-- Selector Segmentado Moderno: Personal Externo (por defecto) vs Contratista / Entidad -->
                        <div class="grid grid-cols-2 gap-1.5 p-1 bg-slate-200/80 rounded-2xl">
                            <button type="button"
                                    id="btnTipoNuevo"
                                    onclick="seleccionarTipoRegistro('nuevo')"
                                    class="py-2.5 px-3 rounded-xl font-bold text-xs flex items-center justify-center gap-1.5 transition-all shadow-xs bg-white text-brand-blue cursor-pointer">
                                <i class="fa-solid fa-user-plus text-[12px]"></i>
                                <span>Personal Externo</span>
                            </button>
                            <button type="button"
                                    id="btnTipoExterno"
                                    onclick="seleccionarTipoRegistro('externo')"
                                    class="py-2.5 px-3 rounded-xl font-bold text-xs flex items-center justify-center gap-1.5 transition-all text-slate-600 hover:text-slate-900 cursor-pointer">
                                <i class="fa-solid fa-building text-[12px]"></i>
                                <span>Contratista / Entidad</span>
                            </button>
                        </div>

                        <!-- Campo Común: Nombre Completo -->
                        <div>
                            <label for="nombre_completo" class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1">
                                Nombre Completo <span class="text-rose-500">*</span>
                            </label>
                            <input type="text"
                                   name="nombre_completo"
                                   id="nombre_completo"
                                   placeholder="Nombres y Apellidos completos"
                                   autocomplete="name"
                                   class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-slate-900 text-xs font-medium focus:outline-none focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20">
                        </div>

                        <!-- Sección: PERSONAL NUEVO (Sede primero, luego áreas de esa sede) -->
                        <div id="seccionPersonalNuevo" class="space-y-3 pt-1 border-t border-slate-200/60">
                            <!-- 1. Sede (Aparece primero) -->
                            <div>
                                <label for="selectSede" class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1">
                                    <i class="fa-solid fa-location-dot text-brand-blue mr-1"></i> Sede a la que ingresa <span class="text-rose-500">*</span>
                                </label>
                                <select name="sede_id"
                                        id="selectSede"
                                        onchange="alCambiarSede()"
                                        class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-slate-900 text-xs font-medium focus:outline-none focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 cursor-pointer">
                                    <option value="">-- Selecciona primero la sede --</option>
                                    @if(isset($sedes))
                                        @foreach($sedes as $s)
                                            <option value="{{ $s->id }}">{{ $s->nombre }} @if($s->ciudad)({{ $s->ciudad }})@endif</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <!-- 2. Área para la que va (Filtrada dinámicamente según la sede elegida) -->
                            <div>
                                <label for="selectArea" class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1">
                                    <i class="fa-solid fa-briefcase text-brand-blue mr-1"></i> Área para la que va <span class="text-rose-500">*</span>
                                </label>
                                <select name="area_id"
                                        id="selectArea"
                                        disabled
                                        class="w-full px-3.5 py-2.5 bg-slate-100 border border-slate-300 rounded-xl text-slate-900 text-xs font-medium focus:outline-none focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 disabled:bg-slate-100 disabled:text-slate-400 cursor-pointer">
                                    <option value="">-- Primero selecciona una sede --</option>
                                </select>
                                <p id="areaAyudaText" class="text-[10px] text-slate-400 mt-1">
                                    Las áreas disponibles se cargarán al seleccionar la sede.
                                </p>
                            </div>
                        </div>

                        <!-- Sección: PERSONA EXTERNA (Solo Entidad / Empresa) -->
                        <div id="seccionPersonaExterna" class="hidden space-y-3 pt-1 border-t border-slate-200/60">
                            <div>
                                <label for="entidad_externa" class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1">
                                    <i class="fa-solid fa-building text-amber-600 mr-1"></i> Entidad / Empresa <span class="text-rose-500">*</span>
                                </label>
                                <input type="text"
                                       name="entidad"
                                       id="entidad_externa"
                                       placeholder="Ej: SENA, Contratista SAS, Proveedor..."
                                       class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-slate-900 text-xs font-medium focus:outline-none focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20">
                                <p class="text-[10px] text-slate-400 mt-1">
                                    Indica la institución, empresa contratista o entidad a la que perteneces.
                                </p>
                            </div>
                        </div>

                    </div>

                    <!-- Paso 2: Firma Digital Táctil (Canvas) -->
                    <div id="seccionFirma" class="pt-2">
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                <i class="fa-solid fa-signature text-brand-blue mr-1"></i> Firma Digital Táctil <span class="text-rose-500">*</span>
                            </label>
                            <button type="button" onclick="limpiarFirma()"
                                    class="text-[11px] text-slate-500 hover:text-rose-600 transition-colors flex items-center gap-1 cursor-pointer">
                                <i class="fa-solid fa-rotate-left"></i> Limpiar
                            </button>
                        </div>

                        <div class="relative bg-white rounded-2xl overflow-hidden shadow-inner border-2 border-slate-300">
                            <canvas id="canvasFirma"
                                    class="w-full h-36 touch-none bg-white cursor-crosshair block"></canvas>
                            <div id="firmaPlaceholder" class="pointer-events-none absolute inset-0 flex items-center justify-center text-xs text-slate-400 select-none">
                                <span><i class="fa-solid fa-pen-nib mr-1"></i> Dibuja tu firma con el dedo aquí</span>
                            </div>
                        </div>
                        <p class="text-[10px] text-slate-400 mt-1 text-center">
                            Firma directamente sobre la pantalla de tu teléfono móvil.
                        </p>
                    </div>

                    <!-- Botón de Envío -->
                    <div class="pt-2">
                        <button type="submit"
                                id="btnRegistrar"
                                class="w-full py-4 px-6 rounded-2xl bg-brand-blue hover:bg-brand-dark text-white font-bold text-sm uppercase tracking-wider shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2 cursor-pointer">
                            <i class="fa-solid fa-check"></i>
                            <span>Confirmar Asistencia</span>
                        </button>
                    </div>

                </form>

            </div>
        @endif

    </main>

    <!-- Pantalla de Éxito Flotante / Modal -->
    <div id="modalExito" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-6 text-center">
        <div class="max-w-sm w-full bg-white border border-slate-200 rounded-3xl p-7 sm:p-8 shadow-2xl animate-fade-in">
            <div class="w-20 h-20 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-4xl mx-auto mb-4 border border-emerald-200 shadow-2xs">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <h2 class="text-xl font-black text-slate-900 mb-1 font-serif">¡Asistencia Confirmada!</h2>
            <p id="exitoNombre" class="text-sm font-bold text-brand-blue mb-3"></p>
            
            <div class="p-3.5 bg-emerald-50 border border-emerald-200 rounded-2xl text-emerald-900 text-xs font-bold leading-relaxed mb-4 shadow-2xs">
                Ya se marcó tu asistencia, puedes salir de este apartado.
            </div>

            <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-200 text-[11px] text-slate-500 font-mono mb-5">
                Hora de Registro: <strong id="exitoHora" class="text-slate-800 font-bold"></strong>
            </div>

            <button type="button" onclick="finalizarAsistencia()"
                    class="w-full py-3.5 px-4 rounded-2xl bg-brand-blue hover:bg-brand-dark text-white text-xs font-bold uppercase tracking-wider transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2 cursor-pointer">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                <span>Finalizar</span>
            </button>
        </div>
    </div>

    <!-- Pie de Página -->
    <footer class="text-center py-2 text-[10px] text-slate-400">
        C.I. Piscícola New York &middot; Seguridad y Calidad Acuícola
    </footer>

    <!-- Scripts de Dibujo Táctil y Verificación de Cédula -->
    <script>
        const CODIGO_SESION = @json($sesion?->codigo_sesion);
        const SEDES_DATA = @json($sedes ?? []);

        function irASesion(e) {
            e.preventDefault();
            const cod = document.getElementById('codigoInput').value.trim();
            if (cod) {
                window.location.href = `/asistencia/${encodeURIComponent(cod)}`;
            }
        }

        // Selector entre Personal Nuevo y Persona Externa
        function seleccionarTipoRegistro(tipo) {
            const btnNuevo = document.getElementById('btnTipoNuevo');
            const btnExt = document.getElementById('btnTipoExterno');
            const secNuevo = document.getElementById('seccionPersonalNuevo');
            const secExt = document.getElementById('seccionPersonaExterna');
            const tipoInput = document.getElementById('tipo_registro_input');
            const esExtInput = document.getElementById('es_externo_input');

            if (!btnNuevo || !btnExt) return;

            if (tipo === 'nuevo') {
                tipoInput.value = 'nuevo';
                esExtInput.value = '0';

                btnNuevo.className = 'py-2.5 px-3 rounded-xl font-bold text-xs flex items-center justify-center gap-1.5 transition-all shadow-xs bg-white text-brand-blue cursor-pointer';
                btnExt.className = 'py-2.5 px-3 rounded-xl font-bold text-xs flex items-center justify-center gap-1.5 transition-all text-slate-600 hover:text-slate-900 cursor-pointer';

                secNuevo?.classList.remove('hidden');
                secExt?.classList.add('hidden');

                if (document.getElementById('selectSede')) document.getElementById('selectSede').required = true;
                if (document.getElementById('selectArea')) document.getElementById('selectArea').required = true;
                if (document.getElementById('entidad_externa')) document.getElementById('entidad_externa').required = false;
            } else {
                tipoInput.value = 'externo';
                esExtInput.value = '1';

                btnExt.className = 'py-2.5 px-3 rounded-xl font-bold text-xs flex items-center justify-center gap-1.5 transition-all shadow-xs bg-white text-amber-700 cursor-pointer';
                btnNuevo.className = 'py-2.5 px-3 rounded-xl font-bold text-xs flex items-center justify-center gap-1.5 transition-all text-slate-600 hover:text-slate-900 cursor-pointer';

                secNuevo?.classList.add('hidden');
                secExt?.classList.remove('hidden');

                if (document.getElementById('selectSede')) document.getElementById('selectSede').required = false;
                if (document.getElementById('selectArea')) document.getElementById('selectArea').required = false;
                if (document.getElementById('entidad_externa')) document.getElementById('entidad_externa').required = true;
            }
        }

        // Carga dinámica de Áreas al seleccionar una Sede
        function alCambiarSede() {
            const selectSede = document.getElementById('selectSede');
            const selectArea = document.getElementById('selectArea');
            const areaAyuda = document.getElementById('areaAyudaText');
            const sedeId = selectSede ? selectSede.value : '';

            if (!selectArea) return;

            selectArea.innerHTML = '';

            if (!sedeId) {
                selectArea.disabled = true;
                selectArea.classList.add('bg-slate-100', 'text-slate-400');
                selectArea.classList.remove('bg-white', 'text-slate-900');
                const opt = document.createElement('option');
                opt.value = '';
                opt.textContent = '-- Primero selecciona una sede --';
                selectArea.appendChild(opt);
                if (areaAyuda) areaAyuda.textContent = 'Las áreas disponibles se cargarán al seleccionar la sede.';
                return;
            }

            const sedeObj = SEDES_DATA.find(s => String(s.id) === String(sedeId));
            const areas = sedeObj && sedeObj.areas ? sedeObj.areas : [];

            selectArea.disabled = false;
            selectArea.classList.remove('bg-slate-100', 'text-slate-400');
            selectArea.classList.add('bg-white', 'text-slate-900');

            if (areas.length === 0) {
                const opt = document.createElement('option');
                opt.value = '';
                opt.textContent = '-- Esta sede no tiene áreas asignadas --';
                selectArea.appendChild(opt);
                if (areaAyuda) areaAyuda.textContent = 'No hay áreas registradas para esta sede.';
                return;
            }

            const optPlaceholder = document.createElement('option');
            optPlaceholder.value = '';
            optPlaceholder.textContent = '-- Selecciona el área a la que vas --';
            selectArea.appendChild(optPlaceholder);

            areas.forEach(a => {
                const opt = document.createElement('option');
                opt.value = a.id;
                opt.textContent = a.nombre;
                selectArea.appendChild(opt);
            });

            if (areaAyuda) areaAyuda.textContent = `${areas.length} área(s) disponible(s) en esta sede.`;
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
                ctx.strokeStyle = '#002d5e'; // Tinta azul marino corporativa nítida
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
            document.getElementById('firmaPlaceholder')?.classList.remove('hidden');
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
                const alertaBloq = document.getElementById('alertaBloqueado');
                const panelEmp = document.getElementById('panelEmpleadoReconocido');
                const panelNoEnc = document.getElementById('panelNoEncontrado');
                const seccionFirma = document.getElementById('seccionFirma');
                const btnReg = document.getElementById('btnRegistrar');
                const esExtInput = document.getElementById('es_externo_input');
                const tipoInput = document.getElementById('tipo_registro_input');

                // Si ya firmó previamente
                if (data.ya_registrado) {
                    alertaYa.classList.remove('hidden');
                    alertaBloq.classList.add('hidden');
                    document.getElementById('textoYaRegistrado').textContent = data.mensaje;
                    panelEmp.classList.add('hidden');
                    panelNoEnc?.classList.add('hidden');
                    if (seccionFirma) seccionFirma.classList.add('hidden');
                    btnReg.disabled = true;
                    return;
                } else {
                    alertaYa.classList.add('hidden');
                }

                // Si está bloqueado (área no convocada)
                if (data.bloqueado) {
                    alertaBloq.classList.remove('hidden');
                    document.getElementById('textoBloqueado').textContent = data.mensaje;
                    panelEmp.classList.add('hidden');
                    panelNoEnc?.classList.add('hidden');
                    if (seccionFirma) seccionFirma.classList.add('hidden');
                    btnReg.disabled = true;
                    return;
                } else {
                    alertaBloq.classList.add('hidden');
                    if (seccionFirma) seccionFirma.classList.remove('hidden');
                    btnReg.disabled = false;
                }

                if (data.found) {
                    // Es empleado interno convocado en nómina
                    esExtInput.value = '0';
                    tipoInput.value = 'reconocido';
                    panelNoEnc?.classList.add('hidden');

                    document.getElementById('empNombre').textContent = data.nombre_completo;
                    document.getElementById('empCargo').textContent = data.cargo;
                    document.getElementById('empArea').textContent = data.area;
                    document.getElementById('empSedeBadge').textContent = data.sede;
                    panelEmp.classList.remove('hidden');

                    if (document.getElementById('nombre_completo')) {
                        document.getElementById('nombre_completo').value = data.nombre_completo;
                        document.getElementById('nombre_completo').required = false;
                    }
                    if (document.getElementById('selectSede')) document.getElementById('selectSede').required = false;
                    if (document.getElementById('selectArea')) document.getElementById('selectArea').required = false;
                    if (document.getElementById('entidad_externa')) document.getElementById('entidad_externa').required = false;
                } else {
                    // No está registrado en nómina -> Mostrar selector de Personal Nuevo o Persona Externa
                    panelEmp.classList.add('hidden');
                    panelNoEnc?.classList.remove('hidden');

                    const btnExt = document.getElementById('btnTipoExterno');
                    const badgePermiso = document.getElementById('badgePermisoExterno');

                    if (!data.permitir_externos) {
                        if (btnExt) {
                            btnExt.disabled = true;
                            btnExt.classList.add('opacity-40', 'cursor-not-allowed');
                            btnExt.title = 'Esta sesión no admite personas externas';
                        }
                        if (badgePermiso) badgePermiso.textContent = '(Externos no habilitados)';
                        seleccionarTipoRegistro('nuevo');
                    } else {
                        if (btnExt) {
                            btnExt.disabled = false;
                            btnExt.classList.remove('opacity-40', 'cursor-not-allowed');
                            btnExt.title = '';
                        }
                        if (badgePermiso) badgePermiso.textContent = '';
                        const tipoActual = tipoInput.value === 'externo' ? 'externo' : 'nuevo';
                        seleccionarTipoRegistro(tipoActual);
                    }

                    if (document.getElementById('nombre_completo')) {
                        document.getElementById('nombre_completo').required = true;
                    }
                }

            } catch (err) {
                console.error("Error al buscar cédula:", err);
                if (spinner) spinner.classList.add('hidden');
            }
        }

        function ocultarPanelesVerificacion() {
            document.getElementById('panelEmpleadoReconocido')?.classList.add('hidden');
            document.getElementById('panelNoEncontrado')?.classList.add('hidden');
            document.getElementById('alertaYaRegistrado')?.classList.add('hidden');
            document.getElementById('alertaBloqueado')?.classList.add('hidden');
            document.getElementById('seccionFirma')?.classList.remove('hidden');
            document.getElementById('btnRegistrar').disabled = false;
        }

        // Envío final del Formulario de Asistencia
        async function enviarAsistencia(e) {
            e.preventDefault();

            const tipoRegistro = document.getElementById('tipo_registro_input').value;

            if (tipoRegistro === 'nuevo') {
                const nombre = document.getElementById('nombre_completo')?.value.trim();
                const sede = document.getElementById('selectSede')?.value;
                const area = document.getElementById('selectArea')?.value;

                if (!nombre) {
                    alert("Por favor ingresa tu nombre completo.");
                    document.getElementById('nombre_completo')?.focus();
                    return;
                }
                if (!sede) {
                    alert("Por favor selecciona primero la sede a la que ingresas.");
                    document.getElementById('selectSede')?.focus();
                    return;
                }
                if (!area) {
                    alert("Por favor selecciona el área para la que vas.");
                    document.getElementById('selectArea')?.focus();
                    return;
                }
            } else if (tipoRegistro === 'externo') {
                const nombre = document.getElementById('nombre_completo')?.value.trim();
                const entidad = document.getElementById('entidad_externa')?.value.trim();

                if (!nombre) {
                    alert("Por favor ingresa tu nombre completo.");
                    document.getElementById('nombre_completo')?.focus();
                    return;
                }
                if (!entidad) {
                    alert("Por favor indica la entidad o empresa a la que perteneces.");
                    document.getElementById('entidad_externa')?.focus();
                    return;
                }
            }

            if (firmaVacia) {
                alert("Por favor dibuja tu firma en el recuadro para confirmar tu asistencia.");
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

        function finalizarAsistencia() {
            // Intentar cerrar la ventana/pestaña
            try {
                window.open('', '_self', '');
                window.close();
            } catch (e) {}

            // Si el navegador del móvil no permite window.close() por políticas de seguridad:
            // Desplegar pantalla completa limpia y definitiva confirmando la salida
            document.body.innerHTML = `
                <div class="min-h-screen bg-slate-100 flex flex-col items-center justify-center p-6 text-center font-sans">
                    <div class="max-w-sm w-full bg-white border border-slate-200 rounded-3xl p-8 shadow-xl animate-fade-in">
                        <div class="w-20 h-20 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-4xl mx-auto mb-4 border border-emerald-200 shadow-2xs">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                        <h1 class="text-xl font-black text-slate-900 font-serif mb-2">¡Asistencia Marcada!</h1>
                        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-emerald-900 text-xs font-bold leading-relaxed mb-5 shadow-2xs">
                            Ya se marcó asistencia, puedes salir de este apartado.
                        </div>
                        <p class="text-xs text-slate-500 mb-6 leading-relaxed">
                            Tu asistencia y firma digital han quedado registradas oficialmente en el sistema. Puedes cerrar con seguridad esta pestaña en tu navegador.
                        </p>
                        <div class="pt-4 border-t border-slate-100">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 text-slate-600 text-xs font-semibold">
                                <i class="fa-solid fa-lock text-[10px] text-emerald-600"></i> Registro completado con éxito
                            </span>
                        </div>
                    </div>
                </div>
            `;
        }
    </script>

</body>
</html>
