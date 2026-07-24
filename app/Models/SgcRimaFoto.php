<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SgcRimaFoto extends Model
{
    protected $table = 'sgc_rima_fotos';

    protected $fillable = [
        'sgc_rima_id',
        'nome_arquivo',
        'caminho_arquivo',
        'latitude',
        'longitude',
        'data_captura',
        'descricao',
        'metadados',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'data_captura' => 'datetime',
        'metadados' => 'array',
    ];

    public function rima()
    {
        return $this->belongsTo(SgcRima::class, 'sgc_rima_id');
    }
}
