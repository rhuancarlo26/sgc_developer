<?php

namespace App\Domain\Contrato\GestaoContrato\Services;

use App\Models\Contrato;
use App\Shared\BaseClasses\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;

class ListagemContratoService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = Contrato::class;
}