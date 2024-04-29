<?php

namespace App\Domain\Contrato\Contratada\DadosGerais\Licenciamento\Observacao\Services;

use App\Models\ContratoLicencaObservacao;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;

class LicenciamentoObservacaoService extends BaseModelService
{
  use Searchable, Deletable;

  protected string $modelClass = ContratoLicencaObservacao::class;

  public function salvarLicenciamentoObservacao($request)
  {
    $response = $this->dataManagement->create(entity: $this->modelClass, infos: $request);

    return [
      'request' => $response['request']
    ];
  }

  public function update(array $post): array
  {
    $licenca = $this->dataManagement->update(entity: $this->modelClass, infos: $post, id: $post['id']);

    return [
      'request' => $licenca['request']
    ];
  }
}
