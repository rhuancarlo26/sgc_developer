<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SgcRelatorioCoordenacao extends Model
{
    use HasFactory;

    protected $table = 'sgc_relatorio_coordenacao';
    protected $guarded = ['id', 'created_at'];

    public function statusRelatorio()
    {
        return $this->belongsTo(SgcStatusRelatorio::class, 'status', 'id_status');
    }

    public function historicos()
    {
        return $this->hasMany(SgcHistoricoRelatorio::class, 'relatorio_num', 'relatorio_num');
    }

}

