<?php

namespace App\Domain\Fiscal\Relatorio\SupressaoVegetacao\Services;

use App\Models\Servicos;
use App\Models\SupressaoRelatorio;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;

class SupressaoService extends BaseModelService
{
  use Searchable, Deletable;

  protected string $modelClass = SupressaoRelatorio::class;

  public function index(object $contrato, array $searchParams): array
  {
    $servicoIds = Servicos::where('id_contrato', $contrato->id)
      ->where('servico', 6)
      ->pluck('id')
      ->toArray();

    $relatorios = $this->searchAllColumns(...$searchParams)
      ->with([
        'servico.tipo',
        'servico.tema'
      ])
      ->whereIn('fk_servico', $servicoIds)
      ->paginate()
      ->appends($searchParams);

    return [
      'relatorios' => $relatorios
    ];
  }

  public function store(array $post): array
  {
    return $this->dataManagement->update(entity: $this->modelClass, infos: $post, id: $post['id']);
  }
}
