<?php

namespace App\Domain\Servico\SupressaoVegetacao\Execucao\Supressao\Services;

use App\Models\TipoBioma;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Searchable;

class TipoBiomaService extends BaseModelService
{
    use Searchable;

    protected string $modelClass = TipoBioma::class;

}
