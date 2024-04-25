<?php

namespace App\Domain\Servico\app\Services;

use App\Models\Licenca;
use App\Models\RecursoEquipamento;
use App\Models\RecursoRh;
use App\Models\RecursoVeiculo;
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
        ->with([
          'tipo',
          'tema',
          'status',
          'rhs',
          'veiculos',
          'veiculos.codigo',
          'equipamentos',
          'condicionantes',
          'condicionantes.licenca'
        ])
        ->where('contrato_id', $contrato->id)
        ->paginate()
        ->appends($searchParams)
    ];
  }

  public function createServicos($contrato, $servico)
  {
    $tipos = ServicoTipo::all();
    $temas = ServicoTema::all();
    $rhs = RecursoRh::where('contrato_id', $contrato->id)->get();
    $veiculos = RecursoVeiculo::with(['codigo'])->where('contrato_id', $contrato->id)->get();
    $equipamentos = RecursoEquipamento::where('contrato_id', $contrato->id)->get();
    $licencasLi = Licenca::select(['id', 'numero_licenca'])
      ->with([
        'condicionantes'
      ])
      ->where('tipo_id', 6)
      ->get();

    if ($servico) {
      $servico->load([
        'tipo',
        'tema',
        'rhs',
        'veiculos',
        'veiculos.codigo',
        'equipamentos',
        'condicionantes',
        'condicionantes.licenca'
      ]);
    }

    return [
      'tipos' => $tipos,
      'temas' => $temas,
      'licencasLi' => $licencasLi,
      'rhs' => $rhs,
      'veiculos' => $veiculos,
      'equipamentos' => $equipamentos,
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
