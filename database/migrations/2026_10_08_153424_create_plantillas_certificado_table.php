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
        Schema::create('plantillas_certificado', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150);
            $table->string('descripcion', 255)->nullable();
            $table->unsignedBigInteger('area_id')->nullable();
            $table->foreign('area_id')->references('id')->on('areas')->onDelete('set null')->onUpdate('cascade');
            $table->string('ruta_plantilla', 255);
            $table->string('nombre_organizacion', 200)->nullable();
            $table->text('texto_certificado')->nullable();
            $table->string('firma_1_nombre', 150)->nullable();
            $table->string('firma_1_cargo', 150)->nullable();
            $table->string('firma_1_ruta', 255)->nullable();
            $table->string('firma_2_nombre', 150)->nullable();
            $table->string('firma_2_cargo', 150)->nullable();
            $table->string('firma_2_ruta', 255)->nullable();
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plantillas_certificado');
    }
};
