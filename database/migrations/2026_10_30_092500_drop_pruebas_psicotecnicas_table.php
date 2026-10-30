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
        // Eliminar restricción de clave foránea en aplicaciones_pruebas
        Schema::table('aplicaciones_pruebas', function (Blueprint $table) {
            $table->dropForeign(['prueba_codigo']);
        });

        // Eliminar la tabla catálogo de pruebas psicotécnicas
        Schema::dropIfExists('pruebas_psicotecnicas');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('pruebas_psicotecnicas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 30)->unique();
            $table->string('nombre', 150);
            $table->text('descripcion')->nullable();
            $table->string('version_instrumento', 20)->default('01');
            $table->enum('estado', ['DISPONIBLE', 'EN_DESARROLLO', 'INACTIVA'])->default('DISPONIBLE');
            $table->timestamps();
        });

        Schema::table('aplicaciones_pruebas', function (Blueprint $table) {
            $table->foreign('prueba_codigo')->references('codigo')->on('pruebas_psicotecnicas')->onUpdate('cascade');
        });
    }
};
