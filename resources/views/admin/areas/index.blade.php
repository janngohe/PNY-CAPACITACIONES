@extends('layouts.admin')

@section('title', 'Gestión de Áreas')
@section('page_title', 'Áreas Institucionales')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    @include('admin._banner', [
        'insignia' => 'Estructura Organizacional',
        'titulo' => 'Áreas Institucionales',
        'descripcion' => 'Organiza los departamentos y áreas de C.I. Piscícola New York. Las capacitaciones se asignan a estas áreas para definir qué colaboradores deben cursarlas.',
        'icono' => 'fa-sitemap',
    ])

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

        <!-- FORMULARIO DE CREAR / EDITAR ÁREA -->
        <div class="bg-white rounded-3xl border border-line/80 shadow-2xs p-6 space-y-4">
            <div class="border-b border-line/60 pb-3">
                <span class="text-[10px] uppercase font-bold text-brand-blue tracking-wider">Gestión</span>
                <h3 id="form-area-title" class="font-heading font-extrabold text-base text-brand-dark">Crear Nueva Área</h3>
            </div>

            <form id="form-area" method="POST" action="{{ route('admin.areas.store') }}" class="space-y-4">
                @csrf
                <div id="method-container"></div>

                <div>
                    <label for="nombre" class="block text-xs font-bold text-brand-dark mb-1">Nombre del Área <span class="text-red-500">*</span></label>
                    <input type="text" id="nombre" name="nombre" required maxlength="150"
                           placeholder="Ej. Producción y Cosecha"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-line bg-slate-50/50 text-xs text-brand-dark focus:bg-white focus:outline-none focus:border-brand-blue">
                </div>

                <div>
                    <label for="descripcion" class="block text-xs font-bold text-brand-dark mb-1">Descripción</label>
                    <textarea id="descripcion" name="descripcion" rows="3" maxlength="255"
                              placeholder="Breve detalle de las funciones o procesos de esta área..."
                              class="w-full px-3.5 py-2 rounded-xl border border-line bg-slate-50/50 text-xs text-brand-dark focus:bg-white focus:outline-none focus:border-brand-blue resize-none"></textarea>
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <button type="submit" id="btn-submit-area"
                            class="flex-1 px-4 py-2.5 rounded-xl bg-brand-blue hover:bg-brand-deep text-white font-heading font-bold text-xs shadow-xs transition-colors cursor-pointer">
                        Crear Área
                    </button>
                    <button type="button" id="btn-cancel-area" onclick="limpiarFormArea()"
                            class="hidden px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors cursor-pointer">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>

        <!-- LISTADO DE ÁREAS -->
        <div class="lg:col-span-2 space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @forelse ($areas as $area)
                    <div id="area-card-{{ $area->id }}"
                         class="bg-white rounded-3xl border border-line/80 shadow-2xs p-5 flex flex-col justify-between gap-4 hover:shadow-md transition-all {{ $area->estado ? '' : 'opacity-60 bg-slate-50/50' }}">
                        <div class="space-y-2">
                            <div class="flex items-start justify-between gap-2">
                                <h4 class="font-heading font-bold text-base text-brand-dark leading-tight">{{ $area->nombre }}</h4>
                                <span class="shrink-0 px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $area->estado ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $area->estado ? 'Activa' : 'Inactiva' }}
                                </span>
                            </div>
                            @if ($area->descripcion)
                                <p class="text-xs text-slate-600 line-clamp-2">{{ $area->descripcion }}</p>
                            @else
                                <p class="text-xs text-muted italic">Sin descripción registrada.</p>
                            @endif
                        </div>

                        <!-- Métricas del área -->
                        <div class="grid grid-cols-3 gap-2 text-center pt-3 border-t border-line/50">
                            <div class="bg-slate-50 rounded-xl p-2">
                                <span class="block font-heading font-extrabold text-sm text-brand-dark">{{ $area->empleados_count }}</span>
                                <span class="text-[9px] text-muted">Empleados</span>
                            </div>
                            <div class="bg-slate-50 rounded-xl p-2">
                                <span class="block font-heading font-extrabold text-sm text-brand-dark">{{ $area->jefes_count }}</span>
                                <span class="text-[9px] text-muted">Jefes</span>
                            </div>
                            <div class="bg-slate-50 rounded-xl p-2">
                                <span class="block font-heading font-extrabold text-sm text-brand-blue">{{ $area->capacitaciones_count }}</span>
                                <span class="text-[9px] text-muted">Cursos</span>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="flex items-center justify-between pt-2">
                            <button type="button"
                                    onclick="editarArea({{ $area->id }}, '{{ addslashes($area->nombre) }}', '{{ addslashes($area->descripcion ?? '') }}')"
                                    class="text-xs font-bold text-brand-blue hover:underline cursor-pointer">
                                <i class="fa-solid fa-pen-to-square mr-1"></i> Editar
                            </button>

                            <form method="POST" action="{{ route('admin.areas.estado', $area) }}" class="m-0"
                                  onsubmit="return confirm('¿{{ $area->estado ? 'Desactivar' : 'Activar' }} el área {{ $area->nombre }}?');">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        class="px-2.5 py-1 rounded-lg text-[11px] font-bold border transition-colors cursor-pointer {{ $area->estado ? 'border-amber-200 text-amber-700 bg-amber-50 hover:bg-amber-100' : 'border-emerald-200 text-emerald-700 bg-emerald-50 hover:bg-emerald-100' }}">
                                    {{ $area->estado ? 'Desactivar' : 'Activar' }}
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white rounded-3xl border border-line/80 p-8 text-center text-muted">
                        No hay áreas registradas todavía. Crea la primera en el formulario lateral.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script>
    function editarArea(id, nombre, descripcion) {
        document.getElementById('form-area-title').innerText = 'Editar Área: ' + nombre;
        document.getElementById('nombre').value = nombre;
        document.getElementById('descripcion').value = descripcion;
        document.getElementById('btn-submit-area').innerText = 'Actualizar Área';
        document.getElementById('btn-cancel-area').classList.remove('hidden');

        const form = document.getElementById('form-area');
        form.action = `/admin/areas/${id}`;
        document.getElementById('method-container').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        window.scrollTo({ top: form.offsetTop - 80, behavior: 'smooth' });
    }

    function limpiarFormArea() {
        document.getElementById('form-area-title').innerText = 'Crear Nueva Área';
        document.getElementById('nombre').value = '';
        document.getElementById('descripcion').value = '';
        document.getElementById('btn-submit-area').innerText = 'Crear Área';
        document.getElementById('btn-cancel-area').classList.add('hidden');

        const form = document.getElementById('form-area');
        form.action = "{{ route('admin.areas.store') }}";
        document.getElementById('method-container').innerHTML = '';
    }
</script>
@endsection
