<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SgcEntregaSimplificada extends Model
{
    protected $table = 'sgc_entregas_simplificadas';

    protected $fillable = [
        'id_contrato',
        'produto_tipo',
        'entidade_tipo',
        'entidade_id',
        'modelo_id',
        'planilha_nome',
        'planilha_caminho',
        'status',
        'versao_analise',
        'aprovado_por',
        'data_aprovacao',
        'arquivada_em',
    ];

    protected $casts = [
        'id_contrato' => 'integer',
        'entidade_id' => 'integer',
        'modelo_id' => 'integer',
        'versao_analise' => 'integer',
        'aprovado_por' => 'integer',
        'data_aprovacao' => 'datetime',
        'arquivada_em' => 'datetime',
    ];

    public function entidade()
    {
        return $this->morphTo(null, 'entidade_tipo', 'entidade_id');
    }

    public function arquivos()
    {
        return $this->hasMany(SgcEntregaSimplificadaArquivo::class, 'entrega_simplificada_id');
    }

    public function fotos()
    {
        return $this->arquivos()->where('tipo', 'foto');
    }

    public function anexos()
    {
        return $this->arquivos()->where('tipo', 'anexo');
    }

    public function planilhasFauna()
    {
        return $this->hasMany(SgcFaunaEntregaSimplificadaPlanilha::class, 'entrega_simplificada_id');
    }

    public function analises()
    {
        return $this->hasMany(SgcEntregaSimplificadaAnalise::class, 'entrega_simplificada_id');
    }

    public function modelo()
    {
        return $this->belongsTo(SgcModulo::class, 'modelo_id');
    }

    public function aprovador()
    {
        return $this->belongsTo(User::class, 'aprovado_por');
    }
}
