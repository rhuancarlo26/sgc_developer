<?php

namespace App\Domain\Servico\MonitoraFauna\Resultado\Services;

use App\Models\ServicoMonitoraFaunaExecCampanha;
use App\Models\ServicoMonitoraFaunaResultado;
use App\Models\ServicoMonitoraFaunaResultadoCampanha;
use App\Models\Servicos;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Searchable;

class ResultadoService extends BaseModelService
{
  use Searchable;

  protected string $modelClass = ServicoMonitoraFaunaResultado::class;
  protected string $modelClassCampanha = ServicoMonitoraFaunaResultadoCampanha::class;

  public function index(Servicos $servico, array $searchParams): array
  {
    return [
      'resultados' => $this->searchAllColumns(...$searchParams)
        ->with(['campanhas', 'campanhas'])
        ->where('id_servico', $servico->id)
        ->paginate()
        ->appends($searchParams),
      'campanhas' => ServicoMonitoraFaunaExecCampanha::select(['id'])->where('id_servico', $servico->id)->get()
    ];
  }

  public function store($post)
  {
    $response = $this->dataManagement->create(entity: $this->modelClass, infos: $post);

    $response['model']->campanhas()->sync(collect($post['campanhas'])->pluck('id'));

    return $response;
  }

  public function update($post)
  {
    $response = $this->dataManagement->update(entity: $this->modelClass, infos: $post, id: $post['id']);

    $this->modelClass::find($post['id'])->campanhas()->sync(collect($post['campanhas'])->pluck('id'));

    return $response;
  }

  public function destroy($resultado)
  {
    $resultado->campanhas()->sync(collect([]));

    return $this->dataManagement->delete(entity: $this->modelClass, id: $resultado->id);
  }
}
