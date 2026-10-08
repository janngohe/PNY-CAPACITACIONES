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
        Schema::create('area_capacitacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('area_id')->constrained('areas')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('capacitacion_id')->constrained('capacitaciones')->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();

            $table->unique(['area_id', 'capacitacion_id'], 'uk_area_capacitacion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('area_capacitacion');
    }
};
