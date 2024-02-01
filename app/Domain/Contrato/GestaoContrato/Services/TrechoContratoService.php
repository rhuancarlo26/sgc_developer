<?php

namespace App\Domain\Contrato\GestaoContrato\Services;

use App\Models\contratoTrecho;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;

class TrechoContratoService extends BaseModelService
{
  use Deletable;

  protected string $modelClass = contratoTrecho::class;
}