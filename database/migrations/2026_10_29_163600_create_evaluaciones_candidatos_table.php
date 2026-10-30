<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluaciones_candidatos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_sesion', 20)->index();
            $table->string('nombre_completo', 150);
            $table->string('identificacion', 30)->index();
            $table->string('lugar_expedicion', 100);
            $table->date('fecha_nacimiento');
            $table->unsignedTinyInteger('edad');
            $table->string('cargo_aspira', 120);
            $table->longText('firma_digital');
            $table->boolean('acepta_terminos')->default(true);
            $table->dateTime('fecha_hora_aceptacion');
            $table->unsignedTinyInteger('paso_actual')->default(1);
            $table->enum('estado', ['EN_PROGRESO', 'COMPLETADA', 'CANCELADA'])->default('EN_PROGRESO');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluaciones_candidatos');
    }
};

