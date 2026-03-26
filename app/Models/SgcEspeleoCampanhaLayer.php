<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SgcEspeleoCampanhaLayer extends Model
{
    protected $table = 'sgc_espeleo_campanha_layers';

    protected $fillable = [
        'campanha_id',
        'map_layer_id',
        'tipo',
    ];

    public function campanha()
    {
        return $this->belongsTo(SgcEspeleoCampanha::class, 'campanha_id');
    }

    public function layer()
    {
        return $this->belongsTo(MapLayer::class, 'map_layer_id');
    }
}
