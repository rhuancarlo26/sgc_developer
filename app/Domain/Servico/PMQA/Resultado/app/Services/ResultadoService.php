<?php

namespace App\Domain\Servico\PMQA\Resultado\app\Services;

use App\Models\ServicoPmqaCampanha;
use App\Models\ServicoPmqaParametro;
use App\Models\ServicoPmqaResultado;
use App\Models\ServicoPmqaResultadoAnaliseIqa;
use App\Models\ServicoPmqaResultadoAnaliseParametro;
use App\Models\ServicoPmqaResultadoOutraAnalise;
use App\Models\Servicos;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;
use Illuminate\Support\Facades\Storage;

class ResultadoService extends BaseModelService
{
  use Searchable, Deletable;

  protected string $modelClass = ServicoPmqaResultado::class;
  protected string $modelClassAnalise = ServicoPmqaResultadoAnaliseParametro::class;
  protected string $modelClassAnaliseIqa = ServicoPmqaResultadoAnaliseIqa::class;
  protected string $modelClassOutraAnalise = ServicoPmqaResultadoOutraAnalise::class;

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

  public function storeAnaliseIqa(array $request): array
  {
    return $this->dataManagement->create(entity: $this->modelClassAnaliseIqa, infos: $request);
  }

  public function storeOutraAnalise(array $request): array
  {
    if ($request['arquivo']->isvalid()) {
      $nome = $request['arquivo']->getClientOriginalName();
      $tipo = $request['arquivo']->extension();
      $caminho = $request['arquivo']->storeAs('Servico' . DIRECTORY_SEPARATOR . 'Pmqa' . DIRECTORY_SEPARATOR . 'Resultado' . DIRECTORY_SEPARATOR . 'OutraAnalise' . DIRECTORY_SEPARATOR . uniqid() . '_' . $nome);
    }

    return $this->dataManagement->create(entity: $this->modelClassOutraAnalise, infos: [
      'resultado_id' => $request['resultado_id'],
      'nome_arquivo' => $nome,
      'tipo' => $tipo,
      'caminho' => $caminho,
      'nome' => $request['nome'],
      'analise' => $request['analise']
    ]);
  }

  public function updateOutraAnalise(array $request): array
  {
    if ($outraAnalise = ServicoPmqaResultadoOutraAnalise::find($request['id'])) {
      if ($outraAnalise->caminho) {
        Storage::delete($outraAnalise->caminho);
      }
    }

    if ($request['arquivo']->isvalid()) {
      $nome = $request['arquivo']->getClientOriginalName();
      $tipo = $request['arquivo']->extension();
      $caminho = $request['arquivo']->storeAs('Servico' . DIRECTORY_SEPARATOR . 'Pmqa' . DIRECTORY_SEPARATOR . 'Resultado' . DIRECTORY_SEPARATOR . 'OutraAnalise' . DIRECTORY_SEPARATOR . uniqid() . '_' . $nome);
    }

    return $this->dataManagement->update(entity: $this->modelClassOutraAnalise, infos: [
      'resultado_id' => $request['resultado_id'],
      'nome_arquivo' => $nome,
      'tipo' => $tipo,
      'caminho' => $caminho,
      'nome' => $request['nome'],
      'analise' => $request['analise']
    ], id: $request['id']);
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

  public function updateAnaliseIqa(array $request): array
  {
    return $this->dataManagement->update(entity: $this->modelClassAnaliseIqa, infos: $request, id: $request['id']);
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

  public function destroyOutraAnalise(ServicoPmqaResultadoOutraAnalise $outra_analise): array
  {
    Storage::delete($outra_analise->caminho);

    return $this->dataManagement->delete(entity: $this->modelClassOutraAnalise, id: $outra_analise->id);
  }

  public function resultado()
  {
    return [
      'parametros' => ServicoPmqaParametro::all()
    ];
  }
}