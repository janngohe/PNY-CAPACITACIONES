<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tablas del instrumento PAC-A (Prueba 1). Son tablas NUEVAS que se enlazan a
 * `evaluaciones_candidatos`; no modifican ninguna tabla existente.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paca_respuestas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluacion_candidato_id')->constrained('evaluaciones_candidatos')->cascadeOnDelete();
            $table->unsignedTinyInteger('numero_pregunta');
            $table->enum('opcion_seleccionada', ['S', 'F', 'R', 'N']);
            $table->decimal('puntaje', 3, 1);
            $table->timestamps();

            $table->unique(['evaluacion_candidato_id', 'numero_pregunta'], 'uk_paca_resp_eval_preg');
        });

        Schema::create('paca_resultados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluacion_candidato_id')->unique()->constrained('evaluaciones_candidatos')->cascadeOnDelete();
            $table->decimal('puntaje_tendencia_accidentogena', 4, 1);
            $table->decimal('puntaje_patron_tipo_a', 4, 1);
            $table->text('interpretacion_accidentogena')->nullable();
            $table->enum('estado_interpretacion_accidentogena', ['DEFINIDA', 'AMBIGUA_VALIDACION_PENDIENTE'])->default('DEFINIDA');
            $table->text('interpretacion_tipo_a')->nullable();
            $table->enum('estado_interpretacion_tipo_a', ['DEFINIDA', 'AMBIGUA_VALIDACION_PENDIENTE'])->default('DEFINIDA');
            $table->text('observaciones_profesional')->nullable();
            $table->boolean('calculado_automaticamente')->default(true);
            $table->dateTime('finalizada_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paca_resultados');
        Schema::dropIfExists('paca_respuestas');
    }
};

