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
        Schema::create('evaluaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('capacitacion_id')->constrained('capacitaciones')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('titulo', 255);
            $table->text('descripcion')->nullable();
            $table->decimal('porcentaje_aprobacion', 5, 2)->default(80.00);
            $table->unsignedInteger('intentos_permitidos')->default(3);
            $table->boolean('estado')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluaciones');
    }
};
