<?php

namespace App\Domain\Servico\PMQA\Resultado\app\Services;

use App\Models\ServicoPmqaCampanha;
use App\Models\ServicoPmqaParametro;
use App\Models\ServicoPmqaResultado;
use App\Models\ServicoPmqaResultadoAnaliseParametro;
use App\Models\Servicos;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;

class ResultadoService extends BaseModelService
{
  use Searchable, Deletable;

  protected string $modelClass = ServicoPmqaResultado::class;
  protected string $modelClassAnalise = ServicoPmqaResultadoAnaliseParametro::class;

  public function index(Servicos $servico, array $searchParams): array
  {
    $resultados = $this->searchAllColumns(...$searchParams)
      ->with(['campanhas'])
      ->where('servico_id', $servico->id)
      ->paginate()
      ->appends($searchParams);

    $campanhas = ServicoPmqaCampanha::where('servico_id', $servico->id)->get();

    return [
      'resultados' => $resultados,
      'campanhas' => $campanhas
    ];
  }

  public function store(array $request): array
  {
    $response = $this->dataManagement->create(entity: $this->modelClass, infos: $request);

    $response['model']->campanhas()->sync(collect($request['campanhas_selecionadas'])->pluck('id'));

    return $response;
  }

  public function storeAnalises(array $request): array
  {
    foreach ($request['analises'] as $key => $value) {
      $response = $this->dataManagement->create(entity: $this->modelClassAnalise, infos: [
        'resultado_id' => $request['resultado_id'],
        'parametro_id' => $key,
        'analise' => $value
      ]);
    }

    return $response;
  }

  public function updateAnalises(array $request): array
  {
    foreach ($request['analises'] as $key => $value) {
      if (gettype($value) == 'string') {
        $response = $this->dataManagement->create(entity: $this->modelClassAnalise, infos: [
          'resultado_id' => $request['resultado_id'],
          'parametro_id' => $key,
          'analise' => $value
        ]);
      } else {
        $response = $this->dataManagement->update(entity: $this->modelClassAnalise, infos: [
          'resultado_id' => $request['resultado_id'],
          'parametro_id' => $key,
          'analise' => $value['analise']
        ], id: $value['id']);
      }
    }

    return $response;
  }

  public function update(array $request): array
  {
    $response = $this->dataManagement->update(entity: $this->modelClass, infos: $request, id: $request['id']);

    $this->modelClass::find($request['id'])->campanhas()->sync(collect($request['campanhas_selecionadas'])->pluck('id'));

    return $response;
  }

  public function destroy(ServicoPmqaResultado $resultado): array
  {
    return $this->dataManagement->delete(entity: $this->modelClass, id: $resultado->id);
  }

  public function resultado()
  {
    return [
      'parametros' => ServicoPmqaParametro::all()
    ];
  }
}