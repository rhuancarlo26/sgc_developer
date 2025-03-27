<?php

namespace App\Domain\Servico\MonitoraFauna\Execucao\Registro\Controller;

use App\Domain\Servico\MonitoraFauna\Configuracao\VincularABIO\Services\VincularABIOService;
use App\Domain\Servico\MonitoraFauna\Execucao\Campanha\Services\CampanhaService;
use App\Domain\Servico\MonitoraFauna\Execucao\Registro\Services\RegistroService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IndexController extends Controller
{
  public function __construct(private readonly RegistroService $registroService) {}

  public function index(Contrato $contrato, Servicos $servico, Request $request): Response
  {
    $searchParams = $request->all('columns', 'value');

    $response = $this->registroService->index($servico, $searchParams);

    return Inertia::render('Servico/MonitoraFauna/Execucao/Registro/Index', [
      'contrato' => $contrato,
      'servico' => $servico->load(['tipo']),
      ...$response
    ]);
  }
}
