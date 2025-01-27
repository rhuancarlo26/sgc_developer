<?php

namespace App\Domain\Servico\MonitoraFauna\Resultado\Controller;

use App\Domain\Servico\MonitoraFauna\Resultado\Services\ResultadoService;
use App\Models\Contrato;
use App\Models\ServicoMonitoraFaunaResultado;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class ResultadoController extends Controller
{
  public function __construct(private readonly ResultadoService $resultadoService) {}

  public function index(Contrato $contrato, Servicos $servico, ServicoMonitoraFaunaResultado $resultado): Response
  {
    $response = $this->resultadoService->resultado($resultado);

    return Inertia::render('Servico/MonitoraFauna/Resultado/Resultado', [
      'contrato' => $contrato,
      'servico' => $servico->load(['tipo']),
      'resutlado' => $resultado,
      ...$response
    ]);
  }
}
