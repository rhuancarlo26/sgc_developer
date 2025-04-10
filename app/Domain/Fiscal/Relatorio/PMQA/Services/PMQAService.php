<?php

namespace App\Domain\Fiscal\Relatorio\PMQA\Services;

use App\Models\ServicoPmqaRelatorio;
use App\Models\Servicos;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;

class PMQAService extends BaseModelService
{
  use Searchable, Deletable;

  protected string $modelClass = ServicoPmqaRelatorio::class;

  public function index(object $contrato, array $searchParams): array
  {
    $servicoIds = Servicos::where('id_contrato', $contrato->id)
      ->where('servico', 1)
      ->pluck('id')
      ->toArray();

    $relatorios = $this->searchAllColumns(...$searchParams)
      ->with([
        'servico.tipo',
        'servico.tema',
        'resultado.analises',
        'resultado.analise_iqa',
        'resultado.outras_analises',
        'resultado.campanhas.pontos.lista.parametros'
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
