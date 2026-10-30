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
        Schema::create('pruebas_psicotecnicas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 30)->unique(); // 'PAC-A', 'VALANTI'
            $table->string('nombre', 150);
            $table->text('descripcion')->nullable();
            $table->string('version_instrumento', 20)->default('01');
            $table->enum('estado', ['DISPONIBLE', 'EN_DESARROLLO', 'INACTIVA'])->default('DISPONIBLE');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pruebas_psicotecnicas');
    }
};
