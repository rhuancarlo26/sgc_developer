<?php

namespace App\Domain\Dashboard\PMQA\Controller;

use App\Domain\Servico\PMQA\Resultado\app\Services\ResultadoService;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Inertia\Inertia;

class IndexDashboardPMQAController extends Controller
{

  public function __construct(private readonly ResultadoService $resultadoService)
  {
  }

  public function index(Servicos $servicos)
  {
    // Carrega os relacionamentos necessários
    $servicos->load([
      'contrato',
      'pontos',
      'pmqaResultados',
      'campanhas.campanha_pontos.ponto',
      'campanhas.campanha_pontos.coleta',
      'campanhas.campanha_pontos.coleta.arquivos',
    ]);

    // Iterar sobre os resultados corretamente
    $responses = [];
    foreach ($servicos->pmqaResultados as $resultado) {
      $responses[] = $this->resultadoService->resultado($resultado);
    }
    return Inertia::render('Dashboard/PMQA/Index', [
      'contrato' => $servicos->contrato,
      'pontos' => $servicos->pontos,
      'resultados' => $servicos->pmqaResultados,
      'servico' => $servicos,
      'campanhas' => $servicos->campanhas,
      'responses' => $responses
    ]);
  }
}
