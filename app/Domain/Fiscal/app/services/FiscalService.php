<?php

namespace App\Domain\Fiscal\app\Services;

use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;
use App\Models\Contrato;

class FiscalService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = Contrato::class;

    public function ListagemContratos($tipo, $searchParams)
    {
        return [
            'contratos' => $this->searchAllColumns(...$searchParams)
                ->with(['aditivos'])
                ->where('tipo_id', $tipo->id)
                ->paginate()
                ->appends($searchParams)
        ];
    }
}
