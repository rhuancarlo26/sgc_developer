<?php

namespace App\Domain\Contrato\Contratada\Servico\Equipamento\Services;

use App\Models\ServicoEquipamento;
use App\Models\ServicoRh;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;

class ServicoEquipamentoService extends BaseModelService
{
  use Searchable, Deletable;

  protected string $modelClass = ServicoEquipamento::class;

  public function StoreServicoEquipamento($request)
  {
    $response = $this->dataManagement->create(entity: $this->modelClass, infos: $request);

    return [
      'request' => $response['request']
    ];
  }

  public function deleteEquipamento($equipamento, $request)
  {
    try {
      $this->modelClass::Where('recurso_equipamento_id', $equipamento->id)->where('servico_id', $request['servico_id'])->firstOrFail()->delete();

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