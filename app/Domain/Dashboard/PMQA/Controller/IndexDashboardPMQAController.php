<?php

namespace App\Domain\Dashboard\PMQA\Controller;

use App\Domain\Servico\ContOcorrencia\Resultado\Services\ResultadoService; // Certifique-se de importar a classe correta
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class IndexDashboardPMQAController extends Controller
{
  

  public function index(Servicos $servicos)
  {
    // Carrega os relacionamentos necessários
    $servicos->load(['contrato', 'pontos', 'pmqaResultados']);

    return Inertia::render('Dashboard/PMQA/Index', [
      'contrato' => $servicos->contrato,
      'pontos' => $servicos->pontos,
      'resultados' => $servicos->pmqaResultados,
      'servico' => $servicos,
    ]);
  }
}
