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
        Schema::create('resultados_paca', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aplicacion_id')->unique()->constrained('aplicaciones_pruebas')->onDelete('cascade');
            $table->decimal('puntaje_tendencia_accidentogena', 4, 1);
            $table->decimal('puntaje_patron_tipo_a', 4, 1);
            $table->text('interpretacion_accidentogena')->nullable();
            $table->enum('estado_interpretacion_accidentogena', ['DEFINIDA', 'AMBIGUA_VALIDACION_PENDIENTE'])->default('DEFINIDA');
            $table->text('interpretacion_tipo_a')->nullable();
            $table->enum('estado_interpretacion_tipo_a', ['DEFINIDA', 'AMBIGUA_VALIDACION_PENDIENTE'])->default('DEFINIDA');
            $table->text('observaciones_profesional')->nullable();
            $table->boolean('calculado_automaticamente')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resultados_paca');
    }
};
