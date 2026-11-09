<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SgcFaunaCavernicola extends Model
{
    protected $table = 'sgc_fauna_amostragem_cavernicola';
    protected $guarded = ['id'];
    public $timestamps = true;

    protected $fillable = [
        'id_contrato',
        'id_campanha',
        'cavidade',
        'latitude',
        'longitude',
        'distancia_eixo_rodovia',
        'formacao_associada',
        'temperatura_media_interna',
        'temperatura_media_externa',
        'umidade_relativa_interna',
        'umidade_relativa_externa',
    ];

    public function campanha()
    {
        return $this->belongsTo(SgcFaunaCampanha::class, 'id_campanha', 'id');
    }
}