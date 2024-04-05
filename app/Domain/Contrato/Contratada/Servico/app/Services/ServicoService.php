<?php

namespace App\Domain\Contrato\Contratada\Servico\app\Services;

use App\Models\Servicos;
use App\Models\ServicoTema;
use App\Models\ServicoTipo;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;

class ServicoService extends BaseModelService
{
  use Searchable, Deletable;

  protected string $modelClass = Servicos::class;

  public function listarServicos($contrato, $searchParams)
  {
    return [
      'servicos' => $this->search(...$searchParams)
        ->with(['tipo', 'tema', 'status'])
        ->where('contrato_id', $contrato->id)
        ->paginate()
        ->appends($searchParams)
    ];
  }

  public function createServicos($servico)
  {
    $tipos = ServicoTipo::all();
    $temas = ServicoTema::all();

    if ($servico) {
      $servico->load([
        'tipo',
        'tema'
      ]);
    }

    return [
      'tipos' => $tipos,
      'temas' => $temas,
      'servico' => $servico
    ];
  }

  public function storeServico($request)
  {
    $response = $this->dataManagement->create(entity: $this->modelClass, infos: $request);

    return [
      'servico' => $response['model']['id'],
      'request' => $response['request']
    ];
  }

  public function updateServico($request)
  {
    $response = $this->dataManagement->update(entity: $this->modelClass, infos: $request, id: $request['id']);

    return [
      'request' => $response['request']
    ];
  }
}