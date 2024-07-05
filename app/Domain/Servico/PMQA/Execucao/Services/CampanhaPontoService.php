<?php

namespace App\Domain\Servico\PMQA\Execucao\Services;

use App\Models\ServicoPmqaCampanha;
use App\Models\ServicoPmqaParametroLista;
use App\Models\ServicoPmqaPonto;
use App\Models\Servicos;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;
use Illuminate\Contracts\Database\Eloquent\Builder;

class CampanhaPontoService extends BaseModelService
{
  use Searchable, Deletable;

  protected string $modelClass = ServicoPmqaPonto::class;

  public function index(ServicoPmqaCampanha $campanha, $searchParams): array
  {
    $pontos = $this->searchAllColumns(...$searchParams)
      ->whereHas('campanhas', function (Builder $query) use ($campanha) {
        $query->where('campanha_id', $campanha->id);
      })
      ->paginate()
      ->appends($searchParams);

    return [
      'pontos' => $pontos
    ];
  }
}