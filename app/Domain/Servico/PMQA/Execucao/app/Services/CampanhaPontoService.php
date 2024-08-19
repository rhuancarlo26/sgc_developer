<?php

namespace App\Domain\Servico\PMQA\Execucao\app\Services;

use App\Models\ServicoPmqaCampanha;
use App\Models\ServicoPmqaCampanhaPonto;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;

class CampanhaPontoService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = ServicoPmqaCampanhaPonto::class;

    public function index(ServicoPmqaCampanha $campanha, $searchParams): array
    {
        $pontos = $this->searchAllColumns(...$searchParams)
            ->with([
                'ponto.lista.parametros_vinculados.parametro',
                'coleta.arquivos',
                'medicao.parametros',
                'medicao.arquivos',
            ])
            ->where('fk_exec_campanha', $campanha->id)
            ->paginate()
            ->appends($searchParams);

        return ['pontos' => $pontos];
    }
}
