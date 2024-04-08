<?php

namespace App\Domain\Contrato\Contratada\Recurso\Veiculo\Controller;

use App\Domain\Contrato\Contratada\Recurso\Equipamento\Services\EquipamentoRecursoService;
use App\Domain\Contrato\Contratada\Recurso\Veiculo\Services\VeiculoRecursoService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StoreDocumentoVeiculoRecursoController extends Controller
{
  public function __construct(private readonly VeiculoRecursoService $veiculoRecursoService)
  {
  }

  public function index(Request $request)
  {
    $response = $this->veiculoRecursoService->salvarDocumentoVeiculo($request->all());

    return to_route('contratos.contratada.recurso.veiculo.create', ['contrato' => $request->contrato_id, 'veiculo' => $request->recurso_veiculo_id])->with('message', $response);
  }
}
