<?php

namespace App\Domain\Sgc\Contratada\Comentario\Services;

use App\Models\SgcComentario;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;

class ComentarioService extends BaseModelService
{
  use Searchable, Deletable;

  protected string $modelClass = SgcComentario::class;

  public function salvarComentario($request)
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

  public function buscarTodos($filters)
  {
      $query = $this->modelClass::query();

      if (!empty($filters)) {
          // Exemplo: $query->where('campo', $filters['campo']);
      }

      return $query->get();
  }
}
