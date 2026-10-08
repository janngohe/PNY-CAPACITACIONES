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
        Schema::create('capacitaciones', function (Blueprint $table) {
            $table->id();
            $table->string('titulo', 255);
            $table->text('descripcion');
            $table->string('ruta_imagen', 255)->nullable();
            $table->decimal('porcentaje_aprobacion', 5, 2)->default(80.00);
            $table->unsignedInteger('intentos_permitidos')->default(3);
            $table->integer('duracion_estimada')->nullable();
            $table->text('incentivo')->nullable();
            $table->dateTime('fecha_disponibilidad')->nullable();
            $table->dateTime('fecha_limite')->nullable();
            $table->unsignedBigInteger('plantilla_certificado_id')->nullable();
            $table->foreign('plantilla_certificado_id')->references('id')->on('plantillas_certificado')->onDelete('set null')->onUpdate('cascade');
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('capacitaciones');
    }
};
