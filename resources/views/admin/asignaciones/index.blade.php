@extends('layouts.admin')

@section('title', 'Asignación de Capacitaciones a Áreas')
@section('page_title', 'Asignación a Áreas')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    @include('admin._banner', [
        'insignia' => 'Segmentación y Alcance',
        'titulo' => 'Asignar Capacitaciones a las Áreas',
        'descripcion' => 'Consulta el catálogo de capacitaciones y despliega las áreas autorizadas para cada curso con un solo clic. Modifica qué departamentos tienen acceso y guarda los cambios.',
        'icono' => 'fa-diagram-project',
    ])

    <!-- BARRA DE RESUMEN Y BÚSQUEDA RÁPIDA -->
    <div class="bg-white p-4 sm:p-5 rounded-3xl border border-line/80 shadow-2xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="w-10 h-10 rounded-2xl bg-brand-light text-brand-blue flex items-center justify-center font-bold text-sm shrink-0">
                <i class="fa-solid fa-layer-group"></i>
            </span>
            <div>
                <span class="block text-sm font-heading font-extrabold text-brand-dark leading-tight">
                    {{ $capacitaciones->count() }} Capacitaciones registradas
                </span>
                <span class="text-[11px] text-muted">Haz clic en una capacitación para desplegar o gestionar sus áreas autorizadas.</span>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" onclick="toggleTodosAcordeones(true)"
                    class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-heading font-bold text-xs transition-colors cursor-pointer">
                <i class="fa-solid fa-angles-down mr-1"></i> Desplegar todas
            </button>
            <button type="button" onclick="toggleTodosAcordeones(false)"
                    class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-heading font-bold text-xs transition-colors cursor-pointer">
                <i class="fa-solid fa-angles-up mr-1"></i> Plegar todas
            </button>
        </div>
    </div>

    <!-- LISTADO DE CAPACITACIONES CON PESTAÑA DESPLEGABLE -->
    <div class="space-y-3.5">
        @forelse ($capacitaciones as $cap)
            @php
                $areasAsignadas = $cap->areas->pluck('id')->all();
                $conteoAreasAsignadas = count($areasAsignadas);
            @endphp
            <div class="bg-white rounded-3xl border border-line/80 shadow-2xs overflow-hidden transition-all hover:border-brand-blue/30 card-capacitacion" id="card-cap-{{ $cap->id }}">
                <form method="POST" action="{{ route('admin.asignaciones.update', $cap) }}">
                    @csrf
                    @method('PUT')

                    <!-- FILA PRINCIPAL: SOLO LA CAPACITACIÓN (Clickable y con hover elegante) -->
                    <div class="p-4 sm:p-5 flex flex-col lg:flex-row lg:items-center justify-between gap-4 cursor-pointer select-none group hover:bg-slate-50/70 transition-colors btn-toggle-acordeon"
                         data-target="desplegable-cap-{{ $cap->id }}"
                         role="button"
                         tabindex="0"
                         title="Haz clic para ver las áreas autorizadas">

                        <div class="flex items-start sm:items-center gap-3.5 min-w-0">
                            <span class="w-11 h-11 rounded-2xl bg-blue-50 text-brand-blue group-hover:bg-brand-blue group-hover:text-white flex items-center justify-center font-bold text-base shrink-0 transition-all shadow-2xs">
                                <i class="fa-solid fa-graduation-cap"></i>
                            </span>

                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="font-heading font-extrabold text-base text-brand-dark group-hover:text-brand-blue transition-colors leading-tight">
                                        {{ $cap->titulo }}
                                    </h3>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $cap->estado ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-600 border-slate-200' }}">
                                        {{ $cap->estado ? 'Activa' : 'Desactivada' }}
                                    </span>
                                </div>

                                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-muted mt-1">
                                    <span>
                                        <i class="fa-solid fa-book-open text-brand-blue mr-1"></i>
                                        {{ $cap->modulos_activos_count }} {{ $cap->modulos_activos_count === 1 ? 'módulo' : 'módulos' }}
                                    </span>
                                    <span>
                                        <i class="fa-solid fa-circle-check text-slate-400 mr-1"></i>
                                        Mín. aprobación: <strong class="text-slate-700">{{ rtrim(rtrim(number_format((float) $cap->porcentaje_aprobacion, 2), '0'), '.') }}%</strong>
                                    </span>
                                    @if ($cap->descripcion)
                                        <span class="line-clamp-1 max-w-md hidden md:inline text-slate-400">
                                            &bull; {{ $cap->descripcion }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- LADO DERECHO: CONTEO DE ÁREAS Y DISPARADOR DE PESTAÑA -->
                        <div class="flex items-center justify-between lg:justify-end gap-3 shrink-0 pt-2 lg:pt-0 border-t lg:border-t-0 border-line/60">
                            <!-- Píldora de áreas autorizadas -->
                            <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold transition-all {{ $conteoAreasAsignadas > 0 ? 'bg-cyan-50 text-cyan-800 border border-cyan-200' : 'bg-amber-50 text-amber-800 border border-amber-200' }}">
                                <i class="fa-solid fa-sitemap text-[11px]"></i>
                                <span>{{ $conteoAreasAsignadas }} {{ $conteoAreasAsignadas === 1 ? 'área autorizada' : 'áreas autorizadas' }}</span>
                            </div>

                            <!-- Botón flecha indicador -->
                            <span class="w-8 h-8 rounded-xl bg-slate-100 text-slate-600 group-hover:bg-brand-blue group-hover:text-white flex items-center justify-center text-xs transition-transform duration-300 icono-flecha"
                                  id="flecha-cap-{{ $cap->id }}">
                                <i class="fa-solid fa-chevron-down"></i>
                            </span>
                        </div>
                    </div>

                    <!-- ========================================================
                         PESTAÑA DESPLEGABLE CON LAS ÁREAS AUTORIZADAS
                         ======================================================== -->
                    <div id="desplegable-cap-{{ $cap->id }}" class="hidden border-t border-line/70 bg-gradient-to-b from-slate-50/70 to-white p-5 sm:p-6 space-y-4">
                        
                        <!-- Barra informativa de la pestaña -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-line/50">
                            <div>
                                <span class="text-[11px] font-bold text-brand-dark uppercase tracking-wider flex items-center gap-1.5">
                                    <i class="fa-solid fa-diagram-project text-brand-blue"></i>
                                    Áreas Autorizadas para esta Capacitación
                                </span>
                                <p class="text-xs text-muted mt-0.5">
                                    Marca las casillas de las áreas que deben cursar esta capacitación y presiona "Guardar Asignación".
                                </p>
                            </div>

                            <button type="submit"
                                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-brand-blue hover:bg-brand-deep text-white font-heading font-bold text-xs shadow-xs transition-colors self-start sm:self-auto cursor-pointer">
                                <i class="fa-solid fa-floppy-disk"></i>
                                <span>Guardar Asignación</span>
                            </button>
                        </div>

                        <!-- Cuadrícula interactiva de áreas con selector -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2.5">
                            @foreach ($areas as $area)
                                @php
                                    $asignada = in_array($area->id, $areasAsignadas, true);
                                    $colaboradores = $empleadosPorArea[$area->id] ?? 0;
                                @endphp
                                <label class="flex items-center justify-between p-3 rounded-2xl border transition-all cursor-pointer {{ $asignada ? 'bg-blue-50/70 border-brand-blue/40 ring-1 ring-brand-blue/20' : 'bg-white border-line/80 hover:bg-slate-50' }}">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <input type="checkbox" name="areas[]" value="{{ $area->id }}" {{ $asignada ? 'checked' : '' }}
                                               class="rounded text-brand-blue focus:ring-brand-blue checkbox-area">
                                        <div class="truncate">
                                            <span class="block text-xs font-bold text-brand-dark truncate leading-tight">{{ $area->nombre }}</span>
                                            <span class="text-[10px] text-muted">{{ $colaboradores }} colaboradores</span>
                                        </div>
                                    </div>
                                    @if ($asignada)
                                        <span class="text-[10px] font-extrabold text-brand-blue bg-white px-2 py-0.5 rounded shadow-2xs border border-blue-100">
                                            Asignada
                                        </span>
                                    @endif
                                </label>
                            @endforeach
                        </div>
                    </div>
                </form>
            </div>
        @empty
            <div class="bg-white rounded-3xl border border-line/80 p-12 text-center text-muted">
                <i class="fa-solid fa-graduation-cap text-3xl mb-2 text-slate-300 block"></i>
                No hay capacitaciones creadas para asignar.
            </div>
        @endforelse
    </div>
</div>

<script>
function toggleAcordeon(targetId, flechaId) {
    const contenedor = document.getElementById(targetId);
    const flecha = document.getElementById(flechaId);

    if (!contenedor) return;

    const estaOculto = contenedor.classList.contains('hidden');
    if (estaOculto) {
        contenedor.classList.remove('hidden');
        if (flecha) flecha.style.transform = 'rotate(180deg)';
    } else {
        contenedor.classList.add('hidden');
        if (flecha) flecha.style.transform = 'rotate(0deg)';
    }
}

function toggleTodosAcordeones(abrir) {
    document.querySelectorAll('[id^="desplegable-cap-"]').forEach(el => {
        const id = el.id.replace('desplegable-cap-', '');
        const flecha = document.getElementById('flecha-cap-' + id);
        if (abrir) {
            el.classList.remove('hidden');
            if (flecha) flecha.style.transform = 'rotate(180deg)';
        } else {
            el.classList.add('hidden');
            if (flecha) flecha.style.transform = 'rotate(0deg)';
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.btn-toggle-acordeon').forEach(btn => {
        btn.addEventListener('click', (e) => {
            // No activar toggle si el click fue dentro de un botón o enlace secundario
            if (e.target.closest('button') || e.target.closest('a') || e.target.closest('input')) {
                return;
            }
            const targetId = btn.getAttribute('data-target');
            const capId = targetId.replace('desplegable-cap-', '');
            toggleAcordeon(targetId, 'flecha-cap-' + capId);
        });

        btn.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                btn.click();
            }
        });
    });
});
</script>
@endsection
