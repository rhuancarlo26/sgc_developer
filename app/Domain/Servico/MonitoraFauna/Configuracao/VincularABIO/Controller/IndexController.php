<?php

namespace App\Domain\Servico\MonitoraFauna\Configuracao\VincularABIO\Controller;

use App\Domain\Servico\MonitoraFauna\Configuracao\VincularABIO\Services\VincularABIOService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IndexController extends Controller
{
  public function __construct(private readonly VincularABIOService $vincularABIOService) {}

  public function index(Contrato $contrato, Servicos $servico, Request $request): Response
  {
    $searchParams = $request->all('columns', 'value');

    $response = $this->vincularABIOService->index($servico, $searchParams);

    return Inertia::render('Servico/MonitoraFauna/Configuracao/VincularABIO/Index', [
      'contrato' => $contrato,
      'servico' => $servico->load(['tipo']),
      ...$response
    ]);
  }
}
