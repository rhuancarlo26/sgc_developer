<?php

namespace App\Domain\Dashboard\MonAtpFauna\Controller;

use App\Domain\Servico\MonAtpFauna\Execucao\Registros\app\Services\RegistrosService;
use App\Shared\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Servicos;


class IndexDashboardMonAtpFaunaController extends Controller
{
  public function __construct(private readonly RegistrosService $registro_service) {}

  public function index(Servicos $servicos): Response
  {
    $servicos->load(['monitora_atp_fauna']);

    $charts =  $this->registro_service->graficos_monitora_atp($servicos);


    return Inertia::render('Dashboard/MonAtpFauna/Index', [
      'at_fauna_execucao_registros' => $servicos->monitora_atp_fauna,
      'chartDataPieAbundancia' => $charts["chartDataPieAbundancia"],
      'chartDataPieDiversidade' => $charts["chartDataPieDiversidade"],
      'chartDataBar2' => $charts["chartDataBar2"],
    ]);
  }
}
