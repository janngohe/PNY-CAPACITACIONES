<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('usuario_capacitacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('capacitacion_id')->constrained('capacitaciones')->cascadeOnDelete()->cascadeOnUpdate();
            $table->dateTime('fecha_asignacion')->useCurrent();
            $table->dateTime('fecha_inicio')->nullable();
            $table->dateTime('fecha_finalizacion')->nullable();
            $table->enum('estado', ['PENDIENTE', 'EN_PROGRESO', 'MODULOS_COMPLETOS', 'COMPLETADA', 'NO_APROBADA'])->default('PENDIENTE');

            $table->unique(['usuario_id', 'capacitacion_id'], 'uk_usuario_capacitacion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuario_capacitacion');
    }
};
