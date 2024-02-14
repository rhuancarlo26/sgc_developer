<?php

namespace App\Domain\Licenca\Services;

use App\Models\Licenca;
// use App\Shared\BaseClasses\BaseModelService;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;

class ListagemLicencaService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = Licenca::class;
}