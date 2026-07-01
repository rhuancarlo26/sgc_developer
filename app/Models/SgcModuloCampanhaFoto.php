<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SgcModuloCampanhaFoto extends Model
{
    protected $table = 'sgc_modulo_campanha_fotos';

    protected $fillable = [
        'campanha_id',
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

    public function campanha()
    {
        return $this->belongsTo(SgcModuloCampanha::class, 'campanha_id');
    }
}
