<?php

namespace App\Domain\Contrato\Contratada\DadosGerais\Empreendimento\Services;

use App\Models\ContratoEmpreendimentoTrecho;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;

class EmpreendimentoTrechoService extends BaseModelService
{
  use Searchable, Deletable;

  protected string $modelClass = ContratoEmpreendimentoTrecho::class;

  public function salvarTrecho($request)
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

  public function destroy($request)
  {
    $introducao = $this->dataManagement->delete(entity: $this->modelClass, infos: $request);

    return [
      'request' => $introducao['request']
    ];
  }
}
