<?php

namespace App\Domain\Servico\SupressaoVegetacao\Execucao\Supressao\Services;

use App\Models\EstagioSucessional;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Searchable;

class EstagioSucessionalService extends BaseModelService
{
    use Searchable;

    protected string $modelClass = EstagioSucessional::class;

}
