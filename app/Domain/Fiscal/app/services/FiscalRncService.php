<?php

namespace App\Domain\Fiscal\app\services;

use App\Models\ServicoConOcorrOcorrenciSupervisaoExecOcorrencia;
use App\Models\ServicoConOcorrSupervisaoExecOcorrenciaHistorico;
use App\Models\Servicos;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;

class FiscalRncService extends BaseModelService
{
  use Searchable, Deletable;

  protected string $modelClass = ServicoConOcorrOcorrenciSupervisaoExecOcorrencia::class;
  protected string $modelClassHistorico = ServicoConOcorrSupervisaoExecOcorrenciaHistorico::class;

  public function index(int $contrato_id, array $searchParams)
  {
    $servico_ids = Servicos::where('id_contrato', '=', $contrato_id)->pluck('id')->toArray();

    return [
      'rncs' => $this->searchAllColumns(...$searchParams)
        ->with(['vistorias', 'ocorrencia_anterior', 'lote', 'historico.levantamento'])
        ->where('tipo', '=', 'RNC')
        ->whereIn('id_servico', $servico_ids)
        ->paginate()
        ->appends($searchParams)
    ];
  }

  public function emitirParecer(int $id_ocorrencia_levantamento, array $post)
  {
    $response = $this->dataManagement->update(entity: $this->modelClass, infos: $post, id: $post['id']);

    $this->dataManagement->create(entity: $this->modelClassHistorico, infos: [
      'id_ocorrencia' => $post['id'],
      'id_ocorrencia_levantamento' => $id_ocorrencia_levantamento,
      'parecer' => $post['parecer_fiscal']
    ]);

    return $response;
  }
}
