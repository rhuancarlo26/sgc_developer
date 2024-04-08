<?php

namespace App\Domain\Contrato\Contratada\Servico\Rh\Services;

use App\Models\RecursoEquipamento;
use App\Models\RecursoRh;
use App\Models\RecursoVeiculo;
use App\Models\ServicoRh;
use App\Models\Servicos;
use App\Models\ServicoTema;
use App\Models\ServicoTipo;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;

class ServicoRhService extends BaseModelService
{
  use Searchable, Deletable;

  protected string $modelClass = ServicoRh::class;

  public function storeServicoRh($request)
  {
    $response = $this->dataManagement->create(entity: $this->modelClass, infos: $request);

    return [
      'request' => $response['request']
    ];
  }
}
