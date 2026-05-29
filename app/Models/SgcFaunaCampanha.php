<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SgcFaunaCampanha extends Model
{
    protected $table = 'sgc_fauna_campanha';

    protected $fillable = [
        'id_contrato',
        'id_campanha',
        'data_ini',
        'data_fim',
        'periodo',
        'observacoes',
        'num_abio',
        'cod_emp',
        'subproduto',
        'status',
        'versao_analise',
        'arquivada_em',
        'etapa_atual',
        'planilha_atropelamento',
        'consideracoes_atropelamento',
    ];

    protected $casts = [
        'id_campanha' => 'integer',
        'arquivada_em' => 'datetime',
    ];

    public function contrato()
    {
        return $this->belongsTo(Contrato::class, 'id_contrato');
    }

    public function abios()
    {
        return $this->hasMany(SgcFaunaCampanhaAbios::class, 'campanha_id', 'id');
    }

    public function profissionais()
    {
        return $this->hasMany(SgcFaunaCampanhaProfissional::class, 'campanha_id', 'id'  );
    }

    public function modulos_amostrais()
    {
        return $this->hasMany(SgcFaunaModuloAmostral::class, 'campanha_id', 'id');
    }
   
    public function pontos_quelo_crocod()
    {
        return $this->hasMany(SgcFaunaQuelonios::class, 'id_campanha', 'id');
    }

    public function pontos_cavernicola()
    {
        return $this->hasMany(SgcFaunaCavernicola::class, 'id_campanha', 'id');
    }

    public function metodologias()
    {
        return $this->hasMany(SgcFaunaMetodologia::class, 'campanha_id', 'id');
    }

    public function resultados()
    {
        return $this->hasMany(SgcFaunaResultados::class, 'id_campanha', 'id');
    }

    public function resultados_consideracoes()
    {
        return $this->hasOne(SgcFaunaResultadosConsideracoes::class, 'id_campanha', 'id');
    }

    public function anexos()
    {
        return $this->hasMany(SgcFaunaAnexo::class, 'id_campanha', 'id');
    }

    public function analises()
    {
        return $this->hasMany(SgcFaunaAnaliseEtapa::class, 'id_campanha'); 
    }

    public function resultadosTerrestre()
    {
        return $this->hasMany(SgcFaunaResultadosTerrestre::class, 'id_campanha', 'id');
    }

    public function resultadosAquatica()
    {
        return $this->hasMany(SgcFaunaResultadosAquatica::class, 'id_campanha', 'id');
    }

    public function resultadosCavernicola()
    {
        return $this->hasMany(SgcFaunaResultadosCavernicola::class, 'id_campanha', 'id');
    }

    public function atropelamento_campanhas()
    {
        return $this->hasMany(SgcFaunaAtropelamentoCampanha::class, 'campanha_id', 'id');
    }
}