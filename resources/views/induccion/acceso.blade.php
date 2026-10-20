<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Acceso Facilitador · Inducciones · C.I. Piscícola New York</title>

    <link rel="icon" type="image/png" href="{{ asset('images/Logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400..700;1,9..40,400..700&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full font-sans antialiased bg-radial from-slate-900 via-slate-950 to-slate-950 text-slate-100 flex items-center justify-center p-4">

    <!-- Fondo decorativo sutil -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none opacity-20">
        <div class="absolute -top-40 -right-40 w-96 h-96 rounded-full bg-cyan-500 blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 rounded-full bg-blue-600 blur-3xl"></div>
    </div>

    <div class="w-full max-w-md relative z-10">
        <!-- Logo y Encabezado -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-xl shadow-2xl p-3 mb-4">
                <img src="{{ asset('images/Logo.png') }}" alt="C.I. Piscícola New York" class="w-full h-full object-contain">
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wider bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 mb-2">
                <i class="fa-solid fa-chalkboard-user"></i> Portal de Inducción
            </span>
            <h1 class="text-2xl font-black text-white font-serif">C.I. Piscícola New York</h1>
            <p class="text-sm text-slate-400 mt-1">Acceso para Facilitadores e Instructores</p>
        </div>

        <!-- Tarjeta de Acceso -->
        <div class="bg-slate-800/80 backdrop-blur-xl border border-slate-700/60 rounded-3xl p-6 sm:p-8 shadow-2xl">
            @if(session('error'))
                <div class="mb-5 p-4 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-sm flex items-start gap-3">
                    <i class="fa-solid fa-circle-exclamation mt-0.5 text-rose-400"></i>
                    <div>{{ session('error') }}</div>
                </div>
            @endif

            @if(session('info'))
                <div class="mb-5 p-4 rounded-2xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-300 text-sm flex items-start gap-3">
                    <i class="fa-solid fa-circle-info mt-0.5 text-cyan-400"></i>
                    <div>{{ session('info') }}</div>
                </div>
            @endif

            <form action="{{ route('induccion.acceso.post') }}" method="POST" class="space-y-5">
                @csrf

                <!-- Campo Cédula -->
                <div>
                    <label for="identificacion" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                        <i class="fa-solid fa-id-card text-cyan-400 mr-1.5"></i> Número de Cédula (CC)
                    </label>
                    <div class="relative">
                        <input type="text"
                               name="identificacion"
                               id="identificacion"
                               value="{{ old('identificacion') }}"
                               placeholder="Ej: 1075254123"
                               required
                               autocomplete="off"
                               class="w-full px-4 py-3.5 bg-slate-900/80 border border-slate-700 rounded-2xl text-white placeholder-slate-500 focus:outline-none focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/20 transition-all font-mono text-base">
                    </div>
                    @error('identificacion')
                        <p class="text-rose-400 text-xs mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Campo PIN de 4 Dígitos -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="pin" class="block text-xs font-bold uppercase tracking-wider text-slate-300">
                            <i class="fa-solid fa-key text-cyan-400 mr-1.5"></i> PIN de Facilitador (4 dígitos)
                        </label>
                        <span class="text-[11px] text-slate-400">Código de Sala</span>
                    </div>
                    <div class="relative">
                        <input type="password"
                               name="pin"
                               id="pin"
                               maxlength="4"
                               pattern="\d{4}"
                               inputmode="numeric"
                               placeholder="••••"
                               required
                               class="w-full text-center tracking-[0.5em] px-4 py-3.5 bg-slate-900/80 border border-slate-700 rounded-2xl text-white placeholder-slate-600 focus:outline-none focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/20 transition-all font-mono text-xl font-bold">
                    </div>
                    @error('pin')
                        <p class="text-rose-400 text-xs mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                    <p class="text-slate-400 text-xs mt-2 flex items-center gap-1.5">
                        <i class="fa-solid fa-circle-question text-slate-500"></i>
                        Si no conoces el PIN, solicítalo al Administrador o a tu Jefe de Área.
                    </p>
                </div>

                <!-- Botón de Ingreso -->
                <button type="submit"
                        class="w-full py-4 px-6 rounded-2xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white font-bold text-sm tracking-wide shadow-lg shadow-cyan-500/20 hover:shadow-cyan-500/30 transition-all duration-200 flex items-center justify-center gap-2 group cursor-pointer mt-2">
                    <span>Ingresar a Inducciones</span>
                    <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </button>
            </form>

            <div class="mt-6 pt-6 border-t border-slate-700/60 text-center">
                <a href="{{ route('login') }}" class="text-xs text-slate-400 hover:text-slate-200 transition-colors inline-flex items-center gap-1.5">
                    <i class="fa-solid fa-arrow-left"></i> Volver al Login Principal
                </a>
            </div>
        </div>

        <!-- Pie de página -->
        <p class="text-center text-xs text-slate-500 mt-6">
            Plataforma Institucional de Capacitaciones e Inducciones &copy; {{ date('Y') }}
        </p>
    </div>

</body>
</html>
