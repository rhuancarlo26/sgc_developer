<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SgcQuilombolaCampanha extends Model
{
    protected $table = 'sgc_quilombola_campanhas';

    protected $fillable = [
        'id_contrato', 'cod_emp', 'id_campanha', 'sei_dnit', 'subproduto', 'status', 'versao_analise',
    ];

    protected $casts = [
        'id_contrato' => 'integer',
        'id_campanha' => 'integer',
        'versao_analise' => 'integer',
    ];

    public function contrato()
    {
        return $this->belongsTo(Contrato::class, 'id_contrato');
    }
}

