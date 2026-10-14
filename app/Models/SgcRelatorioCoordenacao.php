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

    

    public function aprovacao()
    {
        return $this->hasOne(ChangeLog::class, 'record_id')
            ->where('table_name', 'sgc_relatorio_coordenacao')
            ->where('field', 'status')
            ->where('new_value', 'Relatório Aprovado')
            ->latest('id');
    }
}

