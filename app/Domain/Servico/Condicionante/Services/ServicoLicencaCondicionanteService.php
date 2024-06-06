<?php

namespace App\Domain\Servico\Condicionante\Services;

use App\Models\ServicoLicencaCondicionante;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;

class ServicoLicencaCondicionanteService extends BaseModelService
{
  use Searchable, Deletable;

  protected string $modelClass = ServicoLicencaCondicionante::class;

  public function storeServicoLicencaCondicionte($request)
  {
    $response = $this->dataManagement->create(entity: $this->modelClass, infos: $request);

    return [
      'request' => $response['request']
    ];
  }

  public function deleteServicoLicencaCondicionte($request)
  {
    try {
      $this->modelClass::Where('condicionante_id', $request['condicionante_id'])
        ->where('servico_id', $request['servico_id'])
        ->where('licenca_id', $request['licenca_id'])
        ->firstOrFail()->delete();

      $response = [
        'type' => 'success',
        'content' => 'Condicionante deletado com sucesso!'
      ];
    } catch (\Exception $e) {
      $response = [
        'type' => 'error',
        'content' => $e->getMessage()
      ];
    }

    return [
      'request' => $response
    ];
  }
}
