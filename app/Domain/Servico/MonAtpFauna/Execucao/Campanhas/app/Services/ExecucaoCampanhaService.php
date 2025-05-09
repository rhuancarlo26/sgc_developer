<?php

namespace App\Domain\Servico\MonAtpFauna\Execucao\Campanhas\app\Services;

use App\Models\AtFaunaExecucaoCampanhaAbio;
use App\Models\AtFaunaExecucaoCampanhaEquipamento;
use App\Models\AtFaunaExecucaoCampanhaEquipe;
use App\Models\AtFaunaExecucaoCampanhaResposavel;
use App\Models\AtFaunaExecucaoCampanhaTrechoPavimentacao;
use App\Models\AtFaunaExecucaoCampanhaVeiculo;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;
use Illuminate\Support\Facades\DB;

class ExecucaoCampanhaService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = AtFaunaExecucaoCampanhaAbio::class;

    public function vincularAbio(array $request): array
    {
        return $this->dataManagement->create(entity: $this->modelClass, infos: $request);
    }

    public function getAbioCampanhas($campanhaId)
    {
        return $this->model
            ->select([
                'at_fauna_execucao_campanha_abio.id',
                'licencas.numero_licenca',
                'licencas.empreendimento',
                'licencas.emissor',
                DB::raw('DATE_FORMAT(licencas.data_emissao, "%d/%m/%Y") as data_emissao'),
                'licencas.vencimento',
                'licencas.processo_dnit',
                'licencas.arquivo_licenca',
                'licencas.fiscal'
            ])
            ->join('at_fauna_config_vinculacao AS afcv', 'afcv.id', '=', 'at_fauna_execucao_campanha_abio.fk_config_vinculacao')
            ->join('licencas', 'licencas.id', '=', 'afcv.fk_licenca')
            ->join('tipo_licencas', 'licencas.tipo', '=', 'tipo_licencas.id')
            ->where('at_fauna_execucao_campanha_abio.fk_execucao_campanha', $campanhaId)
            ->get();
    }

    public function getRelationshipCampanha($campanhaId)
    {
        return [
            'recurso_responsaveis' => AtFaunaExecucaoCampanhaResposavel::with('servico_rh.rh')->where('at_fauna_execucao_campanha_id', $campanhaId)->get(),
            'recurso_equipes' => AtFaunaExecucaoCampanhaEquipe::with('servico_rh.rh')->where('at_fauna_execucao_campanha_id', $campanhaId)->get(),
            'recurso_equipamentos' => AtFaunaExecucaoCampanhaEquipamento::with('servico_equipamento.equipamento')->where('at_fauna_execucao_campanha_id', $campanhaId)->get(),
            'recurso_veiculos' => AtFaunaExecucaoCampanhaVeiculo::with('servico_veiculo.veiculo')->where('at_fauna_execucao_campanha_id', $campanhaId)->get(),
            'trechos_pavimentacao' => AtFaunaExecucaoCampanhaTrechoPavimentacao::where('campanha_id', $campanhaId)->get()
        ];
    }

    public function getRetsCampanhas($campanhaId)
    {
        return $this->model
            ->select([
                'afcvr.id',
                'licencas.numero_licenca',
                'afcvr.nome_arquivo'
            ])
            ->join('at_fauna_config_vinculacao AS afcv', 'afcv.id', '=', 'at_fauna_execucao_campanha_abio.fk_config_vinculacao')
            ->join('at_fauna_config_vinculacao_ret AS afcvr', 'afcv.id', '=', 'afcvr.fk_at_config_vinculacao')
            ->join('licencas', 'licencas.id', '=', 'afcv.fk_licenca')
            ->join('tipo_licencas', 'licencas.tipo', '=', 'tipo_licencas.id')
            ->where('at_fauna_execucao_campanha_abio.fk_execucao_campanha', $campanhaId)
            ->get();
    }
}
