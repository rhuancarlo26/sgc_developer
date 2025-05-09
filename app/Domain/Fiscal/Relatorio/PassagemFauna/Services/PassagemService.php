<?php

namespace App\Domain\Fiscal\Relatorio\PassagemFauna\Services;

use App\Models\ServicoConOcorrSupervisaoRelatorio;
use App\Models\ServicoPassagemFaunaRelatorio;
use App\Models\Servicos;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;

class PassagemService extends BaseModelService
{
  use Searchable, Deletable;

  protected string $modelClass = ServicoPassagemFaunaRelatorio::class;

  public function index(object $contrato, array $searchParams): array
  {
    $servicoIds = Servicos::where('id_contrato', $contrato->id)
      ->where('servico', 5)
      ->pluck('id')
      ->toArray();

    $relatorios = $this->searchAllColumns(...$searchParams)
      ->with([
        'servico.tipo',
        'servico.tema',
        'resultado.analise',
        'resultado.outras_analises',
        'resultado.campanhas.abios.abio.licenca.tipo_rel',
        'resultado.campanhas.registros.status_iunc',
        'resultado.campanhas.registros.status_federal',
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
