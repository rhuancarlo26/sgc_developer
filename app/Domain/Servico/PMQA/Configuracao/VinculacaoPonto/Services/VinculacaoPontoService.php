<?php

namespace App\Domain\Servico\PMQA\Configuracao\VinculacaoPonto\Services;

use App\Models\ServicoPmqaListaParametro;
use App\Models\ServicoPmqaListaPonto;
use App\Models\ServicoPmqaParametroLista;
use App\Models\ServicoPmqaPonto;
use App\Models\Servicos;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;

class VinculacaoPontoService extends BaseModelService
{
  use Searchable, Deletable;

  protected string $modelClass = ServicoPmqaParametroLista::class;

  public function index(Servicos $servico, $searchParams): array
  {
    $vinculacoes = $this->searchAllColumns(...$searchParams)
      ->with(['pontos'])
      ->where('servico_id', $servico->id)
      ->has('pontos')
      ->paginate()
      ->appends($searchParams);
    $listas = ServicoPmqaParametroLista::with(['pontos'])->where('servico_id', $servico->id)->get();
    $pontos = ServicoPmqaPonto::with(['vinculado'])->where('servico_id', $servico->id)->get();

    return [
      'vinculacoes' => $vinculacoes,
      'listas' => $listas,
      'pontos' => $pontos
    ];
  }

  public function store(Servicos $servico, array $request)
  {
    $this->updateLista($request);

    $modelLista = ServicoPmqaParametroLista::find($request['lista']['id']);

    $modelLista->pontos()->sync(collect($request['pontos'])->toArray());

    return [
      'request' => [
        'type'    => 'success',
        'content' => 'Registro salvo!',
      ]
    ];
  }

  public function updateLista(array $request): void
  {
    $this->dataManagement->update(entity: $this->modelClass, infos: $request, id: $request['lista']['id']);
  }

  public function deleteVinculacao(ServicoPmqaParametroLista $lista)
  {
    $lista->pontos()->sync(collect([])->toArray());

    return [
      'request' => [
        'type'    => 'success',
        'content' => 'Registro excluido!',
      ]
    ];
  }
}