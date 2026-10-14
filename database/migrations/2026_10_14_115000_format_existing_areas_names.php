<?php

use App\Models\Area;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Aplica el formato de primera letra mayúscula por palabra (Title Case)
     * a todas las áreas existentes en la base de datos.
     */
    public function up(): void
    {
        $areas = Area::all();

        foreach ($areas as $area) {
            $nombreOriginal = $area->nombre;
            $nombreFormateado = Area::formatearNombre($nombreOriginal);

            if ($nombreOriginal !== $nombreFormateado) {
                // Actualizar directamente usando el modelo
                $area->nombre = $nombreFormateado;
                $area->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No es necesario revertir la capitalización
    }
};
