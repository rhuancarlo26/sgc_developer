<?php

namespace App\Domain\Dashboard\PassagemFauna\Controller;

use App\Domain\Servico\PassagemFauna\Execucao\Registro\Services\RegistroService;
use App\Shared\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Servicos;


class IndexDashboardPassagemFaunaController extends Controller
{
  public function __construct(private readonly RegistroService $registro_service) {}


  public function index(Servicos $servicos): Response
  {

    $servicos->load(["passagem_fauna_registros.passagem", "contrato"]);

    $charts =  $this->registro_service->graficos_monitora($servicos);


    $passagens = $servicos
      ->passagem_fauna_registros
      ->pluck('passagem')
      ->unique('id')
      ->values();

    return Inertia::render('Dashboard/PassagemFauna/Index', [
      'contrato' => $servicos->contrato,
      'chartDataPieAbundancia' => $charts["chartDataPieAbundancia"],
      'chartDataPieDiversidade' => $charts["chartDataPieDiversidade"],
      'chartDataBar' => $charts["chartDataBar"],
      'chartDataBar2' => $charts["chartDataBar2"],
      'getChartDataBarEspecie' => $charts["getChartDataBarEspecie"],
      'especiesGroup' => $charts["especiesGroup"],
      'modulos' => $servicos->passagem_fauna_registros,
      'passagem' => $passagens,
    ]);
  }
}
