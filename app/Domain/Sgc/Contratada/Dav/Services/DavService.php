<?php

namespace App\Domain\Sgc\Contratada\Dav\Services;

use App\Models\Dav;
use App\Models\SgcDavProfissionais;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;

class DavService extends BaseModelService
{
  use Searchable, Deletable;

  protected string $modelClass = Dav::class;
  protected string $davProfissionaisModel = SgcDavProfissionais::class;

  public function salvarDav($dados)
  {

    $response = $this->dataManagement->create(entity: $this->modelClass, infos: $dados);

    return $response;
  }

//   public function update($request)
//   {
//     $introducao = $this->dataManagement->update(entity: $this->modelClass, infos: $request, id: $request['id']);

//     return [
//       'request' => $introducao['request']
//     ];
//   }

  public function salvarDavProfissionais($dados)
  {

    $response = $this->dataManagement->create(entity: $this->davProfissionaisModel, infos: $dados);

    return $response; // Retorne o modelo criado, ou faça outro tipo de resposta
  }
}
