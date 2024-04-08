<?php

namespace App\Domain\Contrato\Contratada\Servico\Veiculo\Services;

use App\Models\ServicoVeiculo;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;

class ServicoVeiculoService extends BaseModelService
{
  use Searchable, Deletable;

  protected string $modelClass = ServicoVeiculo::class;

  public function storeServicoVeiculo($request)
  {
    $response = $this->dataManagement->create(entity: $this->modelClass, infos: $request);

    return [
      'request' => $response['request']
    ];
  }

  public function deleteVeiculo($veiculo, $request)
  {
    try {
      $this->modelClass::Where('recurso_veiculo_id', $veiculo->id)->where('servico_id', $request['servico_id'])->firstOrFail()->delete();

      $response = [
        'type' => 'success',
        'content' => 'Recurso deletado com sucesso!'
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
