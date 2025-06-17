<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SgcFaunaCampanha extends Model
{
    protected $table = 'sgc_fauna_campanha';

    protected $fillable = [
        'id_contrato',
        'id_campanha',
        'modulos_amostrais',
        'data_ini',
        'data_fim',
        'periodo',
        'observacoes',
        'num_abio',
        'profissional',
        'grupo_profissional',
        'cod_emp',
        'subproduto',
    ];

    public function contrato()
    {
        return $this->belongsTo(Contrato::class, 'id_contrato');
    }
}