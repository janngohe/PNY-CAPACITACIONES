<?php

namespace Database\Seeders;

use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PsicologiaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Usuario con rol PSICOLOGIA para pruebas y operación
        Usuario::updateOrCreate(
            ['identificacion' => '1075250001'],
            [
                'nombre_completo' => 'Leidy Rocío Ramírez Rojas',
                'password' => Hash::make('1075250001'),
                'rol' => 'PSICOLOGIA',
                'cargo' => 'Psicóloga de Selección y Bienestar',
                'usuario_nuevo' => false,
                'estado' => true,
            ]
        );
    }
}
