<?php

namespace App\Domain\Contrato\Contratada\Recurso\Veiculo\Controller;

use App\Domain\Contrato\Contratada\Recurso\Veiculo\Requests\StoreVeiculoRecursoRequest;
use App\Domain\Contrato\Contratada\Recurso\Veiculo\Services\VeiculoRecursoService;
use App\Shared\Http\Controllers\Controller;

class StoreVeiculoRecursoController extends Controller
{
  public function __construct(private readonly VeiculoRecursoService $veiculoRecursoService)
  {
  }

  public function index(StoreVeiculoRecursoRequest $request)
  {
    $response = $this->veiculoRecursoService->salvarVeiculo($request->validated());

    return to_route('contratos.contratada.recurso.veiculo.create', ['contrato' => $request->contrato_id, 'veiculo' => $response['veiculo']['id']])->with('message', $response['request']);
  }
}
