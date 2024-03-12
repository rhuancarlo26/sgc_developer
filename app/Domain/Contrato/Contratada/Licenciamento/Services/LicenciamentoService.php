<?php

namespace App\Domain\Contrato\Contratada\Licenciamento\Services;

use App\Models\Contrato;
use App\Models\ContratoLicenca;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;

class LicenciamentoService extends BaseModelService
{
  use Searchable, Deletable;

  protected string $modelClass = ContratoLicenca::class;

  public function salvarLicenciamento($request)
  {
    $response = $this->dataManagement->create(entity: $this->modelClass, infos: $request);

    return [
      'request' => $response['request']
    ];
  }

  public function licenciamentos($contrato_id)
  {
    return $this->modelClass::Where('contrato_id', $contrato_id)->get();
  }
}