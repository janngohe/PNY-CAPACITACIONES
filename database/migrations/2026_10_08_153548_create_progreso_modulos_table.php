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
        Schema::create('progreso_modulos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('modulo_id')->constrained('modulos')->cascadeOnDelete()->cascadeOnUpdate();
            $table->decimal('porcentaje', 5, 2)->default(0.00);
            $table->boolean('completado')->default(false);
            $table->dateTime('fecha_inicio')->nullable();
            $table->dateTime('fecha_finalizacion')->nullable();
            $table->timestamps();

            $table->unique(['usuario_id', 'modulo_id'], 'uk_progreso_usuario_modulo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('progreso_modulos');
    }
};
