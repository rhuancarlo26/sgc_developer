<?php

namespace App\Domain\Servico\PMQA\Execucao\Coleta\app\Services;

use App\Models\ServicoPmqaCampanha;
use App\Models\ServicoPmqaCampanhaPontoColeta;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;

class ColetaService extends BaseModelService
{
  use Searchable, Deletable;

  protected string $modelClass = ServicoPmqaCampanhaPontoColeta::class;

  public function store(array $request): array
  {
    return $this->dataManagement->create(entity: $this->modelClass, infos: $request);
  }
  public function update(array $request): array
  {
    return $this->dataManagement->update(entity: $this->modelClass, infos: $request, id: $request['id']);
  }
}