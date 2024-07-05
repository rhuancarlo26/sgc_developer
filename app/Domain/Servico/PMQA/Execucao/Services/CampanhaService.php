<?php

namespace App\Domain\Servico\PMQA\Execucao\Services;

use App\Models\ServicoPmqaCampanha;
use App\Models\ServicoPmqaParametroLista;
use App\Models\ServicoPmqaPonto;
use App\Models\Servicos;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;
use Illuminate\Contracts\Database\Eloquent\Builder;

class CampanhaService extends BaseModelService
{
  use Searchable, Deletable;

  protected string $modelClass = ServicoPmqaCampanha::class;

  public function index(Servicos $servico, $searchParams): array
  {
    $campanhas = $this->searchAllColumns(...$searchParams)
      ->with(['pontos'])
      ->where('servico_id', $servico->id)
      ->paginate()
      ->appends($searchParams);
    $pontos = ServicoPmqaPonto::with(['vinculado'])->where('servico_id', $servico->id)->get();

    return [
      'campanhas' => $campanhas,
      'pontos' => $pontos
    ];
  }

  public function getPontosCampanha(ServicoPmqaCampanha $campanha)
  {
    return ServicoPmqaPonto::whereHas('campanhas', function (Builder $query) use ($campanha) {
      $query->where('campanha_id', $campanha->id);
    })->paginate();
  }

  public function store(array $request): array
  {
    $response = $this->dataManagement->create(entity: $this->modelClass, infos: $request);

    $response['model']->pontos()->sync(collect($request['pontos'])->toArray());

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