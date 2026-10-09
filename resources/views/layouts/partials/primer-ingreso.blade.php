    <!-- ========================================================
         MODAL OBLIGATORIO DE PRIMER INGRESO (CAMBIO DE CONTRASEÑA)
         Activado automáticamente si usuario_nuevo === true
         ======================================================== -->
    @if(isset($usuario) && ($usuario->usuario_nuevo ?? false))
    <div id="modal-primer-ingreso" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-brand-dark/75 backdrop-blur-md">
        
        <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl border-2 border-brand-sky/40 relative overflow-hidden animate-in fade-in zoom-in-95 duration-200">
            
            <!-- Olas decorativas en la parte superior del modal -->
            <div class="absolute top-0 left-0 w-full bg-gradient-to-r from-brand-dark via-brand-deep to-brand-blue h-20 -z-0">
                <svg class="absolute bottom-0 left-0 w-full h-4 text-white" viewBox="0 0 400 20" preserveAspectRatio="none">
                    <path d="M0,5 C70,18 140,4 210,14 C280,22 350,6 400,15 L400,20 L0,20 Z" fill="currentColor"/>
                </svg>
            </div>

            <!-- Contenido del modal -->
            <div class="relative z-10 pt-2">
                
                <!-- Icono de Seguridad / Candado -->
                <div class="w-14 h-14 mx-auto rounded-2xl bg-white border-2 border-brand-blue/20 text-brand-blue flex items-center justify-center shadow-md mb-3.5">
                    <svg class="w-7 h-7 text-brand-blue" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                    </svg>
                </div>

                <div class="text-center mb-5">
                    <span class="inline-block px-3 py-0.5 rounded-full bg-brand-light text-brand-blue font-bold text-[10px] uppercase tracking-wider mb-1">
                        Primer Ingreso al Sistema
                    </span>
                    <h3 class="font-heading font-extrabold text-lg sm:text-xl text-brand-dark">
                        Crea tu Nueva Contraseña
                    </h3>
                    <p class="text-xs text-muted mt-1 leading-relaxed">
                        Hola <strong class="text-brand-dark">{{ $usuario->nombre_completo ?? 'Colaborador' }}</strong>, para garantizar la seguridad de tu cuenta debes reemplazar tu contraseña inicial (tu cédula) por una contraseña personal y confidencial.
                    </p>
                </div>

                <!-- Formulario de cambio de contraseña -->
                <form id="form-primer-ingreso" method="POST" action="{{ route('password.primer_ingreso') }}" class="space-y-4">
                    @csrf

                    <div id="error-primer-ingreso" class="hidden p-3 rounded-xl bg-red-50 border border-red-200 text-xs text-red-700"></div>

                    <!-- Nueva Contraseña -->
                    <div class="space-y-1.5">
                        <label for="new_password" class="block text-xs font-bold text-brand-dark">
                            Nueva Contraseña Personal
                        </label>
                        <div class="relative">
                            <input type="password" 
                                   id="new_password" 
                                   name="new_password" 
                                   required 
                                   minlength="6"
                                   placeholder="Mínimo 6 caracteres"
                                   class="w-full px-4 py-2.5 rounded-xl border border-line text-xs font-sans text-brand-dark focus:outline-none focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20">
                        </div>
                    </div>

                    <!-- Confirmar Contraseña -->
                    <div class="space-y-1.5">
                        <label for="new_password_confirmation" class="block text-xs font-bold text-brand-dark">
                            Confirmar Nueva Contraseña
                        </label>
                        <div class="relative">
                            <input type="password" 
                                   id="new_password_confirmation" 
                                   name="new_password_confirmation" 
                                   required 
                                   minlength="6"
                                   placeholder="Repite tu nueva contraseña"
                                   class="w-full px-4 py-2.5 rounded-xl border border-line text-xs font-sans text-brand-dark focus:outline-none focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20">
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit" 
                                id="btn-submit-primer-ingreso"
                                class="w-full py-3 px-4 rounded-xl bg-brand-blue hover:bg-brand-deep text-white font-heading font-bold text-xs sm:text-sm tracking-wide shadow-xs transition-colors flex items-center justify-center gap-2 cursor-pointer">
                            <span>Guardar Contraseña y Continuar</span>
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                            </svg>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- Script de validación interactiva para primer ingreso -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('form-primer-ingreso');
            const errorDiv = document.getElementById('error-primer-ingreso');
            const submitBtn = document.getElementById('btn-submit-primer-ingreso');

            if (form) {
                form.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    errorDiv.classList.add('hidden');
                    errorDiv.innerHTML = '';

                    const pass1 = document.getElementById('new_password').value;
                    const pass2 = document.getElementById('new_password_confirmation').value;

                    if (pass1.length < 6) {
                        errorDiv.textContent = 'La nueva contraseña debe tener al menos 6 caracteres.';
                        errorDiv.classList.remove('hidden');
                        return;
                    }

                    if (pass1 !== pass2) {
                        errorDiv.textContent = 'Las contraseñas no coinciden. Por favor verifícalas.';
                        errorDiv.classList.remove('hidden');
                        return;
                    }

                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-75');
                    submitBtn.innerHTML = '<span>Actualizando contraseña...</span>';

                    try {
                        const formData = new FormData(form);
                        const response = await fetch(form.action, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: formData
                        });

                        const data = await response.json();

                        if (response.ok && data.success) {
                            const modal = document.getElementById('modal-primer-ingreso');
                            if (modal) modal.remove();
                            alert(data.message || 'Contraseña actualizada con éxito.');
                            window.location.reload();
                        } else {
                            submitBtn.disabled = false;
                            submitBtn.classList.remove('opacity-75');
                            submitBtn.innerHTML = '<span>Guardar Contraseña y Continuar</span>';
                            
                            let msg = data.message || 'Error al actualizar contraseña.';
                            if (data.errors) {
                                msg = Object.values(data.errors).flat().join('<br>');
                            }
                            errorDiv.innerHTML = msg;
                            errorDiv.classList.remove('hidden');
                        }
                    } catch (err) {
                        submitBtn.disabled = false;
                        submitBtn.classList.remove('opacity-75');
                        submitBtn.innerHTML = '<span>Guardar Contraseña y Continuar</span>';
                        errorDiv.textContent = 'Ocurrió un error en la conexión. Intenta de nuevo.';
                        errorDiv.classList.remove('hidden');
                    }
                });
            }
        });
    </script>
    @endif
