<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModuloImportadorFotos extends Model
{
    use HasFactory;

    protected $table = 'modulo_importador_fotos';

    protected $fillable = [
        'modulo_importador_id',
        'nome_arquivo',
        'caminho_arquivo',
        'latitude',
        'longitude',
        'descricao',
        'data_captura',
        'fabricante',
        'modelo',
        'largura',
        'altura',
        'orientacao',
        'metadados',
    ];

    protected $casts = [
        'data_captura' => 'datetime',
        'metadados' => 'array',
    ];
}
