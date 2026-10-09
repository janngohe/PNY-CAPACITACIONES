<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $areaSistemas = Area::firstOrCreate(
            ['nombre' => 'Sistemas'],
            [
                'descripcion' => 'Área de Sistemas e Informática',
                'estado' => true,
            ]
        );

        // Empleado: Emerson Triviño Trujillo
        Usuario::updateOrCreate(
            ['identificacion' => '1007342111'],
            [
                'nombre_completo' => 'Emerson Triviño Trujillo',
                'password' => Hash::make('1007342111'),
                'rol' => 'EMPLEADO',
                'area_id' => $areaSistemas->id,
                'usuario_nuevo' => true,
                'estado' => true,
            ]
        );

        // Jefe de Área: Felipe Sanchez
        Usuario::updateOrCreate(
            ['identificacion' => '123456'],
            [
                'nombre_completo' => 'Felipe Sanchez',
                'password' => Hash::make('123456'),
                'rol' => 'JEFE_AREA',
                'area_id' => $areaSistemas->id,
                'usuario_nuevo' => true,
                'estado' => true,
            ]
        );

        // Administrador: Administrador General PNY
        Usuario::updateOrCreate(
            ['identificacion' => 'admin'],
            [
                'nombre_completo' => 'Administrador General',
                'password' => Hash::make('admin123'),
                'rol' => 'ADMINISTRADOR',
                'area_id' => $areaSistemas->id,
                'usuario_nuevo' => false,
                'estado' => true,
            ]
        );
    }
}
