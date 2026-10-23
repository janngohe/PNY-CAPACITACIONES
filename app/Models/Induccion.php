<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Induccion extends Model
{
    protected $table = 'inducciones';

    protected $fillable = [
        'titulo',
        'descripcion',
        'imagen_portada',
        'archivo_presentacion',
        'video_url',
        'contenido_diapositivas',
        'recursos',
        'estado',
        'creado_por',
    ];

    protected function casts(): array
    {
        return [
            'estado' => 'boolean',
            'contenido_diapositivas' => 'array',
            'recursos' => 'array',
        ];
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'creado_por');
    }

    public function sedes(): BelongsToMany
    {
        return $this->belongsToMany(Sede::class, 'induccion_sedes', 'induccion_id', 'sede_id');
    }

    public function areas(): BelongsToMany
    {
        return $this->belongsToMany(Area::class, 'induccion_areas', 'induccion_id', 'area_id');
    }

    public function sesiones(): HasMany
    {
        return $this->hasMany(SesionInduccion::class, 'induccion_id');
    }

    public function asistencias(): HasManyThrough
    {
        return $this->hasManyThrough(AsistenciaInduccion::class, SesionInduccion::class, 'induccion_id', 'sesion_id');
    }

    /**
     * URL de la imagen de portada o avatar institucional por defecto.
     */
    public function getImagenPortadaUrlAttribute(): string
    {
        if (!empty($this->imagen_portada)) {
            return asset('storage/' . $this->imagen_portada);
        }

        return asset('images/Img-login.jpg');
    }

    /**
     * Retorna la lista unificada y normalizada de recursos (Videos, PPTX, PDFs, Imágenes, Texto).
     */
    public function getListaRecursosAttribute(): array
    {
        $items = [];

        // 1. Recursos estructurados en formato JSON
        if (!empty($this->recursos) && is_array($this->recursos)) {
            foreach ($this->recursos as $idx => $rec) {
                $tipo = $rec['tipo'] ?? 'video';
                $titulo = $rec['titulo'] ?? ("Recurso #" . ($idx + 1));
                $url = $rec['url'] ?? null;
                $archivo = $rec['archivo'] ?? null;
                $archivoUrl = $archivo ? asset('storage/' . $archivo) : null;
                $texto = $rec['texto'] ?? $rec['contenido'] ?? null;

                $embedUrl = null;
                if ($tipo === 'video' && $url) {
                    $embedUrl = static::convertirUrlVideoEmbed($url);
                } elseif ($tipo === 'pptx') {
                    if ($url && (str_contains($url, 'embed') || str_contains($url, 'live.com') || str_contains($url, 'google.com'))) {
                        $embedUrl = $url;
                    }
                } elseif ($tipo === 'pdf' && $archivoUrl) {
                    $embedUrl = $archivoUrl . '#toolbar=1&navpanes=0';
                } elseif ($tipo === 'imagen' && $archivoUrl) {
                    $embedUrl = $archivoUrl;
                }

                $items[] = [
                    'id' => $idx + 1,
                    'tipo' => $tipo, // 'pptx', 'video', 'pdf', 'imagen', 'texto'
                    'titulo' => $titulo,
                    'url' => $url,
                    'archivo' => $archivo,
                    'archivo_url' => $archivoUrl,
                    'embed_url' => $embedUrl,
                    'texto' => $texto,
                ];
            }
        }

        // 2. Compatibilidad hacia atrás si no tiene recursos en JSON
        if (empty($items)) {
            if (!empty($this->video_url)) {
                $items[] = [
                    'id' => count($items) + 1,
                    'tipo' => 'video',
                    'titulo' => 'Video de la Inducción',
                    'url' => $this->video_url,
                    'archivo' => null,
                    'archivo_url' => null,
                    'embed_url' => static::convertirUrlVideoEmbed($this->video_url),
                ];
            }

            if (!empty($this->archivo_presentacion)) {
                $ext = strtolower(pathinfo($this->archivo_presentacion, PATHINFO_EXTENSION));
                $esPptx = in_array($ext, ['ppt', 'pptx', 'ppsx', 'pps']);
                $tipo = $esPptx ? 'pptx' : 'pdf';
                $archivoUrl = asset('storage/' . $this->archivo_presentacion);

                $items[] = [
                    'id' => count($items) + 1,
                    'tipo' => $tipo,
                    'titulo' => $esPptx ? 'Diapositivas PowerPoint' : 'Documento de Presentación',
                    'url' => null,
                    'archivo' => $this->archivo_presentacion,
                    'archivo_url' => $archivoUrl,
                    'embed_url' => $esPptx ? null : ($archivoUrl . '#toolbar=1&navpanes=0'),
                ];
            }
        }

        return $items;
    }

    /**
     * Convierte una URL de video (YouTube, Vimeo) a formato iframe embed.
     */
    public static function convertirUrlVideoEmbed(?string $url): ?string
    {
        if (empty($url)) {
            return null;
        }

        $url = trim($url);

        if (str_contains($url, 'youtube.com/embed/') || str_contains($url, 'player.vimeo.com/video/')) {
            return $url;
        }

        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/', $url, $matches)) {
            return 'https://www.youtube.com/embed/' . $matches[1] . '?rel=0&modestbranding=1';
        }

        if (preg_match('/vimeo\.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|)(\d+)/', $url, $matches)) {
            return 'https://player.vimeo.com/video/' . end($matches);
        }

        return $url;
    }

    /**
     * Convierte la URL de un PowerPoint a un visor iFrame en línea solo si es una URL pública accesible.
     */
    public static function convertirUrlPptxEmbed(?string $url): ?string
    {
        if (empty($url)) {
            return null;
        }

        // Si es localhost o IP privada, Microsoft Office Online falla
        if (str_contains($url, 'localhost') || str_contains($url, '127.0.0.1') || str_contains($url, '192.168.') || str_contains($url, '10.')) {
            return null;
        }

        return 'https://view.officeapps.live.com/op/embed.aspx?src=' . urlencode($url);
    }

    /**
     * URL embed retrocompatible para video único.
     */
    public function getEmbedVideoUrlAttribute(): ?string
    {
        return static::convertirUrlVideoEmbed($this->video_url);
    }

    /**
     * URL pública del archivo de presentación retrocompatible.
     */
    public function getArchivoUrlAttribute(): ?string
    {
        return $this->archivo_presentacion ? asset('storage/' . $this->archivo_presentacion) : null;
    }
}
