<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SgcvwEmpreendimentos extends Model
{
    use HasFactory;
    protected $fillable = [
        'cod_emp',
        'br_uf',
        'br',
        'uf',
        'km_ini',
        'km_fin',
        'subtrecho_ini',
        'subtrecho_fin',
        'br2',
        'uf3',
        'km_ini2',
        'km_fin2',
        'subtrecho_ini2',
        'subtrecho_fin3',
        'br3',
        'uf4',
        'km_ini3',
        'km_fin3',
        'subtrecho_ini3',
        'subtrecho_fin32',
        'extensao',
        'tipo_de_intervencao',
        'descricao',
        'contrato_est_ambiental',
        'origem',
        'origem_sei',
        'origem_data',
        'bioma',
        'processo_evtea',
        'processo_projeto',
        'contrato_projeto',
        'forum_2024_cgdesp',
        'forum_2024_cgmab',
        'processo_licenciamento_dnit',
        'situacao_processo_licenciamento_dnit',
        'competencia',
        'fase_do_licenciamento',
        'fca_sei',
        'fca_data',
        'tre_sei_dnit',
        'tre_data',
        'plano_de_trabalho_entregue', // AP
        'plano_de_trabalho_aprovado', // AQ
        'ose_sei',
        'ose_data',
        'processo_ibama_oema',
        'situacao_ibama_oema',
        'criticidade_ibama_oema',
        'processo_funai',
        'situacao_funai',
        'criticidade_funai',
        'processo_iphan', //BA
        'situacao_iphan',
        'criticidade_iphan',
        'processo_incra',
        'situacao_incra',
        'criticidade_incra',
        'processo_icmbio',
        'situacao_icmbio',
        'criticidade_icmbio',
        'processo_ms',
        'situacao_ms',
        'criticidade_ms',
        'lp_avanco',
        'lp_sei',
        'lp_data',
        'li_avanco',
        'li_sei',
        'li_data',
        'estudos'
    ];
    public function estudos()
    {
        return SgcvwEstudos::where('cod_emp', $this->cod_emp)->get();
    }
    public function getContEstudosAttribute()
    {
        return $this->estudos()->count();
    }
}
