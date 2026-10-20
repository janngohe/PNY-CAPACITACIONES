@extends('layouts.admin')

@section('title', 'Catálogo de Beneficios y Puntos')
@section('page_title', 'Beneficios e Incentivos')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    @include('admin._banner', [
        'insignia' => 'Sistema de Incentivos',
        'titulo' => 'Catálogo de Beneficios y Puntos Acumulables',
        'descripcion' => 'Administra los beneficios desbloqueables por puntos para los colaboradores. Los empleados acumulan puntos al completar satisfactoriamente capacitaciones configuradas tanto por la administración como por jefes de área.',
        'icono' => 'fa-gift',
    ])

    <!-- MÉTRICAS RÁPIDAS -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-line/80 p-4 shadow-2xs flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg font-bold shrink-0">
                <i class="fa-solid fa-gift"></i>
            </div>
            <div>
                <span class="text-[11px] text-muted font-bold block uppercase tracking-wider">Total Beneficios</span>
                <span class="font-heading font-extrabold text-xl text-brand-dark">{{ $beneficios->count() }}</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-line/80 p-4 shadow-2xs flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg font-bold shrink-0">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <span class="text-[11px] text-muted font-bold block uppercase tracking-wider">Activos</span>
                <span class="font-heading font-extrabold text-xl text-brand-dark">{{ $beneficios->where('estado', true)->count() }}</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-line/80 p-4 shadow-2xs flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-blue-50 text-brand-blue flex items-center justify-center text-lg font-bold shrink-0">
                <i class="fa-solid fa-ticket"></i>
            </div>
            <div>
                <span class="text-[11px] text-muted font-bold block uppercase tracking-wider">Canjes Realizados</span>
                <span class="font-heading font-extrabold text-xl text-brand-dark">{{ $totalCanjes ?? ($estadisticas['total_canjes'] ?? 0) }}</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-line/80 p-4 shadow-2xs flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-lg font-bold shrink-0">
                <i class="fa-solid fa-star"></i>
            </div>
            <div>
                <span class="text-[11px] text-muted font-bold block uppercase tracking-wider">Puntos Canjeados</span>
                <span class="font-heading font-extrabold text-xl text-brand-dark">{{ number_format($puntosCanjeados ?? ($estadisticas['puntos_canjeados'] ?? 0)) }}</span>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl flex items-center gap-3 text-sm">
            <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl flex items-center gap-3 text-sm">
            <i class="fa-solid fa-triangle-exclamation text-rose-600 text-base"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

        <!-- FORMULARIO DE CREAR / EDITAR BENEFICIO -->
        <div class="bg-white rounded-3xl border border-line/80 shadow-2xs p-6 space-y-4">
            <div class="border-b border-line/60 pb-3">
                <span class="text-[10px] uppercase font-bold text-brand-blue tracking-wider">Configuración</span>
                <h3 id="form-beneficio-title" class="font-heading font-extrabold text-base text-brand-dark">Registrar Nuevo Beneficio</h3>
            </div>

            <form id="form-beneficio" method="POST" action="{{ route('admin.beneficios.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="_method" id="form-beneficio-method" value="POST">

                <div>
                    <label class="block text-xs font-bold text-brand-dark mb-1">Nombre del Beneficio *</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-muted">
                            <i class="fa-solid fa-gift text-xs"></i>
                        </span>
                        <input type="text"
                               name="nombre"
                               id="beneficio-nombre"
                               required
                               placeholder="Ej: Día libre de descanso, Bono almuerzo..."
                               value="{{ old('nombre') }}"
                               class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-line focus:outline-hidden focus:border-brand-blue bg-white text-brand-dark">
                    </div>
                    @error('nombre')
                        <p class="text-rose-600 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-brand-dark mb-1">Descripción *</label>
                    <textarea name="descripcion"
                              id="beneficio-descripcion"
                              rows="3"
                              required
                              placeholder="Detalla en qué consiste el beneficio y cómo el colaborador podrá disfrutarlo..."
                              class="w-full p-3 text-xs rounded-xl border border-line focus:outline-hidden focus:border-brand-blue bg-white text-brand-dark resize-none">{{ old('descripcion') }}</textarea>
                    @error('descripcion')
                        <p class="text-rose-600 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-brand-dark mb-1">Puntos Requeridos *</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-amber-500 font-bold">
                                <i class="fa-solid fa-star text-xs"></i>
                            </span>
                            <input type="number"
                                   name="puntos_requeridos"
                                   id="beneficio-puntos"
                                   min="1"
                                   max="100000"
                                   step="1"
                                   required
                                   value="{{ old('puntos_requeridos', 50) }}"
                                   class="w-full pl-9 pr-3 py-2 text-xs font-bold rounded-xl border border-line focus:outline-hidden focus:border-brand-blue bg-white text-brand-dark">
                        </div>
                        @error('puntos_requeridos')
                            <p class="text-rose-600 text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-brand-dark mb-1">Ícono FontAwesome</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-muted">
                                <i class="fa-solid fa-icons text-xs"></i>
                            </span>
                            <input type="text"
                                   name="icono"
                                   id="beneficio-icono"
                                   placeholder="fa-gift"
                                   value="{{ old('icono', 'fa-gift') }}"
                                   class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-line focus:outline-hidden focus:border-brand-blue bg-white text-brand-dark">
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-1">
                    <input type="checkbox"
                           name="estado"
                           id="beneficio-estado"
                           value="1"
                           {{ old('estado', true) ? 'checked' : '' }}
                           class="w-4 h-4 rounded text-brand-blue focus:ring-brand-blue border-line">
                    <label for="beneficio-estado" class="text-xs font-semibold text-brand-dark cursor-pointer">
                        Beneficio activo y visible en el catálogo
                    </label>
                </div>

                <div class="flex gap-2 pt-2">
                    <button type="submit" id="btn-submit-beneficio" class="flex-1 py-2.5 px-4 bg-brand-blue hover:bg-brand-blue/90 text-white rounded-xl font-bold text-xs shadow-2xs transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Guardar Beneficio</span>
                    </button>
                    <button type="button" id="btn-cancel-beneficio" onclick="resetBeneficioForm()" class="hidden py-2.5 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-bold text-xs transition">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>

        <!-- LISTADO DE BENEFICIOS -->
        <div class="lg:col-span-2 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-heading font-extrabold text-base text-brand-dark">Catálogo Vigente</h3>
                    <p class="text-xs text-muted">Beneficios configurados para desbloqueo por puntos acumulables.</p>
                </div>
            </div>

            @if($beneficios->isEmpty())
                <div class="bg-white rounded-3xl border border-line/80 p-8 text-center space-y-3">
                    <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-2xl mx-auto">
                        <i class="fa-solid fa-gift"></i>
                    </div>
                    <h4 class="font-heading font-bold text-base text-brand-dark">Sin beneficios registrados</h4>
                    <p class="text-xs text-muted max-w-sm mx-auto">Crea el primer beneficio en el formulario lateral para que los colaboradores comiencen a canjear sus puntos acumulados.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($beneficios as $b)
                        <div class="bg-white rounded-2xl border border-line/80 p-5 shadow-2xs space-y-3 flex flex-col justify-between hover:border-brand-blue/40 transition">
                            <div class="space-y-2.5">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-11 h-11 rounded-xl {{ $b->estado ? 'bg-amber-50 text-amber-600' : 'bg-slate-100 text-slate-400' }} flex items-center justify-center text-lg shrink-0">
                                            <i class="fa-solid {{ $b->icono ?: 'fa-gift' }}"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-heading font-bold text-sm text-brand-dark line-clamp-1">{{ $b->nombre }}</h4>
                                            <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-amber-100/70 text-amber-800 text-[11px] font-extrabold">
                                                <i class="fa-solid fa-star text-[10px]"></i>
                                                <span>{{ number_format($b->puntos_requeridos) }} puntos</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Estado toggle -->
                                    <form method="POST" action="{{ route('admin.beneficios.toggle-estado', $b) }}">
                                        @csrf
                                        <button type="submit" title="Cambiar estado" class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase transition {{ $b->estado ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                                            {{ $b->estado ? 'Activo' : 'Inactivo' }}
                                        </button>
                                    </form>
                                </div>

                                <p class="text-xs text-muted line-clamp-3 leading-relaxed">{{ $b->descripcion }}</p>
                            </div>

                            <div class="pt-3 border-t border-line/60 flex items-center justify-between text-xs">
                                <span class="text-[11px] text-muted flex items-center gap-1">
                                    <i class="fa-solid fa-ticket text-slate-400"></i>
                                    <span>{{ $b->canjes_count }} canje(s)</span>
                                </span>

                                <div class="flex items-center gap-1.5">
                                    <button type="button"
                                            onclick='editarBeneficio(@json($b), "{{ route('admin.beneficios.update', $b) }}")'
                                            class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-brand-blue transition flex items-center gap-1">
                                        <i class="fa-solid fa-pen-to-square text-[10px]"></i>
                                        <span>Editar</span>
                                    </button>

                                    <form method="POST" action="{{ route('admin.beneficios.destroy', $b) }}" onsubmit="return confirm('¿Seguro que deseas eliminar este beneficio?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition" title="Eliminar">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- HISTORIAL DE CANJES RECIENTES -->
            @if($canjesRecientes->isNotEmpty())
                <div class="bg-white rounded-3xl border border-line/80 shadow-2xs p-5 space-y-3 mt-6">
                    <div class="flex items-center justify-between">
                        <h4 class="font-heading font-extrabold text-sm text-brand-dark flex items-center gap-2">
                            <i class="fa-solid fa-receipt text-brand-blue"></i>
                            <span>Canjes Recientes por Colaboradores</span>
                        </h4>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-brand-dark">
                            <thead class="bg-slate-50 text-muted uppercase text-[10px] font-bold border-b border-line">
                                <tr>
                                    <th class="py-2.5 px-3">Código</th>
                                    <th class="py-2.5 px-3">Colaborador</th>
                                    <th class="py-2.5 px-3">Beneficio</th>
                                    <th class="py-2.5 px-3 text-center">Puntos</th>
                                    <th class="py-2.5 px-3">Fecha</th>
                                    <th class="py-2.5 px-3 text-right">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-line/60">
                                @foreach($canjesRecientes as $c)
                                    <tr class="hover:bg-slate-50/50">
                                        <td class="py-2.5 px-3 font-mono font-bold text-brand-blue">{{ $c->codigo_canje }}</td>
                                        <td class="py-2.5 px-3">
                                            <span class="font-bold">{{ $c->usuario->nombre ?? 'N/D' }} {{ $c->usuario->apellido ?? '' }}</span>
                                            <span class="block text-[10px] text-muted">{{ $c->usuario->sede->nombre ?? '' }} - {{ $c->usuario->area->nombre ?? '' }}</span>
                                        </td>
                                        <td class="py-2.5 px-3 font-semibold">{{ $c->beneficio->nombre ?? 'Beneficio eliminado' }}</td>
                                        <td class="py-2.5 px-3 text-center">
                                            <span class="inline-flex items-center gap-1 font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded">
                                                <i class="fa-solid fa-star text-[9px]"></i>
                                                {{ $c->puntos_utilizados }}
                                            </span>
                                        </td>
                                        <td class="py-2.5 px-3 text-muted">{{ $c->fecha_canje ? $c->fecha_canje->format('d/m/Y H:i') : $c->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="py-2.5 px-3 text-right">
                                            <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-emerald-100 text-emerald-700">
                                                {{ $c->estado }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>

<script>
function editarBeneficio(data, updateUrl) {
    document.getElementById('form-beneficio-title').textContent = 'Editar Beneficio';
    document.getElementById('form-beneficio').action = updateUrl;
    document.getElementById('form-beneficio-method').value = 'PUT';

    document.getElementById('beneficio-nombre').value = data.nombre || '';
    document.getElementById('beneficio-descripcion').value = data.descripcion || '';
    document.getElementById('beneficio-puntos').value = data.puntos_requeridos || 50;
    document.getElementById('beneficio-icono').value = data.icono || 'fa-gift';
    document.getElementById('beneficio-estado').checked = !!data.estado;

    document.getElementById('btn-submit-beneficio').innerHTML = '<i class="fa-solid fa-check"></i><span>Actualizar Beneficio</span>';
    document.getElementById('btn-cancel-beneficio').classList.remove('hidden');

    window.scrollTo({ top: 100, behavior: 'smooth' });
}

function resetBeneficioForm() {
    document.getElementById('form-beneficio-title').textContent = 'Registrar Nuevo Beneficio';
    document.getElementById('form-beneficio').action = "{{ route('admin.beneficios.store') }}";
    document.getElementById('form-beneficio-method').value = 'POST';

    document.getElementById('beneficio-nombre').value = '';
    document.getElementById('beneficio-descripcion').value = '';
    document.getElementById('beneficio-puntos').value = 50;
    document.getElementById('beneficio-icono').value = 'fa-gift';
    document.getElementById('beneficio-estado').checked = true;

    document.getElementById('btn-submit-beneficio').innerHTML = '<i class="fa-solid fa-floppy-disk"></i><span>Guardar Beneficio</span>';
    document.getElementById('btn-cancel-beneficio').classList.add('hidden');
}
</script>
@endsection
