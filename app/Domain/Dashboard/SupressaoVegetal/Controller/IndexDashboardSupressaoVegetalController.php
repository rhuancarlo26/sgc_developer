<?php

namespace App\Domain\Dashboard\SupressaoVegetal\Controller;


use App\Domain\Servico\PMQA\app\Utils\ConfigucacaoParecer;
use App\Domain\Servico\PMQA\Configuracao\Parametro\Services\ParametroService;
use App\Domain\Servico\SupressaoVegetacao\Configuracao\PlanoSupressao\Services\PlanoSupressaoService;
use App\Domain\Servico\SupressaoVegetacao\Execucao\Supressao\Services\SupressaoService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IndexDashboardSupressaoVegetalController extends Controller
{
  public function __construct(private readonly SupressaoService $supressao_service, private readonly PlanoSupressaoService $plano_supressao_service) {}

  public function index(Servicos $servicos): Response
  {
    $servicos->load(['area_supressao', 'plano_supressao', 'destinacoes.pilhas']);

    $chartsExec =  $this->supressao_service->graficos_monitora_supressao($servicos);
    $chartsPlano =  $this->plano_supressao_service->graficos_monitora_plano_supressao($servicos);

    $labelsPlano = $chartsPlano['getChartDataBarPorBiomaPlano']['labels'];
    $dataPlano   = $chartsPlano['getChartDataBarPorBiomaPlano']['datasets'][0]['data'];

    $execLabels = $chartsExec['getChartDataBarPorBioma']['labels'];
    $execData   = $chartsExec['getChartDataBarPorBioma']['datasets'][0]['data'];
    $dataExecByBiome = array_combine($execLabels, $execData);

    $diffData = [];
    foreach ($labelsPlano as $i => $bioma) {
      $planoVal = $dataPlano[$i] ?? 0;
      $execVal  = $dataExecByBiome[$bioma] ?? 0;
      $diffData[] = $planoVal - $execVal;
    }

    $totalAreas = $servicos->plano_supressao->count();

    $areaTotalAutorizada = array_sum($dataPlano);

    $areaSuprimida = array_sum($execData);

    $areaNaoSuprimida = $areaTotalAutorizada - $areaSuprimida;

    return Inertia::render('Dashboard/SupressaoVegetal/Index', [
      'contrato' =>   $servicos->contrato,
      'area_supressao' =>   $servicos->area_supressao,
      'destinacoes' =>   $servicos->destinacoes,
      'getChartDataBarPorBioma' => $chartsExec['getChartDataBarPorBioma'],
      'getChartDataPieAreas' => $chartsPlano['getChartDataPieAreas'],
      'getChartDataBarPorBiomaPlano' => $chartsPlano['getChartDataBarPorBiomaPlano'],
      'getChartDataBarPorBiomaDiferenca'  => [
        'labels'   => $labelsPlano,
        'datasets' => [[
          'label'        => 'Área Pend. de Supressão (m²)',
          'data'         => $diffData,
          'backgroundColor' => "#f6c23e",
          'borderRadius'    => 5,
        ]],
      ],
        'totalAreas'             => $totalAreas,
        'areaTotalAutorizada'    => $areaTotalAutorizada,
        'areaSuprimida'          => $areaSuprimida,
        'areaNaoSuprimida'       => $areaNaoSuprimida,
    ]);
  }
}
