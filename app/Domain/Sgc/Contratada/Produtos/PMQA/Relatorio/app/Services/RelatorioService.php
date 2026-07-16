<?php

namespace App\Domain\Sgc\Contratada\Produtos\PMQA\Relatorio\app\Services;

use App\Models\SgcPmqaRelatorio;
use App\Models\ServicoPmqaResultado;
use App\Models\SgcPmqa;
use App\Models\SgcPmqaResultado;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;
use Illuminate\Contracts\Database\Eloquent\Builder;

class RelatorioService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = SgcPmqaRelatorio::class;

    public function index(SgcPmqa $pmqa, array $searchParams): array
    {
        $relatorios = $this->searchAllColumns(...$searchParams)
            ->with([
                'resultado.analises',
                'resultado.analise_iqa',
                'resultado.outras_analises',
                'resultado.campanhas.pontos.lista.parametros',
                'resultado.campanhas.campanha_pontos.ponto',
                'resultado.campanhas.campanha_pontos.medicao.parametros'
            ])
            ->where('pmqa_id', $pmqa->id)
            ->paginate()
            ->appends($searchParams);
        $resultados = SgcPmqaResultado::with(['campanhas'])->where('pmqa_id', $pmqa->id)->get();

        return [
            'relatorios' => $relatorios,
            'resultados' => $resultados
        ];
    }

    public function store(array $request): array
    {
        return $this->dataManagement->create(entity: $this->modelClass, infos: $request);
    }

    public function update(array $request): array
    {
        return $this->dataManagement->update(entity: $this->modelClass, infos: $request, id: $request['id']);
    }

    public function destroy(SgcPmqaRelatorio $relatorio): array
    {
        return $this->dataManagement->delete(entity: $this->modelClass, id: $relatorio->id);
    }
}
