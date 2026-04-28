<?php

namespace App\Domain\Sgc\Contratada\Produtos\PMQA\Execucao\app\Services;

use App\Models\ServicoPmqaCampanha;
use App\Models\ServicoPmqaPonto;
use App\Models\Servicos;
use App\Models\SgcPmqa;
use App\Models\SgcPmqaExecCampanha;
use App\Models\SgcPmqaPonto;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;
use Illuminate\Support\Facades\Log;

class CampanhaService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = SgcPmqaExecCampanha::class;

    public function index(SgcPmqa $pmqa, $searchParams): array
    {
        $campanhas = $this->searchAllColumns(...$searchParams)
            ->with(['pontos'])
            ->where('pmqa_id', $pmqa->id)
            ->paginate()
            ->appends($searchParams);
        $pontos = SgcPmqaPonto::with(['vinculacao', 'campanhas'])->where('pmqa_id', $pmqa->id)->get();

        return [
            'campanhas' => $campanhas,
            'pontos'    => $pontos
        ];
    }

    public function store(array $request): array
    {
        Log::info('CampanhaService@store', [
            'pmqa_id' => $request['pmqa_id'] ?? null,
            'pontos_count' => isset($request['pontos']) ? count($request['pontos']) : null,
        ]);

        // MUITO recomendado: tirar 'pontos' do create (evita create “interpretar” array)
        $dadosCampanha = collect($request)->except('pontos')->toArray();

        $response = $this->dataManagement->create(entity: $this->modelClass, infos: $dadosCampanha);

        Log::info('CampanhaService@store created', [
            'campanha_id' => $response['model']->id ?? null,
        ]);

        $response['model']->pontos()->sync($request['pontos'] ?? []);

        Log::info('CampanhaService@store synced', [
            'campanha_id' => $response['model']->id ?? null,
        ]);

        return $response;
    }

    public function update(array $request): array
    {
        $response = $this->dataManagement->update(entity: $this->modelClass, infos: $request, id: $request['id']);

        ServicoPmqaCampanha::find($request['id'])->pontos()->sync(collect($request['pontos'])->toArray());

        return $response;
    }

    public function destroy(ServicoPmqaCampanha $campanha): array
    {
        return $this->dataManagement->delete(entity: $this->modelClass, id: $campanha->id);
    }
}
