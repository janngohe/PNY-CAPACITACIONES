<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    protected $table = 'configuraciones';

    protected $fillable = [
        'clave',
        'valor',
        'descripcion',
    ];

    /**
     * Obtiene el valor de una configuración por su clave.
     */
    public static function obtener(string $clave, ?string $default = null): ?string
    {
        $registro = static::where('clave', $clave)->first();
        return $registro ? $registro->valor : $default;
    }

    /**
     * Establece o actualiza una configuración del sistema.
     */
    public static function establecer(string $clave, ?string $valor, ?string $descripcion = null): static
    {
        return static::updateOrCreate(
            ['clave' => $clave],
            array_filter([
                'valor' => $valor,
                'descripcion' => $descripcion,
            ], fn($v) => $v !== null)
        );
    }

    /**
     * Obtiene el PIN actual de 4 dígitos para inducciones.
     */
    public static function getPinInduccion(): string
    {
        $pin = static::obtener('pin_induccion');
        if (!$pin) {
            $pin = '2026';
            static::establecer('pin_induccion', $pin, 'PIN de 4 dígitos para acceso al módulo de inducción');
        }
        return (string) $pin;
    }

    /**
     * Regenera un nuevo PIN criptográfico aleatorio de 4 dígitos (0000 - 9999).
     */
    public static function regenerarPinInduccion(): string
    {
        $nuevoPin = str_pad((string) random_int(1000, 9999), 4, '0', STR_PAD_LEFT);
        static::establecer('pin_induccion', $nuevoPin, 'PIN de 4 dígitos para acceso al módulo de inducción');
        return $nuevoPin;
    }
}
