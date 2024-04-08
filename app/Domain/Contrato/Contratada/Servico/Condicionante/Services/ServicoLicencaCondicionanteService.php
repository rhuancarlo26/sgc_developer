<?php

namespace App\Domain\Contrato\Contratada\Servico\Condicionante\Services;

use App\Models\ServicoEquipamento;
use App\Models\ServicoLicencaCondicionante;
use App\Models\ServicoRh;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;

class ServicoLicencaCondicionanteService extends BaseModelService
{
  use Searchable, Deletable;

  protected string $modelClass = ServicoLicencaCondicionante::class;

  public function StoreServicoLicencaCondicionte($request)
  {
    $response = $this->dataManagement->create(entity: $this->modelClass, infos: $request);

    return [
      'request' => $response['request']
    ];
  }
}