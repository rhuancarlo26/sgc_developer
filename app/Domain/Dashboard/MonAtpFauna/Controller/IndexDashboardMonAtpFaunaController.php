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
    $servicos->load(['monitora_atp_fauna','monitora_atp_fauna.campanhas']);

    $campanhas = $servicos->monitora_atp_fauna
    ->pluck('campanhas')   
    ->flatten()             
    ->unique('id')          
    ->values();


    $charts =  $this->registro_service->graficos_monitora_atp($servicos);

    return Inertia::render('Dashboard/MonAtpFauna/Index', [
      'at_fauna_execucao_registros' => $servicos->monitora_atp_fauna,
      'campanhas' => $campanhas,
      'chartDataPieAbundancia' => $charts["chartDataPieAbundancia"],
      'chartDataPieDiversidade' => $charts["chartDataPieDiversidade"],
      'getChartDataBarCampanhas' => $charts["getChartDataBarCampanhas"],
      'chartDataBar2' => $charts["chartDataBar2"],
      'especiesGroup' => $charts["especiesGroup"]
    ]);
  }
}
