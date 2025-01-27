<?php

namespace App\Domain\Servico\MonitoraFauna\Resultado\Controller;

use App\Domain\Servico\MonitoraFauna\Resultado\Services\ResultadoService;
use App\Models\Contrato;
use App\Models\ServicoMonitoraFaunaResultado;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class DestroyController extends Controller
{
  public function __construct(private readonly ResultadoService $resultadoService) {}

  public function index(Contrato $contrato, Servicos $servico, ServicoMonitoraFaunaResultado $resultado): RedirectResponse
  {
    $response = $this->resultadoService->destroy($resultado);

    return to_route('contratos.contratada.servicos.monitora_fauna.resultado.index', ['contrato' => $contrato->id, 'servico' => $servico->id])->with('message', $response['request']);
  }
}
