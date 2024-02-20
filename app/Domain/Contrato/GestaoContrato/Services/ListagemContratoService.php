<?php

namespace App\Domain\Contrato\GestaoContrato\Services;

use App\Models\Contrato;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;

class ListagemContratoService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = Contrato::class;

    public function ListagemLicencas($searchParams, $tipo)
    {
        return [
            'contratos' => $this->search(...$searchParams)
                ->where('tipo_id', $tipo->id)
                ->paginate()
                ->appends($searchParams),
            'tipo' => $tipo
        ];
    }
}
