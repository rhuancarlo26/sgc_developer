<?php

namespace App\Domain\Dashboard\AfugentamentoFauna\Controller;

use App\Domain\Servico\AfugentamentoResgateFauna\Execucao\Registros\Service\RegistrosService;
use App\Domain\Servico\PMQA\app\Utils\ConfigucacaoParecer;
use App\Domain\Servico\PMQA\Configuracao\Parametro\Services\ParametroService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IndexDashboardAfugentamentoFaunaController extends Controller
{
  public function __construct(private readonly RegistrosService $registros_service) {}

  public function index(Servicos $servicos): Response
  {

    $servicos->load(['monitora_afugentamento_fauna']);

    $charts =  $this->registros_service->graficos_monitora_afugentamento($servicos);
    
    return Inertia::render('Dashboard/AfugentamentoFauna/Index',
    [
      'totalRegistros' => $charts["totalRegistros"],
      'monitora_afugentamento_faunas' => $servicos->monitora_afugentamento_fauna,
      'chartDataPieAbundancia' => $charts["chartDataPieAbundancia"],
      'chartDataPieDiversidade' => $charts["chartDataPieDiversidade"],
      'getChartDataPieTipoRegistro' => $charts["getChartDataPieTipoRegistro"],
      'getChartDataPieFormaRegistro' => $charts["getChartDataPieFormaRegistro"],
      'getChartDataPieFormaRegistroGrafico' => $charts["getChartDataPieFormaRegistroGrafico"],
      'chartDataBar2' => $charts["chartDataBar2"]
    ]);
  }
}
