<?php

namespace App\Domain\Servico\PMQA\Relatorio\app\Services;

use App\Models\ServicoPmqaCampanha;
use App\Models\ServicoPmqaParametroLista;
use App\Models\ServicoPmqaPonto;
use App\Models\ServicoPmqaRelatorio;
use App\Models\ServicoPmqaResultado;
use App\Models\Servicos;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;
use Illuminate\Contracts\Database\Eloquent\Builder;

class RelatorioService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = ServicoPmqaRelatorio::class;

    public function index(Servicos $servico, array $searchParams): array
    {
        $relatorios = $this->searchAllColumns(...$searchParams)
            ->with([
                'resultado.analises',
                'resultado.analise_iqa',
                'resultado.outras_analises',
                'resultado.campanhas.pontos.lista.parametros'
            ])
            ->where('fk_servico', $servico->id)
            ->paginate()
            ->appends($searchParams);

        $resultados = ServicoPmqaResultado::with(['campanhas'])->where('fk_servico', $servico->id)->get();

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

    public function destroy(ServicoPmqaRelatorio $relatorio): array
    {
        return $this->dataManagement->delete(entity: $this->modelClass, id: $relatorio->id);
    }
}
