<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MapLayer extends Model
{
    protected $table = 'map_layers';

    protected $fillable = [
        'user_id',

        // Identificadores técnicos
        'workspace',
        'datastore',
        'layer_name',

        // Campos amigáveis
        'title',
        'description',

        // Info geográfica
        'geometry_type',
        'crs',
        'bbox',

        // Arquivos
        'storage_path',
        'published_at',

        'visible',

        // Mapa temático
        'thematic_field',
        'thematic_style',
    ];

    protected $casts = [
        'bbox' => 'array',
        'visible' => 'boolean',
        'thematic_style' => 'array',
    ];

    /**
     * Relação com usuário (se aplicável)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Retorna o nome fully-qualified da layer no GeoServer
     * Ex: workspace:layer_name
     */
    public function qualifiedName(): string
    {
        return "{$this->workspace}:{$this->layer_name}";
    }

    /**
     * URL base do WMS dessa layer
     */
    public function wmsUrl(): string
    {
        return config('services.geoserver.url') . "/{$this->workspace}/wms";
    }

    /**
     * Payload mínimo para o Leaflet
     */
    public function toLeaflet(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'layer' => $this->qualifiedName(),
            'wms_url' => $this->wmsUrl(),
            'visible' => $this->visible
        ];
    }

    /**
     * Scope: somente layers visíveis
     */
    public function scopeVisible($query)
    {
        return $query->where('visible', true);
    }
}
