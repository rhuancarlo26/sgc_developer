<?php

namespace App\Domain\Contrato\Contratada\Recurso\Veiculo\Controller;

use App\Domain\Contrato\Contratada\Recurso\Veiculo\Services\VeiculoRecursoService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StoreVeiculoRecursoController extends Controller
{
  public function __construct(private readonly VeiculoRecursoService $veiculoRecursoService)
  {
  }

  public function index(Request $request)
  {
    $response = $this->veiculoRecursoService->salvarVeiculo($request->all());

    return to_route('contratos.contratada.recurso.veiculo.create', ['contrato' => $request->contrato_id, 'veiculo' => $response['veiculo']['id']])->with('message', $response['request']);
  }
}
