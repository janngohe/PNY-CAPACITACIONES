<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('canjes_beneficios', function (Blueprint $table) {
            if (!Schema::hasColumn('canjes_beneficios', 'qr_data')) {
                $table->text('qr_data')->nullable()->after('codigo_canje');
            }
        });

        // Actualizar registros previos para reemplazar cualquier código 'CNJ-' por 'QR-' y poblar su contenido QR
        $canjes = DB::table('canjes_beneficios')
            ->join('usuarios', 'usuarios.id', '=', 'canjes_beneficios.usuario_id')
            ->join('beneficios', 'beneficios.id', '=', 'canjes_beneficios.beneficio_id')
            ->leftJoin('areas', 'areas.id', '=', 'usuarios.area_id')
            ->select(
                'canjes_beneficios.id',
                'canjes_beneficios.codigo_canje',
                'canjes_beneficios.fecha_canje',
                'usuarios.nombre_completo',
                'areas.nombre as area_nombre',
                'beneficios.nombre as beneficio_nombre'
            )
            ->get();

        foreach ($canjes as $canje) {
            $codigo = $canje->codigo_canje;
            if (str_starts_with($codigo, 'CNJ-')) {
                $codigo = 'QR-' . strtoupper(Str::random(10));
            }

            $area = $canje->area_nombre ?: 'Sin área asignada';
            $fecha = $canje->fecha_canje ? date('d/m/Y H:i', strtotime($canje->fecha_canje)) : date('d/m/Y H:i');

            $qrData = "C.I. PISCÍCOLA NEW YORK\n"
                . "CANJE DE BENEFICIO\n"
                . "-----------------------------\n"
                . "Colaborador: {$canje->nombre_completo}\n"
                . "Área: {$area}\n"
                . "Beneficio: {$canje->beneficio_nombre}\n"
                . "Código QR: {$codigo}\n"
                . "Fecha: {$fecha}";

            DB::table('canjes_beneficios')->where('id', $canje->id)->update([
                'codigo_canje' => $codigo,
                'qr_data' => $qrData,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('canjes_beneficios', function (Blueprint $table) {
            if (Schema::hasColumn('canjes_beneficios', 'qr_data')) {
                $table->dropColumn('qr_data');
            }
        });
    }
};
