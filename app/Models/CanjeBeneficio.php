<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CanjeBeneficio extends Model
{
    protected $table = 'canjes_beneficios';

    protected $fillable = [
        'usuario_id',
        'beneficio_id',
        'puntos_utilizados',
        'estado',
        'codigo_canje',
        'qr_data',
        'fecha_canje',
        'fecha_entrega',
        'entregado_por',
        'responsable_usuario_id',
        'responsable_cc',
        'notas',
    ];

    protected $casts = [
        'puntos_utilizados' => 'integer',
        'fecha_canje' => 'datetime',
        'fecha_entrega' => 'datetime',
    ];

    /**
     * Resuelve dinámicamente la URL base pública para escanear con celulares (prioriza Ngrok activo).
     */
    public static function resolverUrlBasePublica(): string
    {
        // 1. Detección automática de túnel Ngrok en el puerto 4040
        try {
            $ch = curl_init('http://127.0.0.1:4040/api/tunnels');
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT_MS => 300,
                CURLOPT_CONNECTTIMEOUT_MS => 200,
            ]);
            $ngrokRes = curl_exec($ch);
            curl_close($ch);
            if ($ngrokRes) {
                $ngrokData = json_decode($ngrokRes, true);
                foreach ($ngrokData['tunnels'] ?? [] as $t) {
                    if (!empty($t['public_url']) && str_starts_with($t['public_url'], 'https://')) {
                        return rtrim($t['public_url'], '/');
                    }
                }
                if (!empty($ngrokData['tunnels'][0]['public_url'])) {
                    return rtrim($ngrokData['tunnels'][0]['public_url'], '/');
                }
            }
        } catch (\Throwable $e) {}

        // 2. Si la petición actual viene por Ngrok
        if (request()) {
            $host = request()->getHost();
            if (str_contains($host, 'ngrok')) {
                return rtrim(request()->getSchemeAndHttpHost(), '/');
            }
        }

        // 3. Fallback: evitar dominios .test locales si request está en localhost
        if (request() && !str_contains(request()->getHost(), '.test')) {
            return rtrim(request()->getSchemeAndHttpHost(), '/');
        }

        return rtrim(config('app.url') ?: url('/'), '/');
    }

    /**
     * Retorna la URL pública de verificación accesible desde cualquier celular.
     */
    public function getUrlVerificacionAttribute(): string
    {
        return self::resolverUrlBasePublica() . '/canje/verificar/' . $this->codigo_canje;
    }

    /**
     * Retorna el contenido exacto codificado en el código QR.
     */
    public function getQrContenidoAttribute(): string
    {
        $empleado = $this->usuario?->nombre_completo ?? 'Colaborador';
        $doc = $this->usuario?->identificacion ? 'CC ' . $this->usuario->identificacion : '—';
        $area = $this->usuario?->area?->nombre ?? ($this->usuario?->sedeActual()?->nombre ?? 'Sin área asignada');
        $beneficio = $this->beneficio?->nombre ?? 'Beneficio';
        $puntos = $this->puntos_utilizados ?? ($this->beneficio?->puntos_requeridos ?? 0);
        $fecha = $this->fecha_canje ? $this->fecha_canje->format('d/m/Y H:i') : ($this->created_at ? $this->created_at->format('d/m/Y H:i') : now()->format('d/m/Y H:i'));
        $urlVerif = $this->url_verificacion;

        $estadoTexto = $this->estado === 'CANJEADO' ? 'CANJEADO Y RECIBIDO' : 'CANJE AUTORIZADO (PENDIENTE DE ENTREGA)';

        return "C.I. PISCÍCOLA NEW YORK\n"
            . "CANJE DE BENEFICIO AUTORIZADO\n"
            . "----------------------------------------\n"
            . "Colaborador: {$empleado}\n"
            . "Identificación: {$doc}\n"
            . "Área: {$area}\n"
            . "Beneficio: {$beneficio}\n"
            . "Puntos: {$puntos} pts\n"
            . "Código QR: {$this->codigo_canje}\n"
            . "Fecha: {$fecha}\n"
            . "Estado: {$estadoTexto}\n"
            . "Verificar: {$urlVerif}";
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function beneficio(): BelongsTo
    {
        return $this->belongsTo(Beneficio::class, 'beneficio_id');
    }

    public function responsable(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'responsable_usuario_id');
    }
}
