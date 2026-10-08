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
        
        <!-- Header con identidad de marca -->
        <header class="w-full max-w-[1280px] mx-auto px-5 sm:px-6 md:px-10 lg:px-14 pt-6 md:pt-8" aria-label="Identidad de la empresa">
            <div class="flex items-center gap-3.5">
                <div class="h-12 md:h-14 px-2 py-1 bg-white/85 backdrop-blur-xs border border-line rounded-xl flex items-center justify-center shadow-xs">
                    <img src="{{ asset('images/Logo.png') }}" 
                         alt="Logo C.I. Piscícola New York" 
                         class="h-9 md:h-11 w-auto max-w-[140px] object-contain">
                </div>
                <div class="font-heading font-extrabold text-[11px] md:text-[13px] leading-[1.35] tracking-[0.11em] md:tracking-[0.13em] text-brand-dark uppercase select-none">
                    C.I. PISCÍCOLA<br><span class="text-brand-blue font-bold">NEW YORK</span>
                </div>
            </div>
        </header>

        <!-- Contenido principal -->
        <main class="w-full max-w-[1280px] mx-auto px-5 sm:px-6 md:px-10 lg:px-14 py-8 md:py-10 lg:py-12 flex-1 grid grid-cols-1 lg:grid-cols-[minmax(380px,0.85fr)_minmax(460px,1.15fr)] items-center gap-8 lg:gap-14">
            
            <!-- Columna del Formulario de Inicio de Sesión -->
            <section class="w-full max-w-[470px] mx-auto lg:mx-0 lg:pl-3 xl:pl-10" aria-labelledby="login-title">
                
                <!-- Encabezado del Formulario -->
                <div class="flex items-center justify-between gap-3 min-h-[120px] lg:min-h-0 mb-6 lg:mb-10 relative">
                    <div class="w-[62%] sm:w-[65%] lg:w-full relative z-10">
                        <span class="inline-block text-[11px] font-bold uppercase tracking-wider text-brand-blue bg-brand-light px-2.5 py-0.5 rounded-full mb-2">
                            Plataforma de Capacitaciones
                        </span>
                        <h1 id="login-title" class="font-heading font-bold text-[30px] sm:text-4xl lg:text-[43px] tracking-[-0.055em] leading-[1.08] text-brand-dark mb-2.5">
                            Iniciar sesión
                        </h1>
                        <p class="text-[13px] sm:text-sm lg:text-[15px] leading-relaxed text-muted max-w-[210px] sm:max-w-[280px] lg:max-w-[340px]">
                            Ingresa tu número de identificación para acceder al sistema.
                        </p>
                    </div>

                    <!-- Detalle fotográfico con forma de olas visible solo en móvil/tablet -->
                    <div class="w-[118px] h-[118px] sm:w-[130px] sm:h-[130px] shrink-0 relative mr-0.5 lg:hidden" aria-hidden="true">
                        <!-- Ondas orbitales azules -->
                        <div class="absolute -inset-2 border-2 border-brand-sky/40 rounded-[58%_42%_62%_38%/48%_52%_48%_52%] animate-pulse pointer-events-none"></div>
                        <div class="absolute -inset-3.5 border border-brand-blue/20 rounded-[44%_56%_38%_62%/54%_46%_56%_44%] -rotate-12 pointer-events-none"></div>
                        
                        <!-- Contenedor de imagen con silueta de onda -->
                        <div class="w-full h-full rounded-[58%_42%_63%_37%/44%_56%_44%_56%] overflow-hidden shadow-lg border-2 border-white bg-brand-light relative">
                            <img src="{{ asset('images/Img-login.jpg') }}"
                                 alt="Piscicultura y producción C.I. Piscícola New York"
                                 loading="lazy"
                                 decoding="async"
                                 class="w-full h-full object-cover object-center scale-105">
                            <div class="absolute inset-0 bg-gradient-to-t from-brand-deep/30 to-transparent pointer-events-none"></div>
                        </div>
                    </div>
                </div>

                <!-- Formulario -->
                <form class="flex flex-col gap-4.5" method="POST" action="#" onsubmit="event.preventDefault()">
                    @csrf

                    <!-- Campo Número de Identificación -->
                    <div class="flex flex-col gap-2">
                        <label for="identification" class="font-heading font-bold text-[13px] tracking-[-0.01em] text-brand-dark">
                            Número de identificación
                        </label>
                        <div class="h-[54px] flex items-center relative border border-line rounded-xl bg-white/85 transition-all duration-200 focus-within:border-brand-blue focus-within:ring-3 focus-within:ring-brand-blue/15 focus-within:bg-white shadow-2xs">
                            <input type="text"
                                   id="identification"
                                   name="identification"
                                   inputmode="numeric"
                                   autocomplete="username"
                                   placeholder="Ingresa tu número"
                                   required
                                   class="w-full h-full px-4 border-0 outline-none rounded-xl bg-transparent text-ink placeholder-[#94a3b8] text-sm font-medium font-sans">
                        </div>
                    </div>

                    <!-- Campo Contraseña con botón alternar visibilidad -->
                    <div class="flex flex-col gap-2">
                        <label for="password" class="font-heading font-bold text-[13px] tracking-[-0.01em] text-brand-dark">
                            Contraseña
                        </label>
                        <div class="h-[54px] flex items-center relative border border-line rounded-xl bg-white/85 transition-all duration-200 focus-within:border-brand-blue focus-within:ring-3 focus-within:ring-brand-blue/15 focus-within:bg-white shadow-2xs">
                            <input type="password"
                                   id="password"
                                   name="password"
                                   autocomplete="current-password"
                                   placeholder="Ingresa tu contraseña"
                                   required
                                   class="w-full h-full pl-4 pr-12 border-0 outline-none rounded-xl bg-transparent text-ink placeholder-[#94a3b8] text-sm font-medium font-sans">
                            
                            <button type="button"
                                    id="toggle-password"
                                    class="w-[42px] h-[42px] absolute right-1.5 top-1.5 flex items-center justify-center rounded-lg text-muted hover:text-brand-blue hover:bg-brand-light focus-visible:outline-2 focus-visible:outline-brand-blue/50 transition-colors cursor-pointer"
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
                            class="min-h-[54px] w-full mt-1.5 border-0 rounded-xl bg-brand-blue hover:bg-brand-deep active:bg-brand-dark text-white font-heading font-bold text-sm tracking-[0.01em] shadow-[0_10px_20px_rgba(0,86,179,0.22)] hover:shadow-[0_12px_24px_rgba(0,86,179,0.28)] hover:-translate-y-0.5 active:translate-y-0 focus-visible:outline-3 focus-visible:outline-brand-blue/35 focus-visible:outline-offset-2 transition-all duration-200 cursor-pointer flex items-center justify-center gap-2 group">
                        <span>Iniciar sesión</span>
                        <svg class="w-4 h-4 transition-transform group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                        </svg>
                    </button>
                </form>

                <!-- Nota al pie del formulario -->
                <p class="flex items-center justify-center lg:justify-start gap-1.5 mt-5 text-muted text-[11px] md:text-xs leading-relaxed">
                    <svg class="w-3.5 h-3.5 text-brand-blue shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Acceso exclusivo para colaboradores de C.I. Piscícola New York.</span>
                </p>
            </section>

            <!-- Columna Multimedia con Diseño de Olas Azules para Desktop -->
            <div class="hidden lg:block relative min-h-[540px] xl:min-h-[600px] h-[min(65vw,660px)] max-h-[740px] select-none pointer-events-none" aria-hidden="true">
                
                <!-- Ondas orbitales exteriores en gradientes de azul -->
                <div class="wave-ring-2 absolute w-[min(59vw,690px)] h-[min(54vw,630px)] max-w-[690px] max-h-[630px] top-1/2 left-1/2 border-2 border-brand-sky/25 pointer-events-none"></div>
                <div class="wave-ring-1 absolute w-[min(55vw,650px)] h-[min(55vw,650px)] max-w-[650px] max-h-[650px] top-1/2 left-1/2 border-2 border-brand-blue/35 pointer-events-none"></div>

                <!-- Silueta de ola orgánica principal que contiene img-login -->
                <div class="wave-shape w-[min(52vw,610px)] h-[min(52vw,610px)] max-w-[610px] max-h-[610px] absolute top-1/2 left-1/2 overflow-hidden bg-brand-light shadow-[0_24px_56px_rgba(0,61,128,0.18)] border-4 border-white/80">
                    
                    <img src="{{ asset('images/Img-login.jpg') }}"
                         alt="C.I. Piscícola New York — Producción y Aguas"
                         loading="lazy"
                         decoding="async"
                         class="w-full h-full object-cover object-center scale-105">

                    <!-- Overlay sutil con gradiente azul oceánico -->
                    <div class="absolute inset-0 bg-gradient-to-tr from-brand-deep/35 via-transparent to-brand-sky/15 pointer-events-none"></div>

                    <!-- Ondas de agua SVG dinámicas en tonos azules que fluyen sobre la imagen -->
                    <svg class="absolute w-full h-[38%] bottom-0 left-0 z-10 pointer-events-none" viewBox="0 0 600 200" preserveAspectRatio="none">
                        <!-- Capa de ola trasera semitransparente -->
                        <path d="M0,110 C150,170 320,60 460,120 C540,150 580,130 600,125 L600,200 L0,200 Z"
                              fill="rgba(2, 132, 199, 0.35)" />
                        <!-- Capa de ola intermedia azul corporativo -->
                        <path d="M0,135 C180,80 300,165 440,115 C520,85 570,120 600,110 L600,200 L0,200 Z"
                              fill="rgba(0, 86, 179, 0.55)" />
                        <!-- Cresta de ola frontal brillante con borde claro -->
                        <path d="M0,155 C140,125 280,180 420,140 C500,118 560,145 600,135 L600,200 L0,200 Z"
                              fill="rgba(0, 61, 128, 0.75)" />
                        <!-- Línea de espuma / brillo de ola -->
                        <path d="M0,155 C140,125 280,180 420,140 C500,118 560,145 600,135"
                              fill="none"
                              stroke="rgba(224, 242, 254, 0.85)"
                              stroke-width="2" />
                    </svg>

                    <!-- Cresta de onda superior sutil -->
                    <svg class="absolute w-[80%] h-[25%] top-0 right-0 z-10 pointer-events-none opacity-45" viewBox="0 0 400 100" preserveAspectRatio="none">
                        <path d="M0,0 L400,0 L400,40 C320,85 220,15 120,55 C60,75 20,40 0,30 Z"
                              fill="rgba(14, 165, 233, 0.3)" />
                    </svg>
                </div>
            </div>

        </main>

        <!-- Footer / Espaciador inferior corporativo -->
        <footer class="w-full py-4 text-center text-[11px] text-[#64748b] border-t border-line/60 bg-white/40 backdrop-blur-xs">
            &copy; {{ date('Y') }} C.I. Piscícola New York S.A. Todos los derechos reservados.
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
