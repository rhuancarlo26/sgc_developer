<?php

namespace App\Domain\Dashboard\MonitoraFauna\Controller;

use App\Domain\Servico\MonitoraFauna\Execucao\Registro\Services\RegistroService;
use App\Shared\Http\Controllers\Controller;
use App\Models\Servicos;
use Inertia\Inertia;
use Inertia\Response;

class IndexDashboardMonitoraFaunaController extends Controller
{
  public function __construct(private readonly RegistroService $registro_service) {}


  public function index(Servicos $servicos): Response
  {

    $servicos->load(["monitora_fauna_modulos.armadilhas"]);

    $charts =  $this->registro_service->graficos_monitora($servicos);

    return Inertia::render('Dashboard/MonitoraFauna/Index', [
      'especiesGroup' => $charts["especiesGroup"],
      'chartDataPieAbundancia' => $charts["chartDataPieAbundancia"],
      'chartDataPieDiversidade' => $charts["chartDataPieDiversidade"],
      'chartDataBar' => $charts["chartDataBar"],
      'chartDataBar2' => $charts["chartDataBar2"],
      'chartDataLine' => $charts["chartDataLine"],
      'modulos' => $servicos->monitora_fauna_modulos
    ]);
  }
}
