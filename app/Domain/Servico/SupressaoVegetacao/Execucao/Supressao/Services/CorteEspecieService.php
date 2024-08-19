<?php

namespace App\Domain\Servico\SupressaoVegetacao\Execucao\Supressao\Services;

use App\Models\CorteEspecie;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;

class CorteEspecieService extends BaseModelService
{
    use Deletable;

    protected string $modelClass = CorteEspecie::class;

    public function getSomaByAreaSupressao(array $areaSupressaoIds)
    {
        return $this->model
            ->selectRaw('nome, nome_popular, legislacao, SUM(qtd_corte) AS n_individuos, SUM(compensacao) AS n_compensacao')
            ->whereIn('area_supressao_id', $areaSupressaoIds)
            ->groupBy('nome', 'nome_popular', 'legislacao')
            ->get();
    }

}
