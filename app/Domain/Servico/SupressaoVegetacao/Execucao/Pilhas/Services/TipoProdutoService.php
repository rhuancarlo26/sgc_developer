<?php

namespace App\Domain\Servico\SupressaoVegetacao\Execucao\Pilhas\Services;

use App\Models\TipoProduto;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Searchable;

class TipoProdutoService extends BaseModelService
{
    use Searchable;

    protected string $modelClass = TipoProduto::class;

}
