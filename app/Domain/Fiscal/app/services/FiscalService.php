<?php

namespace App\Domain\Fiscal\app\services;

use App\Models\Servicos;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;

class FiscalService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = Servicos::class;

    public function listagemServicos($contrato, $searchParams)
    {
        $query = $this->search(...$searchParams)
            ->with([
                'tipo',
                'tema',
                'rhs',
                'veiculos',
                'veiculos.codigo',
                'equipamentos',
                'condicionantes',
                'condicionantes.licenca',
                'parecer'
            ])
            ->where('id_contrato', $contrato->id)
            ->where('deleted_at', null)
            ->whereIn('status_aprovacao', [2, 3, 4]);

        return ['servicos' => $query->paginate()->appends($searchParams)];
    }

    public function listagemConfiguracao($contrato, $searchParams, $id_servico): array
    {
        $query = $this->search(...$searchParams)
            ->with([
                'tema',
                'tipo',
                'parecer',
                'parecerPmqa',
                'parecerSupressaoVegetacao',
            ])
            ->where('id_contrato', $contrato->id)
            ->where('servico', $id_servico)
            ->where('deleted_at', null)
            ->whereIn('status_aprovacao', [2, 3, 4]);

        return ['servicos' => $query->paginate()->appends($searchParams)];
    }
}
