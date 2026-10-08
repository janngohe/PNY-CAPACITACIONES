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
        Schema::create('respuestas_usuario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('intento_id')->constrained('intentos_evaluacion')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('pregunta_id')->constrained('preguntas')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('opcion_id')->nullable()->constrained('opciones_respuesta')->nullOnDelete()->cascadeOnUpdate();
            $table->text('respuesta_texto')->nullable();
            $table->boolean('es_correcta')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('respuestas_usuario');
    }
};
