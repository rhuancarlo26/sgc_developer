<?php
namespace App\Domain\Sgc\Contratada\app\Imports;

// Funções de Importação da Planílha
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
//Models antigas:
use App\Models\Dashboard;
use App\Models\DashexcelEmpreendimentos;
//Novos Models:
use App\Models\SgcvwEmpreendimentos;
use App\Models\SgcvwEstudos;
use App\Models\SgcvwSubprodutos;

class YourImportClass implements WithMultipleSheets {
    public function sheets(): array {
        return [
            // 'Empreendimentos' => new DashboardImport,
            // 'Estudos' => new DashboardImportestudos,
            'Subprodutos' => new DashboardImportsubprodutos,
            # Adicione mais instâncias de classes de importação conforme necessário
        ];
    }
}
class DashboardImport implements ToModel, WithStartRow, WithCalculatedFormulas {
    public function model(array $row) {
        if (!in_array($row[0], [Null, 'NULL', 'null', null, NULL, "", " "])):
            return new SgcvwEmpreendimentos([
                "contrato_id" => 1,
                "cod_emp" => $row[0],
                "br_uf" => $row[1],
                "br" => $row[2],
                "uf" => $row[3],
                "km_ini" => $row[4],
                "km_fin" => $row[5],
                "subtrecho_ini" => $row[6],
                "subtrecho_fin" => $row[7],
                "br2" => $row[8],
                "uf3" => $row[9],
                "km_ini2" => $row[10],
                "km_fin2" => $row[11],
                "subtrecho_ini2" => $row[12],
                "subtrecho_fin3" => $row[13],
                "br3" => $row[14],
                "uf4" => $row[15],
                "km_ini3" => $row[16],
                "km_fin3" => $row[17],
                "subtrecho_ini3" => $row[18],
                "subtrecho_fin32" => $row[19],
                "extensao" => $row[20],
                "tipo_de_intervencao" => $row[21],
                "descricao" => $row[22],
                "contrato_est_ambiental" => $row[23],
                "origem" => $row[24],
                "origem_sei" => $row[25],
                "origem_data" => $row[26],
                "bioma" => $row[27],
                "processo_evtea" => $row[28],
                "processo_projeto" => $row[29],
                "contrato_projeto" => $row[30],
                "forum_2024_cgdesp" => $row[31],
                "forum_2024_cgmab" => $row[32],
                "processo_licenciamento_dnit" => $row[33],
                "situacao_processo_licenciamento_dnit" => $row[34],
                "competencia" => $row[35],
                "fase_do_licenciamento" => $row[36],
                "fca_sei" => $row[37],
                "fca_data" => $row[38],
                "tre_sei_dnit" => $row[39],
                "tre_data" => $row[40],
                "plano_de_trabalho_entregue" => $row[41],
                "plano_de_trabalho_aprovado" => $row[42],
                "ose_sei" => $row[43],
                "ose_data" => $row[44],
                "processo_ibama_oema" => $row[45],
                "situacao_ibama_oema" => $row[46],
                "criticidade_ibama_oema" => $row[47],
                "processo_funai" => $row[48],
                "situacao_funai" => $row[49],
                "criticidade_funai" => $row[50],
                "processo_iphan" => $row[51],
                "situacao_iphan" => $row[52],
                "criticidade_iphan" => $row[53],
                "processo_incra" => $row[54],
                "situacao_incra" => $row[55],
                "criticidade_incra" => $row[56],
                "processo_icmbio" => $row[57],
                "situacao_icmbio" => $row[58],
                "criticidade_icmbio" => $row[59],
                "processo_ms" => $row[60],
                "situacao_ms" => $row[61],
                "criticidade_ms" => $row[62],
                "lp_avanco" => $row[63],
                "lp_sei" => $row[64],
                "lp_data" => $row[65],
                "li_avanco" => $row[66],
                "li_sei" => $row[67],
                "li_data" => $row[68],
            ]);
        endif;
    }
    public function startRow(): int {
        return 2;
    }
}
class DashboardImportestudos implements ToModel, WithStartRow, WithCalculatedFormulas {
    public function model(array $row) {
        if (!in_array($row[0], [Null, 'NULL', 'null', null, NULL, "", " "])):
            return new SgcvwEstudos([
                "contrato_id" => 1,
                "cod_emp" => $row[88],
                "br" => $row[1],
                "uf" => $row[2],
                "km_inicial" => $row[3],
                "km_final" => $row[4],
                "empreendimento" => $row[5],
                "tipo_de_intervencao" => $row[6],
                "competencia_do_licenciamento" => $row[7],
                "contrato" => $row[8],
                "empresa" => $row[9],
                "ose_emitida_sei" => $row[10],
                "ose_emitida_data" => $row[11],
                "etapa" => $row[12],
                "cod_siac" => $row[13],
                "produto" => $row[14],
                "item_edital" => $row[15],
                "familia" => $row[16],
                "subproduto" => $row[17],
                "qtd_ose" => $row[18],
                "r_ose" => $row[19],
                "campo" => $row[20],
                "relatorio" => $row[21],
                "situacao_dnit" => $row[22],
                "situacao_ext" => $row[23],
                "avanco" => $row[24],
                "req_ext" => $row[25],
                "apto_medicao_40" => $row[26],
                "medicao_40" => $row[27],
                "medicao_40_qtd" => $row[28],
                "processo_medicao_40" => $row[29],
                "apto_medicao_60" => $row[30],
                "medicao_60" => $row[31],
                "medicao_60_qtd" => $row[32],
                "processo_medicao_60" => $row[33],
                "data_de_inicio_previsto" => $row[34],
                "data_de_entrega_previsto" => $row[35],
                "versao_00_sei" => $row[36],
                "versao_00_data_de_entrega" => $row[37],
                "versao_00_sei_nt" => $row[38],
                "versao_00_data_nt" => $row[39],
                "versao_00_sei_oficio" => $row[40],
                "versao_00_data_oficio" => $row[41],
                "versao_01_sei" => $row[42],
                "versao_01_data_de_entrega" => $row[43],
                "versao_01_sei_nt" => $row[44],
                "versao_01_data_nt" => $row[45],
                "versao_01_sei_oficio" => $row[46],
                "versao_01_data_oficio" => $row[47],
                "versao_02_sei" => $row[48],
                "versao_02_data_de_entrega" => $row[49],
                "versao_02_sei_nt" => $row[50],
                "versao_02_data_nt" => $row[51],
                "versao_02_sei_oficio" => $row[52],
                "versao_02_data_oficio" => $row[53],
                "versao_03_sei" => $row[54],
                "versao_03_data_de_entrega" => $row[55],
                "versao_03_sei_nt" => $row[56],
                "versao_03_data_nt" => $row[57],
                "versao_03_sei_oficio" => $row[58],
                "versao_03_data_oficio" => $row[59],
                "versao_04_sei" => $row[60],
                "versao_04_data_de_entrega" => $row[61],
                "versao_04_sei_nt" => $row[62],
                "versao_04_data_nt" => $row[63],
                "versao_04_sei_oficio" => $row[64],
                "versao_04_data_oficio" => $row[65],
                "versao_aceita_sei" => $row[66],
                "versao_aceita_data" => $row[67],
                "req_ext_sei" => $row[68],
                "req_ext_data" => $row[69],
                "analise_ext_01_sei" => $row[70],
                "analise_ext_01_data" => $row[71],
                "versao_ext_01_sei" => $row[72],
                "versao_ext_01_data" => $row[73],
                "analise_ext_02_sei" => $row[74],
                "analise_ext_02_data" => $row[75],
                "versao_ext_02_sei" => $row[76],
                "versao_ext_02_data" => $row[77],
                "analise_ext_03_sei" => $row[78],
                "analise_ext_03_data" => $row[79],
                "versao_ext_03_sei" => $row[80],
                "versao_ext_03_data" => $row[81],
                "analise_ext_04_sei" => $row[82],
                "analise_ext_04_data" => $row[83],
                "versao_ext_04_sei" => $row[84],
                "versao_ext_04_data" => $row[85],
                "aut_ext_sei" => $row[86],
                "aut_ext_data" => $row[87],
                ]);
        endif;
    }
    public function startRow(): int {
        return 2;
    }
}
class DashboardImportsubprodutos implements ToModel, WithStartRow, WithCalculatedFormulas {
    public function model(array $row) {
        if (!in_array($row[0], [Null, 'NULL', 'null', null, NULL, "", " "])):
            return new SgcvwSubprodutos([
                "contrato_id" => 1,
                "cod_siac" => $row[0],
                "produto" => $row[1],
                "subproduto" => $row[2],
                "familia" => $row[3],
                "descricao_siac" => $row[4],
                "descricao_revisada" => $row[5],
                "und" => $row[6],
                "etapa" => $row[7],
                "contrato" => $row[8],
                "req_ext" => $row[9],
                "prazo_de_elaboracao" => $row[10],
                "qtd_contrato" => $row[11],
                "qtd_1_ta" => $row[12],
                "qtd_2_ta" => $row[13],
                "qtd_ose" => $row[14],
                "qtd_saldo_ose" => $row[15],
                "qtd_medido" => $row[16],
                "qtd_saldo_medido" => $row[17],
                "r_preco_unitario" => $row[18],
                "r_total_contrato" => $row[19],
                "r_1_ta" => $row[20],
                "r_2_ta" => $row[21],
                "r_ose" => $row[22],
                "r_saldo_ose" => $row[23],
                "r_medido" => $row[24],
                "r_saldo_a_medir" => $row[25],
            ]);
        endif;
    }
    public function startRow(): int {
        return 2;
    }
}
