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

    $servicos->load(["passagem_fauna_registros.passagem","contrato"]);

    $charts =  $this->registro_service->graficos_monitora($servicos);

   
     return Inertia::render('Dashboard/PassagemFauna/Index', [
      'contrato' => $servicos->contrato,
      // 'especiesGroup' => $charts["especiesGroup"],
      'chartDataPieAbundancia' => $charts["chartDataPieAbundancia"],
      'chartDataPieDiversidade' => $charts["chartDataPieDiversidade"],
      'chartDataBar' => $charts["chartDataBar"],
      'chartDataBar2' => $charts["chartDataBar2"],
      // 'chartDataLine' => $charts["chartDataLine"],
      'modulos' => $servicos->passagem_fauna_registros
    ]);

    
  }
}
