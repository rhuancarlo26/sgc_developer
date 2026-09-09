<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SgcEntregaSimplificadaAnalise extends Model
{
    protected $table = 'sgc_entrega_simplificada_analises';

    protected $fillable = [
        'entrega_simplificada_id', 'versao_analise', 'status', 'observacoes', 'fiscal_id',
    ];

    public function fiscal()
    {
        return $this->belongsTo(User::class, 'fiscal_id');
    }
}
