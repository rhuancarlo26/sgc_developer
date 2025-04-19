<?php

namespace App\Domain\Dashboard\SupervisaoAmbiental\Controller;

use App\Domain\Servico\SupervisaoAmbiental\Execucao\Registros\Service\RegistrosService;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class IndexDashboardSupervisaoAmbientalController extends Controller
{
  public function __construct(private readonly RegistrosService $registros_service) {}

  public function index(Servicos $servicos): Response
  {

    $servicos->load(['monitora_supervisao_ambiental']);

    $charts =  $this->registros_service->graficos_monitora_supervisao($servicos);


    return Inertia::render('Dashboard/SupervisaoAmbiental/Index', [
      'lotes' =>      $charts['lotes'],
      'registros' =>  $charts['registros'],
      'monitora_supervisao_ambientais' => $servicos->monitora_supervisao_ambiental,
      'getChartDataPieLocalExecOcorrencia' => $charts['getChartDataPieLocalExecOcorrencia'],
      'getChartDataPieTipoRncDiretoExecOcorrencia' => $charts['getChartDataPieTipoRncDiretoExecOcorrencia'],
      'getChartDataPieIntensidadeExecOcorrencia' => $charts['getChartDataPieIntensidadeExecOcorrencia'],
      'getChartDataBarExecOcorrencia' => $charts['getChartDataBarExecOcorrencia'],
      'getChartDataBarLoteIntensidadeExecOcorrencia' => $charts['getChartDataBarLoteIntensidadeExecOcorrencia'],

    ]);
  }
}
