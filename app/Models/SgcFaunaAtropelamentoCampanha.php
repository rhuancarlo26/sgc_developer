<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SgcFaunaAtropelamentoCampanha extends Model
{
    protected $table = 'sgc_fauna_atropelamento_campanhas';

    protected $fillable = [
        'campanha_id',
        'id_contrato',
        'rodovia',
        'data_inicial',
        'data_final',
        'uf_inicial',
        'uf_final',
        'km_inicial',
        'km_final',
        'latitude_inicial',
        'longitude_inicial',
        'latitude_final',
        'longitude_final',
        'obs',
    ];

    public function campanha()
    {
        return $this->belongsTo(SgcFaunaCampanha::class, 'campanha_id');
    }
}
