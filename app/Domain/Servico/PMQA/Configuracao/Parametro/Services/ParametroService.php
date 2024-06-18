<?php

namespace App\Domain\Servico\PMQA\Configuracao\Parametro\Services;

use App\Models\ServicoPmqaParametro;
use App\Models\ServicoPmqaParametroLista;
use App\Models\Servicos;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;

class ParametroService extends BaseModelService
{
  use Searchable, Deletable;

  protected string $modelClass = ServicoPmqaParametroLista::class;

  public function index(): array
  {
    return [
      'parametros' => ServicoPmqaParametro::orderBy('nome')->get()
    ];
  }

  public function store(Servicos $servico, array $request)
  {
    $modelParametroLista = $this->storeParametroLista([
      'servico_id' => $servico->id,
      'nome' => $request['nome'],
      'medir_iqa' => $request['medir_iqa']
    ]);

    $this->storeListaParametros([]);
  }

  private function storeParametroLista(array $request): ServicoPmqaParametroLista
  {
    $response = $this->dataManagement->create(entity: $this->modelClass, infos: $request);

    return $response['model'];
  }

  private function storeListaParametros(array $request): ServicoPmqaParametroLista
  {
    $response = $this->dataManagement->create(entity: $this->modelClass, infos: $request);

    return $response['model'];
  }
}
