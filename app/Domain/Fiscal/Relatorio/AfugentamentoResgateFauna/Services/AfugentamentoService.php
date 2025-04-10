<?php

namespace App\Domain\Fiscal\Relatorio\AfugentamentoResgateFauna\Services;

use App\Models\AfugentFaunaRelatorioModel;
use App\Models\ServicoConOcorrSupervisaoRelatorio;
use App\Models\Servicos;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;

class AfugentamentoService extends BaseModelService
{
  use Searchable, Deletable;

  protected string $modelClass = AfugentFaunaRelatorioModel::class;

  public function index(object $contrato, array $searchParams): array
  {
    $servicoIds = Servicos::where('id_contrato', $contrato->id)
      ->where('servico', 2)
      ->pluck('id')
      ->toArray();

    $relatorios = $this->searchAllColumns(...$searchParams)
      ->with([
        'servico.tipo',
        'servico.tema'
      ])
      ->whereIn('id_servico', $servicoIds)
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
