<?php

namespace App\Domain\Contrato\Contratada\DadosGerais\Introducao\Services;

use App\Models\Contrato;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;

class IntroducaoService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = Contrato::class;


    public function update($request)
    {
        $introducao = $this->dataManagement->update(entity: $this->modelClass, infos: $request, id: $request['id']);

        return [
            'request' => $introducao['request']
        ];
    }
}
