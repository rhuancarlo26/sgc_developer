<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SgcvwEstudos extends Model
{
    use HasFactory;
    protected $fillable = [
        'contrato_id',
        'cod_emp',
        'br',
        'uf',
        'km_inicial',
        'km_final',
        'empreendimento',
        'tipo_de_intervencao',
        'competencia_do_licenciamento',
        'contrato',
        'empresa',
        'ose_emitida_sei',
        'ose_emitida_data',
        'etapa',
        'cod_siac',
        'produto',
        'item_edital', //P
        'familia',
        'subproduto',
        'qtd_ose',
        'r_ose',
        'campo',
        'relatorio',
        'situacao_dnit',
        'situacao_ext',
        'avanco',
        'req_ext',
        'apto_medicao_40',
        'medicao_40',
        'medicao_40_qtd',
        'processo_medicao_40',
        'apto_medicao_60',
        'medicao_60',
        'medicao_60_qtd',
        'processo_medicao_60',
        'data_de_inicio_previsto', // AI
        'data_de_entrega_previsto', // AJ
        'versao_00_sei',
        'versao_00_data_de_entrega',
        'versao_00_sei_nt',
        'versao_00_data_nt',
        'versao_00_sei_oficio',
        'versao_00_data_oficio',
        'versao_01_sei',
        'versao_01_data_de_entrega', // AR
        'versao_01_sei_nt',
        'versao_01_data_nt',
        'versao_01_sei_oficio',
        'versao_01_data_oficio',
        'versao_02_sei',
        'versao_02_data_de_entrega',
        'versao_02_sei_nt',
        'versao_02_data_nt',
        'versao_02_sei_oficio',
        'versao_02_data_oficio',
        'versao_03_sei',
        'versao_03_data_de_entrega',
        'versao_03_sei_nt',
        'versao_03_data_nt',
        'versao_03_sei_oficio',
        'versao_03_data_oficio',
        'versao_04_sei',
        'versao_04_data_de_entrega',
        'versao_04_sei_nt',
        'versao_04_data_nt',
        'versao_04_sei_oficio',
        'versao_04_data_oficio',
        'versao_aceita_sei', //BO
        'versao_aceita_data', //BP
        'req_ext_sei', //BQ
        'req_ext_data', //BR
        'analise_ext_01_sei',
        'analise_ext_01_data',
        'versao_ext_01_sei',
        'versao_ext_01_data',
        'analise_ext_02_sei',
        'analise_ext_02_data',
        'versao_ext_02_sei',
        'versao_ext_02_data',
        'analise_ext_03_sei',
        'analise_ext_03_data',
        'versao_ext_03_sei',
        'versao_ext_03_data',
        'analise_ext_04_sei',
        'analise_ext_04_data',
        'versao_ext_04_sei',
        'versao_ext_04_data',
        'aut_ext_sei', //CI
        'aut_ext_data', //CJ
    ];
    public function changelogs()
    {
        return $this->hasMany(ChangeLog::class, 'record_id')
            ->where('table_name', 'sgcvw_estudos')
            ->with('user')
            ->orderBy('created_at', 'desc');
    }
}
