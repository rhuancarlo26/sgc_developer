<?php

namespace App\Domain\Servico\MonAtpFauna\Relatorios\app\Services;

use App\Models\AtFaunaRelatorio;
use App\Models\Servicos;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;

class RelatoriosService extends BaseModelService
{
  use Searchable, Deletable;

  protected string $modelClass = AtFaunaRelatorio::class;

  public function index(Servicos $servico, array $searchParams)
  {
    return $this->searchAllColumns(...$searchParams)
        ->where('fk_servico', $servico->id)
        ->paginate();
  }

  public function store(array $request): array
  {
    return $this->dataManagement->create(entity: $this->modelClass, infos: $request);
  }

    public function update(array $request)
    {
        return $this->dataManagement->update(entity: $this->modelClass, infos: $request, id: $request['id']);

    }
}
