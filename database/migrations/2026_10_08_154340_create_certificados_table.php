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
        Schema::create('certificados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('capacitacion_id')->constrained('capacitaciones')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('plantilla_certificado_id')->constrained('plantillas_certificado')->restrictOnDelete()->cascadeOnUpdate();
            $table->string('codigo', 100)->unique();
            $table->string('nombre_empleado', 150);
            $table->string('identificacion', 30);
            $table->string('nombre_capacitacion', 255);
            $table->string('area_nombre', 150)->nullable();
            $table->decimal('porcentaje', 5, 2)->default(0.00);
            $table->dateTime('fecha_emision')->useCurrent();
            $table->string('ruta_archivo', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificados');
    }
};
