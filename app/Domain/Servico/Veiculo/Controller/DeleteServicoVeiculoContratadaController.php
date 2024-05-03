<?php

namespace App\Domain\Servico\Veiculo\Controller;

use App\Domain\Servico\Veiculo\Services\ServicoVeiculoService;
use App\Models\RecursoVeiculo;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DeleteServicoVeiculoContratadaController extends Controller
{
  public function __construct(private readonly ServicoVeiculoService $servicoVeiculoService)
  {
  }

  public function index(RecursoVeiculo $veiculo, Request $request)
  {
    $response = $this->servicoVeiculoService->deleteVeiculo($veiculo, $request->all());

    return to_route('contratos.contratada.servicos.create', ['contrato' => $request->contrato_id, 'servico' => $request->servico_id])->with('message', $response['request']);
  }
}
