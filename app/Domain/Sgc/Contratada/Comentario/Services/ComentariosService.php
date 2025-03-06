<?php

namespace App\Domain\Sgc\Contratada\Comentario\Services;

use App\Models\SgcComentarios;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;

class ComentariosService extends BaseModelService
{
    use Deletable;

    protected string $modelClass = SgcComentarios::class;

    public function salvarComentarios($request)
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


}
