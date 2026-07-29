<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SgcRimaAnalise extends Model
{
    protected $table = 'sgc_rima_analises';

    protected $fillable = [
        'id_contrato',
        'id_campanha',
        'versao_analise',
        'status',
        'observacoes',
        'fiscal_id',
    ];

    protected $casts = [
        'id_contrato' => 'integer',
        'id_campanha' => 'integer',
        'versao_analise' => 'integer',
        'fiscal_id' => 'integer',
    ];

    public function campanha()
    {
        return $this->belongsTo(SgcRima::class, 'id_campanha');
    }

    public function contrato()
    {
        return $this->belongsTo(Contrato::class, 'id_contrato');
    }

    public function fiscal()
    {
        return $this->belongsTo(User::class, 'fiscal_id');
    }
}
