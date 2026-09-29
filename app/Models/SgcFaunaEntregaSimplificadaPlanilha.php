<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SgcFaunaEntregaSimplificadaPlanilha extends Model
{
    protected $table = 'sgc_fauna_entrega_simplificada_planilhas';

    protected $fillable = [
        'entrega_simplificada_id', 'tipo', 'modulo_id', 'nome_arquivo',
        'caminho_arquivo', 'mime_type', 'tamanho_bytes',
    ];

    protected $casts = [
        'entrega_simplificada_id' => 'integer',
        'modulo_id' => 'integer',
        'tamanho_bytes' => 'integer',
    ];

    public function entrega()
    {
        return $this->belongsTo(SgcEntregaSimplificada::class, 'entrega_simplificada_id');
    }

    public function modulo()
    {
        return $this->belongsTo(SgcModulo::class, 'modulo_id');
    }
}
