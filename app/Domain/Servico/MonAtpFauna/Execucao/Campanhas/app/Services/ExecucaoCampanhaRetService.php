<?php

namespace App\Domain\Servico\MonAtpFauna\Execucao\Campanhas\app\Services;

use App\Models\AtFaunaExecucaoCampanhaRet;
use App\Shared\Abstract\BaseModelService;

class ExecucaoCampanhaRetService extends BaseModelService
{

    protected string $modelClass = AtFaunaExecucaoCampanhaRet::class;

    public function getRetsCampanhasVinculadas($campanhaId)
    {
        return $this->model
            ->select([
                'at_fauna_execucao_campanha_ret.id',
                'afcvr.id AS id_ret',
                'afcvr.created_at',
                'afcvr.nome_arquivo'
            ])
            ->join('at_fauna_config_vinculacao_ret AS afcvr', 'afcvr.id', '=', 'at_fauna_execucao_campanha_ret.fk_config_ret')
            ->where('at_fauna_execucao_campanha_ret.fk_execucao_campanha', $campanhaId)
            ->get();
    }

    public function vincularRet(array $request): array
    {
        return $this->dataManagement->create(entity: $this->modelClass, infos: $request);
    }
}
