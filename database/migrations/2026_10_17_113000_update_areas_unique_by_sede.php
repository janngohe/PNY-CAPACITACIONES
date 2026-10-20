<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $indexes = collect(Schema::getIndexes('areas'))->pluck('name')->all();

        Schema::table('areas', function (Blueprint $table) use ($indexes) {
            if (in_array('areas_nombre_unique', $indexes)) {
                $table->dropUnique('areas_nombre_unique');
            } elseif (in_array('uk_area_nombre', $indexes)) {
                $table->dropUnique('uk_area_nombre');
            }

            if (!in_array('areas_sede_id_nombre_unique', $indexes)) {
                $table->unique(['sede_id', 'nombre'], 'areas_sede_id_nombre_unique');
            }
        });
    }

    public function down(): void
    {
        $indexes = collect(Schema::getIndexes('areas'))->pluck('name')->all();

        Schema::table('areas', function (Blueprint $table) use ($indexes) {
            if (in_array('areas_sede_id_nombre_unique', $indexes)) {
                $table->dropUnique('areas_sede_id_nombre_unique');
            }

            if (!in_array('areas_nombre_unique', $indexes) && !in_array('uk_area_nombre', $indexes)) {
                $table->unique('nombre', 'areas_nombre_unique');
            }
        });
    }
};
