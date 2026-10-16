@extends('layouts.empleado')

@section('title', 'Capacitaciones e Inducciones')
@section('page_title', 'Mis Capacitaciones')

@push('styles')
<style>
    .no-scrollbar::-webkit-scrollbar {
        display: none !important;
        width: 0 !important;
        height: 0 !important;
    }
    .no-scrollbar {
        -ms-overflow-style: none !important;
        scrollbar-width: none !important;
    }
</style>
@endpush

@section('content')
<div class="space-y-6 sm:space-y-8 max-w-7xl mx-auto">

    <!-- ========================================================
         BANNER DE BIENVENIDA CON NOMBRE COMPLETO Y EFECTO DE OLAS
         ======================================================== -->
    <div class="relative rounded-3xl bg-gradient-to-r from-brand-dark via-brand-deep to-[#0056b3] text-white p-6 sm:p-8 md:p-10 overflow-hidden shadow-lg border border-brand-blue/20">
        
        <div class="absolute -right-10 -top-10 w-64 h-64 rounded-full bg-brand-sky/15 blur-2xl pointer-events-none"></div>
        <div class="absolute right-1/3 -bottom-16 w-80 h-80 rounded-full bg-brand-blue/20 blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-xs font-semibold text-brand-sky mb-3">
                    <span class="w-2 h-2 rounded-full bg-brand-sky animate-ping"></span>
                    <span>Área: {{ $usuario->area->nombre ?? ($usuario->area_nombre ?? 'Producción Piscícola') }}</span>
                </div>

                <h1 class="font-heading font-extrabold text-2xl sm:text-3xl md:text-4xl tracking-tight leading-tight text-white">
                    ¡Bienvenido, <span class="text-transparent bg-clip-text bg-gradient-to-r from-white via-cyan-100 to-brand-sky">{{ $usuario->nombre_completo ?? 'Colaborador' }}</span>!
                </h1>


                <div class="mt-4 flex flex-wrap items-center gap-3 text-xs text-slate-300">
                    <span class="inline-flex items-center gap-1.5 bg-black/20 px-2.5 py-1 rounded-lg">
                        <svg class="w-3.5 h-3.5 text-brand-sky" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                        Identificación: <strong class="text-white">{{ $usuario->identificacion ?? '---' }}</strong>
                    </span>
                    <span class="inline-flex items-center gap-1.5 bg-black/20 px-2.5 py-1 rounded-lg">
                        <svg class="w-3.5 h-3.5 text-brand-sky" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Rol: <strong class="text-white">{{ $usuario->rol ?? 'EMPLEADO' }}</strong>
                    </span>
                </div>
            </div>
        </div>

        <div class="absolute bottom-0 left-0 w-full overflow-hidden leading-none pointer-events-none opacity-30">
            <svg class="relative block w-full h-5 text-white" viewBox="0 0 1200 40" preserveAspectRatio="none">
                <path d="M0,0 C150,35 350,10 500,25 C650,40 850,5 1000,20 C1100,30 1160,15 1200,25 L1200,40 L0,40 Z" fill="currentColor"/>
            </svg>
        </div>
    </div>


    <!-- NOTIFICACIONES FLASH -->
    @if(session('success_modulo'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-semibold flex items-center gap-2.5 shadow-2xs" role="status">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ session('success_modulo') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs sm:text-sm font-semibold flex items-center gap-2.5 shadow-2xs" role="alert">
            <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif


    <!-- ========================================================
         SECCIÓN PRINCIPAL DE CAPACITACIONES CON VISTA SEPARADA
         ======================================================== -->
    <div id="seccion-listado-capacitaciones" class="space-y-5">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4 border-b border-line/70 pb-4">
            <div>
                <h2 class="font-heading font-extrabold text-base sm:text-xl text-brand-dark flex items-center gap-2">
                    <span>Mis Capacitaciones e Inducciones</span>
                </h2>
                <p id="subtitulo-filtro-capacitaciones" class="text-xs text-muted mt-0.5 leading-relaxed">
                    Visualiza tus cursos activos en progreso o consulta tus capacitaciones ya completadas.
                </p>
            </div>

            <!-- Selector de Pestañas / Tabs Responsivo -->
            <div class="w-full sm:w-auto p-1 rounded-2xl bg-slate-100 border border-slate-200/80 shadow-2xs overflow-x-auto no-scrollbar"
                 style="scrollbar-width: none; -ms-overflow-style: none;">
                <div class="flex items-center gap-1 min-w-full sm:min-w-0">
                    <!-- Tab Pendientes / En Progreso -->
                    <button type="button"
                            id="tab-btn-pendientes"
                            onclick="cambiarPestanaCapacitaciones('pendientes')"
                            class="tab-filtro-btn flex-1 sm:flex-initial justify-center whitespace-nowrap px-2.5 sm:px-4 py-2 rounded-xl text-xs font-heading font-bold transition-all duration-200 flex items-center gap-1.5 sm:gap-2 cursor-pointer bg-brand-blue text-white shadow-xs">
                        <i class="fa-solid fa-clock-rotate-left text-xs shrink-0"></i>
                        <span class="inline sm:hidden">Pendientes</span>
                        <span class="hidden sm:inline">En Curso / Pendientes</span>
                        <span id="badge-count-pendientes" class="px-1.5 sm:px-2 py-0.5 rounded-full text-[10px] bg-white/20 text-white font-extrabold shrink-0">
                            {{ $capacitacionesPendientes->count() }}
                        </span>
                    </button>

                    <!-- Tab Completadas -->
                    <button type="button"
                            id="tab-btn-completadas"
                            onclick="cambiarPestanaCapacitaciones('completadas')"
                            class="tab-filtro-btn flex-1 sm:flex-initial justify-center whitespace-nowrap px-2.5 sm:px-4 py-2 rounded-xl text-xs font-heading font-bold transition-all duration-200 flex items-center gap-1.5 sm:gap-2 cursor-pointer text-slate-600 hover:text-brand-dark hover:bg-white/60">
                        <i class="fa-solid fa-circle-check text-xs text-emerald-600 shrink-0"></i>
                        <span>Completadas</span>
                        <span id="badge-count-completadas" class="px-1.5 sm:px-2 py-0.5 rounded-full text-[10px] bg-slate-200 text-slate-700 font-extrabold shrink-0">
                            {{ $capacitacionesCompletadas->count() }}
                        </span>
                    </button>

                    <!-- Tab Todas (Opcional) -->
                    <button type="button"
                            id="tab-btn-todas"
                            onclick="cambiarPestanaCapacitaciones('todas')"
                            class="tab-filtro-btn flex-1 sm:flex-initial justify-center whitespace-nowrap px-2.5 sm:px-4 py-2 rounded-xl text-xs font-heading font-bold transition-all duration-200 flex items-center gap-1.5 sm:gap-2 cursor-pointer text-slate-600 hover:text-brand-dark hover:bg-white/60">
                        <span>Todas</span>
                        <span id="badge-count-todas" class="px-1.5 sm:px-2 py-0.5 rounded-full text-[10px] bg-slate-200 text-slate-700 font-extrabold shrink-0">
                            {{ $capacitaciones->count() }}
                        </span>
                    </button>
                </div>
            </div>
        </div>

        <!-- 1. PANEL: CAPACITACIONES PENDIENTES / EN PROGRESO (Activo por defecto) -->
        <div id="panel-capacitaciones-pendientes" class="grid grid-cols-1 md:grid-cols-2 gap-5 sm:gap-6">
            @forelse($capacitacionesPendientes as $cap)
                @include('empleado._tarjeta_capacitacion', ['cap' => $cap])
            @empty
                <div class="col-span-full bg-white rounded-3xl border border-line/80 p-8 sm:p-12 text-center space-y-3">
                    <div class="w-16 h-16 mx-auto rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl shadow-2xs">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                    <h3 class="font-heading font-extrabold text-base sm:text-lg text-brand-dark">¡Estás al día! No tienes cursos pendientes</h3>
                    <p class="text-xs text-muted max-w-md mx-auto leading-relaxed">
                        Has completado todas las capacitaciones asignadas hasta la fecha. Puedes consultar tus certificados o repasar el material de tus cursos finalizados.
                    </p>
                    @if($capacitacionesCompletadas->isNotEmpty())
                        <div class="pt-2">
                            <button type="button"
                                    onclick="cambiarPestanaCapacitaciones('completadas')"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-heading font-bold text-xs shadow-xs transition-all cursor-pointer">
                                <i class="fa-solid fa-graduation-cap"></i>
                                <span>Ver Mis Capacitaciones Completadas</span>
                            </button>
                        </div>
                    @endif
                </div>
            @endforelse
        </div>

        <!-- 2. PANEL: CAPACITACIONES YA COMPLETADAS (Aparte, limpio) -->
        <div id="panel-capacitaciones-completadas" class="grid grid-cols-1 md:grid-cols-2 gap-5 sm:gap-6 hidden">
            @forelse($capacitacionesCompletadas as $cap)
                @include('empleado._tarjeta_capacitacion', ['cap' => $cap])
            @empty
                <div class="col-span-full bg-white rounded-3xl border border-line/80 p-8 sm:p-12 text-center space-y-3">
                    <div class="w-16 h-16 mx-auto rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-2xl">
                        <i class="fa-solid fa-award"></i>
                    </div>
                    <h3 class="font-heading font-extrabold text-base sm:text-lg text-brand-dark">Aún no tienes capacitaciones completadas</h3>
                    <p class="text-xs text-muted max-w-md mx-auto leading-relaxed">
                        A medida que finalices los módulos y apruebes las evaluaciones de tus cursos asignados, aparecerán archivados aquí con su certificado digital disponible.
                    </p>
                    @if($capacitacionesPendientes->isNotEmpty())
                        <div class="pt-2">
                            <button type="button"
                                    onclick="cambiarPestanaCapacitaciones('pendientes')"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-blue hover:bg-brand-deep text-white font-heading font-bold text-xs shadow-xs transition-all cursor-pointer">
                                <i class="fa-solid fa-play text-[11px]"></i>
                                <span>Ir a Mis Cursos en Curso / Pendientes</span>
                            </button>
                        </div>
                    @endif
                </div>
            @endforelse
        </div>

        <!-- 3. PANEL: TODAS LAS CAPACITACIONES (Vista Global) -->
        <div id="panel-capacitaciones-todas" class="grid grid-cols-1 md:grid-cols-2 gap-5 sm:gap-6 hidden">
            @forelse($capacitaciones as $cap)
                @include('empleado._tarjeta_capacitacion', ['cap' => $cap])
            @empty
                <div class="col-span-full bg-white rounded-3xl border border-line/80 p-10 text-center space-y-3">
                    <div class="w-14 h-14 mx-auto rounded-2xl bg-blue-50 text-brand-blue flex items-center justify-center">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                        </svg>
                    </div>
                    <h3 class="font-heading font-extrabold text-base text-brand-dark">No hay capacitaciones asignadas actualmente</h3>
                    <p class="text-xs text-muted max-w-sm mx-auto">
                        Tan pronto el administrador o jefe de área te asigne un nuevo plan de inducción o capacitación, lo verás disponible en este panel.
                    </p>
                </div>
            @endforelse
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
function cambiarPestanaCapacitaciones(tipo) {
    const panelPendientes = document.getElementById('panel-capacitaciones-pendientes');
    const panelCompletadas = document.getElementById('panel-capacitaciones-completadas');
    const panelTodas = document.getElementById('panel-capacitaciones-todas');

    const btnPendientes = document.getElementById('tab-btn-pendientes');
    const btnCompletadas = document.getElementById('tab-btn-completadas');
    const btnTodas = document.getElementById('tab-btn-todas');

    const subtitulo = document.getElementById('subtitulo-filtro-capacitaciones');

    const baseTabClasses = 'tab-filtro-btn flex-1 sm:flex-initial justify-center whitespace-nowrap px-2.5 sm:px-4 py-2 rounded-xl text-xs font-heading font-bold transition-all duration-200 flex items-center gap-1.5 sm:gap-2 cursor-pointer';
    const inactiveTabClasses = baseTabClasses + ' text-slate-600 hover:text-brand-dark hover:bg-white/60 bg-transparent';
    const inactiveBadgeClasses = 'px-1.5 sm:px-2 py-0.5 rounded-full text-[10px] bg-slate-200 text-slate-700 font-extrabold shrink-0';
    const activeBadgeClasses = 'px-1.5 sm:px-2 py-0.5 rounded-full text-[10px] bg-white/20 text-white font-extrabold shrink-0';

    // Desactivar todos los botones
    [btnPendientes, btnCompletadas, btnTodas].forEach(btn => {
        if (!btn) return;
        btn.className = inactiveTabClasses;
        const badge = btn.querySelector('span[id^="badge-count-"]');
        if (badge) {
            badge.className = inactiveBadgeClasses;
        }
    });

    // Ocultar todos los paneles
    if (panelPendientes) panelPendientes.classList.add('hidden');
    if (panelCompletadas) panelCompletadas.classList.add('hidden');
    if (panelTodas) panelTodas.classList.add('hidden');

    if (tipo === 'completadas') {
        if (panelCompletadas) panelCompletadas.classList.remove('hidden');
        if (btnCompletadas) {
            btnCompletadas.className = baseTabClasses + ' bg-emerald-600 text-white shadow-xs';
            const badge = document.getElementById('badge-count-completadas');
            if (badge) badge.className = activeBadgeClasses;
        }
        if (subtitulo) subtitulo.textContent = 'Capacitaciones finalizadas satisfactoriamente con sus certificados disponibles.';
        if (history.replaceState) history.replaceState(null, null, '#completadas');
    } else if (tipo === 'todas') {
        if (panelTodas) panelTodas.classList.remove('hidden');
        if (btnTodas) {
            btnTodas.className = baseTabClasses + ' bg-slate-800 text-white shadow-xs';
            const badge = document.getElementById('badge-count-todas');
            if (badge) badge.className = activeBadgeClasses;
        }
        if (subtitulo) subtitulo.textContent = 'Listado global de todos los cursos e inducciones asignados a tu usuario.';
        if (history.replaceState) history.replaceState(null, null, '#todas');
    } else {
        // 'pendientes'
        if (panelPendientes) panelPendientes.classList.remove('hidden');
        if (btnPendientes) {
            btnPendientes.className = baseTabClasses + ' bg-brand-blue text-white shadow-xs';
            const badge = document.getElementById('badge-count-pendientes');
            if (badge) badge.className = activeBadgeClasses;
        }
        if (subtitulo) subtitulo.textContent = 'Cursos asignados en los que debes avanzar o que tienes pendientes por iniciar.';
        if (history.replaceState) history.replaceState(null, null, '#pendientes');
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const hash = window.location.hash;
    if (hash === '#completadas') {
        cambiarPestanaCapacitaciones('completadas');
    } else if (hash === '#todas') {
        cambiarPestanaCapacitaciones('todas');
    } else {
        @if($capacitacionesPendientes->isEmpty() && $capacitacionesCompletadas->isNotEmpty())
            cambiarPestanaCapacitaciones('completadas');
        @else
            cambiarPestanaCapacitaciones('pendientes');
        @endif
    }
});
</script>
@endpush
