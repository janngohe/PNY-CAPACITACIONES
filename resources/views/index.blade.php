<!DOCTYPE html>
<html lang="es" class="h-full bg-paper">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Iniciar sesión · C.I. Piscícola New York</title>
    <meta name="description" content="Plataforma de capacitaciones de C.I. Piscícola New York. Ingresa tu número de identificación para acceder.">

    <!-- Tipografía Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400..700;1,9..40,400..700&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS compilado por Vite (npm) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full font-sans text-ink bg-paper antialiased selection:bg-brand-blue/20 selection:text-brand-dark">
    <div class="min-h-screen flex flex-col justify-between overflow-x-clip bg-paper bg-[radial-gradient(ellipse_at_100%_0%,rgba(186,230,253,0.55),transparent_42%),radial-gradient(ellipse_at_0%_100%,rgba(224,242,254,0.45),transparent_38%)]">
        
        <!-- ========================================================
             HEADER CON OLAS MARINAS (Estilo piscicolanewyork.com)
             ======================================================== -->
        <header class="w-full bg-white relative shadow-xs z-30" aria-label="Identidad de la empresa">
            <div class="w-full max-w-[1280px] mx-auto px-5 sm:px-8 md:px-12 py-3.5 sm:py-4 flex items-center justify-between">
                <!-- Marca & Logo -->
                <div class="flex items-center gap-3.5">
                    <div class="h-11 sm:h-12 md:h-14 flex items-center justify-center">
                        <img src="{{ asset('images/Logo.png') }}" 
                             alt="Logo C.I. Piscícola New York" 
                             class="h-9 sm:h-10 md:h-12 w-auto max-w-[150px] object-contain">
                    </div>
                    <div class="font-heading font-extrabold text-[12px] sm:text-[13px] md:text-[14px] leading-tight tracking-[0.11em] text-brand-dark uppercase select-none">
                        C.I. PISCÍCOLA <span class="text-brand-blue font-bold">NEW YORK</span>
                        <span class="block text-[10px] font-semibold text-muted tracking-normal normal-case mt-0.5">Portal de Capacitaciones</span>
                    </div>
                </div>

                <!-- Insignia corporativa lateral -->
                <div class="hidden sm:flex items-center gap-2 text-xs font-semibold text-brand-blue bg-brand-light/80 px-3.5 py-1.5 rounded-full border border-brand-blue/15 shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-brand-blue animate-pulse"></span>
                    <span>Acceso Seguro</span>
                </div>
            </div>

            <!-- Olas decorativas continuas en el borde inferior del Header -->
            <div class="w-full overflow-hidden leading-none absolute top-full left-0 pointer-events-none z-20">
                <!-- Capa 1: Sombra de ola azul cian traslúcida para profundidad -->
                <svg class="relative block w-full h-6 sm:h-8 md:h-10 text-brand-sky/25" viewBox="0 0 1440 50" preserveAspectRatio="none">
                    <path d="M0,0 L0,20 C60,45 180,45 240,25 C300,5 420,5 480,25 C540,45 660,45 720,25 C780,5 900,5 960,25 C1020,45 1140,45 1200,25 C1260,5 1380,5 1440,25 L1440,0 Z" fill="currentColor"/>
                </svg>
                <!-- Capa 2: Ola blanca sólida principal (idéntica a la página oficial) -->
                <svg class="relative block w-full h-5 sm:h-7 md:h-9 text-white -mt-6 sm:-mt-8 md:-mt-10" viewBox="0 0 1440 50" preserveAspectRatio="none">
                    <path d="M0,0 L0,25 C60,45 180,45 240,25 C300,5 420,5 480,25 C540,45 660,45 720,25 C780,5 900,5 960,25 C1020,45 1140,45 1200,25 C1260,5 1380,5 1440,25 L1440,0 Z" fill="currentColor"/>
                </svg>
            </div>
        </header>

        <!-- ========================================================
             CONTENIDO PRINCIPAL / FORMULARIO & FOTO CUADRADA CON OLAS
             ======================================================== -->
        <main class="w-full max-w-[1280px] mx-auto px-5 sm:px-6 md:px-10 lg:px-14 pt-10 sm:pt-12 md:pt-14 pb-8 sm:pb-10 md:pb-12 flex-1 grid grid-cols-1 lg:grid-cols-[1.1fr_0.9fr] items-center gap-8 lg:gap-12 relative z-10">
            
            <!-- Columna del Formulario de Inicio de Sesión -->
            <section class="w-full max-w-[460px] mx-auto lg:mx-0 lg:pl-2 xl:pl-8" aria-labelledby="login-title">
                
                <!-- Encabezado del Formulario -->
                <div class="flex items-center justify-between gap-3 min-h-[110px] lg:min-h-0 mb-6 lg:mb-8 relative">
                    <div class="w-[64%] sm:w-[68%] lg:w-full relative z-10">
                        <span class="inline-block text-[11px] font-bold uppercase tracking-wider text-brand-blue bg-brand-light px-2.5 py-0.5 rounded-full mb-2">
                            Plataforma de Capacitaciones
                        </span>
                        <h1 id="login-title" class="font-heading font-bold text-[28px] sm:text-4xl lg:text-[40px] tracking-[-0.055em] leading-[1.1] text-brand-dark mb-2">
                            Iniciar sesión
                        </h1>
                        <p class="text-[13px] sm:text-sm lg:text-[14.5px] leading-relaxed text-muted max-w-[210px] sm:max-w-[280px] lg:max-w-[340px]">
                            Ingresa tu número de identificación para acceder al sistema.
                        </p>
                    </div>

                    <!-- Detalle fotográfico cuadrado con difuminado de olas para móvil/tablet -->
                    <div class="w-[105px] h-[105px] sm:w-[120px] sm:h-[120px] shrink-0 relative mr-0.5 lg:hidden rounded-2xl overflow-hidden shadow-md border-2 border-white bg-brand-light" aria-hidden="true">
                        <img src="{{ asset('images/Img-login.jpg') }}"
                             alt="Piscicultura y producción C.I. Piscícola New York"
                             loading="lazy"
                             decoding="async"
                             class="w-full h-full object-cover">
                        <!-- Difuminado en ola inferior para móvil -->
                        <svg class="absolute w-full h-[48%] bottom-0 left-0 pointer-events-none" viewBox="0 0 200 80" preserveAspectRatio="none">
                            <path d="M0,25 C40,45 80,10 120,30 C160,50 180,20 200,28 L200,80 L0,80 Z" fill="rgba(0, 61, 128, 0.78)" />
                            <path d="M0,25 C40,45 80,10 120,30 C160,50 180,20 200,28" fill="none" stroke="rgba(224, 242, 254, 0.85)" stroke-width="2" />
                        </svg>
                    </div>
                </div>

                <!-- Formulario -->
                <form class="flex flex-col gap-4" method="POST" action="{{ route('login.post') }}">
                    @csrf

                    @if ($errors->any())
                        <div class="p-3.5 rounded-xl bg-red-50 border border-red-200 text-xs text-red-700 flex items-start gap-2.5">
                            <svg class="w-4 h-4 text-red-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                            </svg>
                            <div class="flex-1">
                                @foreach ($errors->all() as $error)
                                    <p>{{ $error }}</p>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if (session('status'))
                        <div class="p-3.5 rounded-xl bg-blue-50 border border-blue-200 text-xs text-brand-blue flex items-center gap-2.5">
                            <svg class="w-4 h-4 text-brand-blue shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                            </svg>
                            <span>{{ session('status') }}</span>
                        </div>
                    @endif

                    <!-- Campo Número de Identificación -->
                    <div class="flex flex-col gap-2">
                        <label for="identification" class="font-heading font-bold text-[13px] tracking-[-0.01em] text-brand-dark">
                            Número de identificación
                        </label>
                        <div class="h-[52px] flex items-center relative border border-line rounded-xl bg-white/90 transition-all duration-200 focus-within:border-brand-blue focus-within:ring-3 focus-within:ring-brand-blue/15 focus-within:bg-white shadow-2xs">
                            <input type="text"
                                   id="identification"
                                   name="identification"
                                   value="{{ old('identification') }}"
                                   inputmode="numeric"
                                   autocomplete="username"
                                   placeholder="Ingresa tu número de cédula"
                                   required
                                   class="w-full h-full px-4 border-0 outline-none rounded-xl bg-transparent text-ink placeholder-[#94a3b8] text-sm font-medium font-sans">
                        </div>
                    </div>

                    <!-- Campo Contraseña con botón alternar visibilidad -->
                    <div class="flex flex-col gap-2">
                        <label for="password" class="font-heading font-bold text-[13px] tracking-[-0.01em] text-brand-dark">
                            Contraseña
                        </label>
                        <div class="h-[52px] flex items-center relative border border-line rounded-xl bg-white/90 transition-all duration-200 focus-within:border-brand-blue focus-within:ring-3 focus-within:ring-brand-blue/15 focus-within:bg-white shadow-2xs">
                            <input type="password"
                                   id="password"
                                   name="password"
                                   autocomplete="current-password"
                                   placeholder="Ingresa tu contraseña"
                                   required
                                   class="w-full h-full pl-4 pr-12 border-0 outline-none rounded-xl bg-transparent text-ink placeholder-[#94a3b8] text-sm font-medium font-sans">
                            
                            <button type="button"
                                    id="toggle-password"
                                    class="w-[40px] h-[40px] absolute right-1.5 top-1.5 flex items-center justify-center rounded-lg text-muted hover:text-brand-blue hover:bg-brand-light focus-visible:outline-2 focus-visible:outline-brand-blue/50 transition-colors cursor-pointer"
                                    aria-label="Mostrar contraseña"
                                    aria-pressed="false">
                                <!-- Icono Ojo (Visible) -->
                                <svg id="icon-eye" class="w-5 h-5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <!-- Icono Ojo Tachado (Oculto) -->
                                <svg id="icon-eye-off" class="w-5 h-5 hidden transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Botón de Envío Corporativo Azul -->
                    <button type="submit"
                            id="login-submit"
                            class="min-h-[52px] w-full mt-1.5 border-0 rounded-xl bg-brand-blue hover:bg-brand-deep active:bg-brand-dark text-white font-heading font-bold text-sm tracking-[0.01em] shadow-[0_10px_20px_rgba(0,86,179,0.22)] hover:shadow-[0_12px_24px_rgba(0,86,179,0.28)] hover:-translate-y-0.5 active:translate-y-0 focus-visible:outline-3 focus-visible:outline-brand-blue/35 focus-visible:outline-offset-2 transition-all duration-200 cursor-pointer flex items-center justify-center gap-2 group">
                        <span>Iniciar sesión</span>
                        <svg class="w-4 h-4 transition-transform group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                        </svg>
                    </button>
                </form>

                <!-- Nota al pie del formulario -->
                <p class="flex items-center justify-center lg:justify-start gap-1.5 mt-4.5 text-muted text-[11px] md:text-xs leading-relaxed">
                    <svg class="w-3.5 h-3.5 text-brand-blue shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Acceso exclusivo para colaboradores de C.I. Piscícola New York.</span>
                </p>
            </section>

            <!-- ========================================================
                 COLUMNA MULTIMEDIA (PC): CUADRADA, ESTÁTICA Y CON OLAS
                 ======================================================== -->
            <div class="hidden lg:flex items-center justify-center relative select-none" aria-hidden="true">
                
                <!-- Tarjeta contenedora cuadrada estática y proporcionada -->
                <div class="relative w-full max-w-[380px] xl:max-w-[410px] aspect-square">
                    
                    <!-- Marco decorativo trasero estático (profundidad sin movimiento) -->
                    <div class="absolute inset-0 translate-x-3 translate-y-3 rounded-3xl border-2 border-brand-sky/25 bg-brand-light/60 -z-10"></div>
                    
                    <!-- Tarjeta principal cuadrada con esquinas suaves -->
                    <div class="relative w-full h-full rounded-3xl overflow-hidden bg-brand-dark shadow-[0_22px_45px_rgba(0,61,128,0.18)] border-2 border-white">
                        
                        <!-- Imagen principal -->
                        <img src="{{ asset('images/Img-login.jpg') }}"
                             alt="C.I. Piscícola New York — Producción y Aguas"
                             loading="lazy"
                             decoding="async"
                             class="w-full h-full object-cover object-center">

                        <!-- Gradiente ambiental estático -->
                        <div class="absolute inset-0 bg-gradient-to-t from-brand-dark/85 via-brand-dark/25 to-transparent pointer-events-none"></div>

                        <!-- Difuminado en ola superior sutil tipo header -->
                        <svg class="absolute w-full h-[24%] top-0 left-0 z-10 pointer-events-none opacity-40" viewBox="0 0 600 120" preserveAspectRatio="none">
                            <path d="M0,0 L600,0 L600,50 C480,15 360,95 240,40 C140,-5 60,65 0,45 Z" fill="rgba(14, 165, 233, 0.35)"/>
                        </svg>

                        <!-- ========================================================
                             DIFUMINADO EN TIPO DE OLAS INFERIOR (Conexión con el header)
                             ======================================================== -->
                        <svg class="absolute w-full h-[46%] bottom-0 left-0 z-10 pointer-events-none" viewBox="0 0 600 220" preserveAspectRatio="none">
                            <!-- Capa 1: Difuminado de ola azul cielo traslúcida -->
                            <path d="M0,90 C80,140 200,40 320,95 C430,145 520,70 600,90 L600,220 L0,220 Z"
                                  fill="rgba(14, 165, 233, 0.35)"/>
                            
                            <!-- Capa 2: Ola azul cian intermedia -->
                            <path d="M0,115 C100,60 220,150 340,105 C440,65 530,125 600,110 L600,220 L0,220 Z"
                                  fill="rgba(2, 132, 199, 0.55)"/>

                            <!-- Capa 3: Ola principal azul corporativo profundo que difumina la base -->
                            <path d="M0,135 C90,105 200,165 310,125 C410,90 510,140 600,125 L600,220 L0,220 Z"
                                  fill="rgba(0, 45, 94, 0.88)"/>

                            <!-- Línea de cresta / brillo de espuma idéntica a las olas -->
                            <path d="M0,135 C90,105 200,165 310,125 C410,90 510,140 600,125"
                                  fill="none"
                                  stroke="rgba(224, 242, 254, 0.9)"
                                  stroke-width="2.5"/>
                        </svg>

                        <!-- Texto corporativo integrado sobre el difuminado de olas -->
                      

                    </div>
                </div>

            </div>

        </main>

        <!-- ========================================================
             FOOTER CON OLAS MARINAS (Estilo piscicolanewyork.com)
             ======================================================== -->
        <footer class="w-full relative mt-auto z-30" aria-label="Pie de página">
            <!-- Olas decorativas continuas en la parte superior del Footer -->
            <div class="w-full overflow-hidden leading-none relative pointer-events-none">
                <!-- Capa 1: Sombra de ola azul cian para dar profundidad y dinamismo -->
                <svg class="relative block w-full h-6 sm:h-8 md:h-10 text-brand-sky/25" viewBox="0 0 1440 50" preserveAspectRatio="none">
                    <path d="M0,50 L0,30 C60,5 180,5 240,25 C300,45 420,45 480,25 C540,5 660,5 720,25 C780,45 900,45 960,25 C1020,5 1140,5 1200,25 C1260,45 1380,45 1440,25 L1440,50 Z" fill="currentColor"/>
                </svg>
                <!-- Capa 2: Ola blanca sólida principal conectada con el fondo del pie -->
                <svg class="relative block w-full h-5 sm:h-7 md:h-9 text-white -mt-6 sm:-mt-8 md:-mt-10" viewBox="0 0 1440 50" preserveAspectRatio="none">
                    <path d="M0,50 L0,25 C60,5 180,5 240,25 C300,45 420,45 480,25 C540,5 660,5 720,25 C780,45 900,45 960,25 C1020,5 1140,5 1200,25 C1260,45 1380,45 1440,25 L1440,50 Z" fill="currentColor"/>
                </svg>
            </div>

            <!-- Cuerpo del Footer Blanco -->
            <div class="w-full bg-white py-4 sm:py-5 px-5 sm:px-8 md:px-12 text-center shadow-xs">
                <div class="max-w-[1280px] mx-auto flex flex-col sm:flex-row items-center justify-between gap-3 text-[11px] sm:text-xs text-[#64748b]">
                    <p class="m-0">
                        &copy; {{ date('Y') }} <span class="font-bold text-brand-dark">C.I. Piscícola New York S.A.</span> Todos los derechos reservados.
                    </p>
                    <p class="m-0 flex items-center gap-2">
                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-brand-blue"></span>
                        <span>Huila, Colombia · Sistema de Capacitaciones</span>
                    </p>
                </div>
            </div>
        </footer>
    </div>

    <!-- Script para alternar visibilidad de la contraseña -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toggleBtn = document.getElementById('toggle-password');
            const passInput = document.getElementById('password');
            const iconEye = document.getElementById('icon-eye');
            const iconEyeOff = document.getElementById('icon-eye-off');

            if (toggleBtn && passInput) {
                toggleBtn.addEventListener('click', () => {
                    const isPassword = passInput.type === 'password';
                    passInput.type = isPassword ? 'text' : 'password';

                    toggleBtn.setAttribute('aria-label', isPassword ? 'Ocultar contraseña' : 'Mostrar contraseña');
                    toggleBtn.setAttribute('aria-pressed', isPassword ? 'true' : 'false');

                    if (isPassword) {
                        iconEye.classList.add('hidden');
                        iconEyeOff.classList.remove('hidden');
                    } else {
                        iconEye.classList.remove('hidden');
                        iconEyeOff.classList.add('hidden');
                    }
                });
            }
        });
    </script>
</body>
</html>
