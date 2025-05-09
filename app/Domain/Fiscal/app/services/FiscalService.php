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

    public function listagemServicos($contrato, $searchParams): array
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
                'parecerAfugentamento',
                'parecerOcorrencia',
                'parecer_passagem_fauna',
                'pontos',
                'parametros',
                'parametros.pontos',
                'supervisao_lotes.uf',
                'supervisao_lotes.rodovia',
                'licencas_condicionantes.licenca.tipo_rel',
                'passagem_fauna_passagens',
                'passagem_fauna_abios.licenca.tipo_rel',
                'parecer_atropelamento',
                'atropelamento_abios.licenca.tipo_rel'
            ])
            ->where('id_contrato', $contrato->id)
            ->where('servico', $id_servico)
            ->where('deleted_at', null)
            ->whereIn('status_aprovacao', [2, 3, 4]);

        return ['servicos' => $query->paginate()->appends($searchParams)];
    }
}
