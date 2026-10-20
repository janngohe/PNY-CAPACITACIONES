<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Induccion extends Model
{
    protected $table = 'inducciones';

    protected $fillable = [
        'titulo',
        'descripcion',
        'tipo',
        'archivo_presentacion',
        'video_url',
        'contenido_diapositivas',
        'estado',
        'creado_por',
    ];

    protected function casts(): array
    {
        return [
            'estado' => 'boolean',
            'contenido_diapositivas' => 'array',
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

    /**
     * Convierte una URL común de video (YouTube, Vimeo, etc.) en URL embed para iframe.
     */
    public function getEmbedVideoUrlAttribute(): ?string
    {
        if (empty($this->video_url)) {
            return null;
        }

        $url = trim($this->video_url);

        // Si ya es un embed directo
        if (str_contains($url, 'youtube.com/embed/') || str_contains($url, 'player.vimeo.com/video/')) {
            return $url;
        }

        // YouTube (formato largo o corto)
        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/', $url, $matches)) {
            return 'https://www.youtube.com/embed/' . $matches[1] . '?rel=0&modestbranding=1';
        }

        // Vimeo
        if (preg_match('/vimeo\.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|)(\d+)/', $url, $matches)) {
            return 'https://player.vimeo.com/video/' . end($matches);
        }

        return $url;
    }

    /**
     * URL pública del archivo PDF o presentación.
     */
    public function getArchivoUrlAttribute(): ?string
    {
        return $this->archivo_presentacion ? asset('storage/' . $this->archivo_presentacion) : null;
    }
}
