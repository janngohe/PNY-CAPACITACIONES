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
        Schema::create('respuestas_paca', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aplicacion_id')->constrained('aplicaciones_pruebas')->onDelete('cascade');
            $table->unsignedTinyInteger('numero_pregunta');
            $table->enum('opcion_seleccionada', ['S', 'F', 'R', 'N']);
            $table->decimal('puntaje', 3, 1);
            $table->timestamps();

            $table->unique(['aplicacion_id', 'numero_pregunta']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('respuestas_paca');
    }
};
