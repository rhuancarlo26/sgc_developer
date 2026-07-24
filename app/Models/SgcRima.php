<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SgcRima extends Model
{
    protected $table = 'sgc_rima';

    protected $fillable = [
        'id_contrato',
        'cod_emp',
        'id_campanha',
        'sei_dnit',
        'subproduto',
        'modulo_id',
        'status',
        'planilha_nome',
        'planilha_caminho',
        'versao_analise',
        'aprovado_por',
        'data_aprovacao',
        'arquivada_em'
    ];

    protected $casts = [
        'id_contrato' => 'integer',
        'id_campanha' => 'integer',
        'modulo_id' => 'integer',
        'versao_analise' => 'integer',
        'aprovado_por' => 'integer',
        'data_aprovacao' => 'datetime',
    ];

    public function fotos()
    {
        return $this->hasMany(SgcRimaFoto::class, 'sgc_rima_id');
    }

    public function anexos()
    {
        return $this->hasMany(SgcRimaAnexo::class, 'sgc_rima_id');
    }

    public function modulo()
    {
        return $this->belongsTo(SgcModulo::class, 'modulo_id');
    }

    public function analises()
    {
        return $this->hasMany(SgcRimaAnalise::class, 'id_campanha');
    }

    public function aprovador()
    {
        return $this->belongsTo(User::class, 'aprovado_por');
    }
}
