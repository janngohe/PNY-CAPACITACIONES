{{-- Banner superior reutilizable del panel administrador.
     Parámetros: $titulo, $descripcion, $insignia (opcional), $icono (opcional, clase FontAwesome) --}}
<div class="relative rounded-3xl bg-gradient-to-r from-brand-dark via-brand-deep to-[#0056b3] text-white p-6 sm:p-8 overflow-hidden shadow-lg border border-brand-blue/20">
    <div class="absolute -right-10 -top-10 w-64 h-64 rounded-full bg-brand-sky/15 blur-2xl pointer-events-none"></div>
    <div class="absolute right-1/3 -bottom-16 w-80 h-80 rounded-full bg-brand-blue/20 blur-3xl pointer-events-none"></div>

    <div class="relative z-10 flex items-center justify-between gap-6">
        <div class="max-w-2xl">
            @isset($insignia)
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-xs font-semibold text-brand-sky mb-3">
                    <span class="w-2 h-2 rounded-full bg-brand-sky animate-ping"></span>
                    <span>{{ $insignia }}</span>
                </div>
            @endisset
            <h1 class="font-heading font-extrabold text-2xl sm:text-3xl tracking-tight leading-tight text-white">{{ $titulo }}</h1>
            <p class="mt-2 text-xs sm:text-sm text-slate-200/90 leading-relaxed">{{ $descripcion }}</p>
        </div>
        @isset($icono)
            <div class="hidden md:flex w-20 h-20 rounded-3xl bg-white/10 backdrop-blur-md border border-white/20 items-center justify-center text-4xl text-brand-sky shrink-0">
                <i class="fa-solid {{ $icono }}"></i>
            </div>
        @endisset
    </div>

    <div class="absolute bottom-0 left-0 w-full overflow-hidden leading-none pointer-events-none opacity-30">
        <svg class="relative block w-full h-4 text-white" viewBox="0 0 1200 40" preserveAspectRatio="none">
            <path d="M0,0 C150,35 350,10 500,25 C650,40 850,5 1000,20 C1100,30 1160,15 1200,25 L1200,40 L0,40 Z" fill="currentColor"/>
        </svg>
    </div>
</div>
