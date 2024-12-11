<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SgcHistoricoRelatorio extends Model
{
    use HasFactory;

    protected $table = 'sgc_historico_relatorios';
    protected $guarded = ['id', 'created_at'];

    public function relatorioCoordenacao()
    {
        return $this->belongsTo(SgcRelatorioCoordenacao::class, 'relatorio_num', 'relatorio_num');
    }
}
