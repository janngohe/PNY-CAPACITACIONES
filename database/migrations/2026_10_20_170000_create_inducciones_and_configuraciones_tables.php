<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tabla de configuraciones del sistema (para PIN y parámetros generales)
        if (!Schema::hasTable('configuraciones')) {
            Schema::create('configuraciones', function (Blueprint $table) {
                $table->id();
                $table->string('clave', 60)->unique();
                $table->text('valor')->nullable();
                $table->string('descripcion', 255)->nullable();
                $table->timestamps();
            });

            // Sembrar el PIN inicial de 4 dígitos para el módulo de inducción
            DB::table('configuraciones')->insert([
                'clave' => 'pin_induccion',
                'valor' => '2026',
                'descripcion' => 'Código PIN de 4 dígitos para acceso de facilitadores al módulo de inducción presencial',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 2. Tabla de Inducciones
        if (!Schema::hasTable('inducciones')) {
            Schema::create('inducciones', function (Blueprint $table) {
                $table->id();
                $table->string('titulo', 255);
                $table->text('descripcion')->nullable();
                $table->string('tipo', 50)->default('GENERAL'); // GENERAL, SST, AMBIENTAL, PLANTA, VISITANTES
                $table->string('archivo_presentacion', 255)->nullable(); // PDF de diapositivas
                $table->string('video_url', 500)->nullable(); // URL de video (YouTube, Vimeo, etc. para iframe)
                $table->json('contenido_diapositivas')->nullable(); // Diapositivas interactivas en JSON
                $table->boolean('estado')->default(true);
                $table->integer('creado_por')->nullable();
                $table->timestamps();

                $table->foreign('creado_por')->references('id')->on('usuarios')->nullOnDelete();
            });
        }

        // 3. Relación Inducción - Sedes autorizadas
        if (!Schema::hasTable('induccion_sedes')) {
            Schema::create('induccion_sedes', function (Blueprint $table) {
                $table->foreignId('induccion_id')->constrained('inducciones')->cascadeOnDelete();
                $table->foreignId('sede_id')->constrained('sedes')->cascadeOnDelete();
                $table->primary(['induccion_id', 'sede_id']);
            });
        }

        // 4. Relación Inducción - Áreas autorizadas
        if (!Schema::hasTable('induccion_areas')) {
            Schema::create('induccion_areas', function (Blueprint $table) {
                $table->foreignId('induccion_id')->constrained('inducciones')->cascadeOnDelete();
                $table->integer('area_id');
                $table->primary(['induccion_id', 'area_id']);

                $table->foreign('area_id')->references('id')->on('areas')->cascadeOnDelete();
            });
        }

        // 5. Sesiones en vivo de Inducción
        if (!Schema::hasTable('sesiones_induccion')) {
            Schema::create('sesiones_induccion', function (Blueprint $table) {
                $table->id();
                $table->foreignId('induccion_id')->constrained('inducciones')->cascadeOnDelete();
                $table->integer('facilitador_id');
                $table->foreignId('sede_id')->constrained('sedes')->cascadeOnDelete();
                $table->string('codigo_sesion', 30)->unique(); // Ej: IND-4921
                $table->enum('estado', ['EN_CURSO', 'FINALIZADA'])->default('EN_CURSO');
                $table->dateTime('fecha_inicio');
                $table->dateTime('fecha_cierre')->nullable();
                $table->json('areas_convocadas')->nullable();
                $table->timestamps();

                $table->foreign('facilitador_id')->references('id')->on('usuarios')->cascadeOnDelete();
                $table->index(['codigo_sesion', 'estado']);
            });
        }

        // 6. Registro de Asistencias y Firmas
        if (!Schema::hasTable('asistencias_induccion')) {
            Schema::create('asistencias_induccion', function (Blueprint $table) {
                $table->id();
                $table->foreignId('sesion_id')->constrained('sesiones_induccion')->cascadeOnDelete();
                $table->integer('usuario_id')->nullable();
                $table->string('identificacion', 30);
                $table->string('nombre_completo', 150);
                $table->string('cargo', 150)->nullable();
                $table->string('area', 150)->nullable();
                $table->string('sede', 150)->nullable();
                $table->boolean('es_externo')->default(false);
                $table->string('empresa_externa', 150)->nullable();
                $table->string('telefono', 30)->nullable();
                $table->string('firma_digital', 255); // Ruta al archivo de la firma capturada
                $table->dateTime('fecha_registro');
                $table->string('ip_origen', 45)->nullable();
                $table->timestamps();

                $table->foreign('usuario_id')->references('id')->on('usuarios')->nullOnDelete();
                $table->unique(['sesion_id', 'identificacion'], 'uk_asistencia_sesion_identificacion');
                $table->index(['sesion_id', 'fecha_registro']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asistencias_induccion');
        Schema::dropIfExists('sesiones_induccion');
        Schema::dropIfExists('induccion_areas');
        Schema::dropIfExists('induccion_sedes');
        Schema::dropIfExists('inducciones');
        Schema::dropIfExists('configuraciones');
    }
};
