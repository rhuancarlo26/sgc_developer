<?php

namespace App\Domain\Contrato\Contratada\DadosGerais\Licenciamento\Services;

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

  public function deleteLicenciamento($licenca, $request)
  {
    try {
      $this->modelClass::Where('licenca_id', $licenca->id)->where('contrato_id', $request['contrato_id'])->firstOrFail()->delete();

      $response = [
        'type' => 'success',
        'content' => 'Licenciamento deletado com sucesso!'
      ];
    } catch (\Exception $e) {
      $response = [
        'type' => 'error',
        'content' => $e->getMessage()
      ];
    }

    return $response;
  }

  public function licenciamentos($contrato_id)
  {
    return $this->modelClass::Where('contrato_id', $contrato_id)->get();
  }
}
