<?php

namespace App\Domain\Servico\SupressaoVegetacao\Execucao\Supressao\Services;

use App\Models\CorteEspecie;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;

class CorteEspecieService extends BaseModelService
{
    use Deletable;

    protected string $modelClass = CorteEspecie::class;

}
