<?php

namespace App\Domain\Servico\SupressaoVegetacao\Configuracao\PatioEstocagem\Services;

use App\Models\TipoPatio;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Searchable;

class TipoPatioService extends BaseModelService
{
    use Searchable;

    protected string $modelClass = TipoPatio::class;

}
