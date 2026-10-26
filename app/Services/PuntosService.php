<?php

namespace App\Services;

use App\Models\Beneficio;
use App\Models\CanjeBeneficio;
use App\Models\Capacitacion;
use App\Models\MovimientoPuntos;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PuntosService
{
    /**
     * Acredita automáticamente los puntos de una capacitación completada al usuario.
     * Es idempotente: solo otorga puntos una única vez por capacitación completada.
     */
    public static function acreditarPuntosPorCapacitacion(Usuario $usuario, Capacitacion $capacitacion): ?MovimientoPuntos
    {
        $puntos = (int) ($capacitacion->puntos ?? 0);
        if ($puntos <= 0) {
            return null;
        }

        return DB::transaction(function () use ($usuario, $capacitacion, $puntos) {
            // Verificar si ya se le acreditaron puntos por esta misma capacitación
            $yaAcreditado = MovimientoPuntos::where('usuario_id', $usuario->id)
                ->where('capacitacion_id', $capacitacion->id)
                ->where('tipo', 'ACREDITACION')
                ->lockForUpdate()
                ->exists();

            if ($yaAcreditado) {
                return null;
            }

            // Bloquear el registro del usuario para actualizar el saldo de forma concurrente y segura
            $userBloqueado = Usuario::where('id', $usuario->id)->lockForUpdate()->first();
            if (!$userBloqueado) {
                return null;
            }

            $saldoAnterior = (int) ($userBloqueado->puntos_saldo ?? 0);
            $saldoNuevo = $saldoAnterior + $puntos;

            $userBloqueado->puntos_saldo = $saldoNuevo;
            $userBloqueado->save();

            // Sincronizar el modelo en memoria del usuario si coincide
            $usuario->puntos_saldo = $saldoNuevo;

            return MovimientoPuntos::create([
                'usuario_id' => $userBloqueado->id,
                'capacitacion_id' => $capacitacion->id,
                'beneficio_id' => null,
                'tipo' => 'ACREDITACION',
                'puntos' => $puntos,
                'saldo_anterior' => $saldoAnterior,
                'saldo_nuevo' => $saldoNuevo,
                'motivo' => 'Finalización satisfactoria: ' . $capacitacion->titulo,
            ]);
        });
    }

    /**
     * Procesa el canje de un beneficio por parte de un empleado.
     * Valida saldo suficiente, descuenta puntos de forma segura y registra el canje.
     */
    public static function canjearBeneficio(Usuario $usuario, Beneficio $beneficio): CanjeBeneficio
    {
        if (!$beneficio->estado) {
            throw ValidationException::withMessages([
                'beneficio' => 'El beneficio seleccionado no se encuentra activo actualmente.',
            ]);
        }

        return DB::transaction(function () use ($usuario, $beneficio) {
            // Bloqueo del usuario para evitar saldos negativos por peticiones concurrentes
            $userBloqueado = Usuario::where('id', $usuario->id)->lockForUpdate()->firstOrFail();

            $puntosRequeridos = (int) $beneficio->puntos_requeridos;
            $saldoActual = (int) ($userBloqueado->puntos_saldo ?? 0);

            if ($saldoActual < $puntosRequeridos) {
                throw ValidationException::withMessages([
                    'puntos' => "No cuentas con suficientes puntos para canjear este beneficio. Tienes {$saldoActual} puntos y requieres {$puntosRequeridos}.",
                ]);
            }

            // Verificar si este beneficio ya fue canjeado previamente si no se permite canje múltiple
            $yaCanjeado = CanjeBeneficio::where('usuario_id', $userBloqueado->id)
                ->where('beneficio_id', $beneficio->id)
                ->exists();

            if ($yaCanjeado) {
                throw ValidationException::withMessages([
                    'beneficio' => 'Ya has canjeado este beneficio anteriormente.',
                ]);
            }

            $saldoAnterior = $saldoActual;
            $saldoNuevo = $saldoAnterior - $puntosRequeridos;

            $userBloqueado->puntos_saldo = $saldoNuevo;
            $userBloqueado->save();
            $usuario->puntos_saldo = $saldoNuevo;

            // Generar código QR único
            $codigo = 'QR-' . strtoupper(Str::random(10));
            while (CanjeBeneficio::where('codigo_canje', $codigo)->exists()) {
                $codigo = 'QR-' . strtoupper(Str::random(10));
            }

            $userBloqueado->loadMissing(['area', 'sede']);
            $empleadoNombre = $userBloqueado->nombre_completo;
            $empleadoDoc = $userBloqueado->identificacion ? 'CC ' . $userBloqueado->identificacion : '—';
            $areaNombre = $userBloqueado->area?->nombre ?? ($userBloqueado->sedeActual()?->nombre ?? 'Sin área asignada');
            $beneficioNombre = $beneficio->nombre;
            $fechaCanje = now();
            $fechaTexto = $fechaCanje->format('d/m/Y H:i');
            $urlVerif = CanjeBeneficio::resolverUrlBasePublica() . '/canje/verificar/' . $codigo;

            // Contenido completo del código QR legible por cualquier cámara de smartphone
            $qrData = "C.I. PISCÍCOLA NEW YORK\n"
                . "CANJE DE BENEFICIO AUTORIZADO\n"
                . "----------------------------------------\n"
                . "Colaborador: {$empleadoNombre}\n"
                . "Identificación: {$empleadoDoc}\n"
                . "Área: {$areaNombre}\n"
                . "Beneficio: {$beneficioNombre}\n"
                . "Puntos: {$puntosRequeridos} pts\n"
                . "Código QR: {$codigo}\n"
                . "Fecha: {$fechaTexto}\n"
                . "Estado: SOLICITADO (PENDIENTE DE ENTREGA)\n"
                . "Verificar: {$urlVerif}";

            $canje = CanjeBeneficio::create([
                'usuario_id' => $userBloqueado->id,
                'beneficio_id' => $beneficio->id,
                'puntos_utilizados' => $puntosRequeridos,
                'estado' => 'SOLICITADO',
                'codigo_canje' => $codigo,
                'qr_data' => $qrData,
                'fecha_canje' => $fechaCanje,
                'notas' => 'Canje solicitado a través del portal de empleados.',
            ]);

            MovimientoPuntos::create([
                'usuario_id' => $userBloqueado->id,
                'capacitacion_id' => null,
                'beneficio_id' => $beneficio->id,
                'tipo' => 'CANJE',
                'puntos' => -$puntosRequeridos,
                'saldo_anterior' => $saldoAnterior,
                'saldo_nuevo' => $saldoNuevo,
                'motivo' => "Canje de beneficio: {$beneficio->nombre} (QR: {$codigo})",
            ]);

            return $canje;
        });
    }
}
