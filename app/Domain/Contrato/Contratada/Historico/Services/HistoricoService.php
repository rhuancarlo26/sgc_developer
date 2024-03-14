<?php

namespace App\Domain\Contrato\Contratada\Historico\Services;

use App\Models\Contrato;
use App\Models\ContratoHistorico;
use App\Models\ContratoLicenca;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;

class HistoricoService extends BaseModelService
{
  use Searchable, Deletable;

  protected string $modelClass = ContratoHistorico::class;

  public function salvarHistorico($request)
  {
    $response = $this->dataManagement->create(entity: $this->modelClass, infos: $request);

    return [
      'request' => $response['request']
    ];
  }

  public function update($request)
  {
    $introducao = $this->dataManagement->update(entity: $this->modelClass, infos: $request, id: $request['id']);

    return [
      'request' => $introducao['request']
    ];
  }
}