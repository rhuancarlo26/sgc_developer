<?php

namespace App\Domain\Contrato\GestaoContrato\Aditivo\Services;

use App\Models\ContratoAditivo;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;

class AditivoService extends BaseModelService
{
  use Searchable, Deletable;

  protected string $modelClass = ContratoAditivo::class;

  public function store($request)
  {
    return $this->dataManagement->create(entity: $this->modelClass, infos: $request);
  }

  public function update($request)
  {
    return $this->dataManagement->update(entity: $this->modelClass, infos: $request, id: $request['id']);
  }
}
