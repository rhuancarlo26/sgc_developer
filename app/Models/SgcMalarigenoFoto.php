<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SgcMalarigenoFoto extends Model
{
    protected $table = 'sgc_malarigeno_fotos';

    protected $fillable = [
        'sgc_malarigeno_id',
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

    public function malarigeno()
    {
        return $this->belongsTo(SgcMalarigeno::class, 'sgc_malarigeno_id');
    }
}
