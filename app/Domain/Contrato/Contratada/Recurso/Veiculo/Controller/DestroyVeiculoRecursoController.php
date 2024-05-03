<?php

namespace App\Domain\Contrato\Contratada\Recurso\Veiculo\Controller;

use App\Domain\Contrato\Contratada\Recurso\Veiculo\Services\VeiculoRecursoService;
use App\Models\RecursoEquipamento;
use App\Models\RecursoVeiculo;
use App\Shared\Http\Controllers\Controller;

class DestroyVeiculoRecursoController extends Controller
{
  public function __construct(private readonly VeiculoRecursoService $veiculoRecursoService)
  {
  }

  public function index(RecursoVeiculo $veiculo)
  {
    try {
    $response = $this->veiculoRecursoService->destroyVeiculo($veiculo);

    $this->veiculoRecursoService->delete($veiculo);

    return to_route('contratos.contratada.recurso.veiculo.index', ['contrato' => $veiculo->contrato_id])->with('message', $response);
    } catch (\Exception $e) {
      return to_route('contratos.contratada.recurso.veiculo.index', ['contrato' => $veiculo->contrato_id])->with('message', $response);
    }
  }
}