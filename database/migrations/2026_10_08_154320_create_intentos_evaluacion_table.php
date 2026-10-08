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
        Schema::create('intentos_evaluacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('evaluacion_id')->constrained('evaluaciones')->cascadeOnDelete()->cascadeOnUpdate();
            $table->unsignedInteger('numero_intento');
            $table->decimal('porcentaje', 5, 2)->default(0.00);
            $table->unsignedInteger('respuestas_correctas')->default(0);
            $table->unsignedInteger('total_preguntas')->default(0);
            $table->enum('estado', ['APROBADO', 'NO_APROBADO']);
            $table->dateTime('fecha_inicio')->nullable();
            $table->dateTime('fecha_finalizacion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intentos_evaluacion');
    }
};
