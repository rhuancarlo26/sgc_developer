<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SgcFaunaAnaliseEtapa extends Model
{
    protected $table = 'sgc_fauna_analise_etapas';

    protected $fillable = [
        'id_contrato',
        'id_campanha',
        'etapa',
        'analise',
        'status',
        'comentario',
        'fiscal_id',
    ];

    public function campanha()
    {
        return $this->belongsTo(SgcFaunaCampanha::class, 'id_campanha');
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