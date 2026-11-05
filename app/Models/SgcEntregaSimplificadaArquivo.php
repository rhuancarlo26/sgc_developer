<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SgcEntregaSimplificadaArquivo extends Model
{
    protected $table = 'sgc_entrega_simplificada_arquivos';

    protected $fillable = [
        'entrega_simplificada_id', 'tipo', 'nome_arquivo', 'caminho_arquivo',
        'mime_type', 'tamanho_bytes', 'descricao', 'latitude', 'longitude',
        'data_captura', 'metadados',
    ];

    protected $casts = [
        'data_captura' => 'datetime',
        'metadados' => 'array',
    ];
}
