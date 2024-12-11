<?php

namespace App\Domain\Sgc\Contratada\app\Services;

use App\Models\Contrato;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Searchable;

class ContratosService extends BaseModelService
{
    use Searchable;

    protected string $modelClass = Contrato::class;

    public function Contratos($tipo, $searchParams)
    {

        return [
            'contratos' => Contrato::where('tipo_contrato', $tipo->id)->paginate()->appends($searchParams)
        ];    
    }
}