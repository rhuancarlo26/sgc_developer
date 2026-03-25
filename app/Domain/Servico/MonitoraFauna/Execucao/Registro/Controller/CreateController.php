<?php

namespace App\Domain\Servico\MonitoraFauna\Execucao\Registro\Controller;

use App\Domain\Servico\MonitoraFauna\Execucao\Registro\Services\RegistroService;
use App\Models\Contrato;
use App\Models\ServicoMonitoraFaunaExecRegistro;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class CreateController extends Controller
{
  public function __construct(private readonly RegistroService $registroService) {}

  public function index(Contrato $contrato, Servicos $servico, ServicoMonitoraFaunaExecRegistro $registro): Response
  {
    $response = $this->registroService->create($servico, $registro);

    return Inertia::render('Servico/MonitoraFauna/Execucao/Registro/Form', [
      'contrato' => $contrato,
      'servico' => $servico->load(['tipo']),
      ...$response
    ]);
  }
}
