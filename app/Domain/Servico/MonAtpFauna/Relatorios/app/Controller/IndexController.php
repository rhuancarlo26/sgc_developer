<?php

namespace App\Domain\Servico\MonAtpFauna\Relatorios\app\Controller;

use App\Domain\Servico\MonAtpFauna\Relatorios\app\Services\RelatoriosService;
use App\Domain\Servico\PMQA\Configuracao\Parametro\Services\ParametroService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IndexController extends Controller
{
  public function __construct(private readonly RelatoriosService $relatoriosService)
  {
  }

  public function index(Contrato $contrato, Servicos $servico, Request $request): Response
  {
    $searchParams = $request->all('columns', 'value');

    $response = $this->relatoriosService->index($servico, $searchParams);

    return Inertia::render('Servico/MonAtpFauna/Relatorios/Index', [
      'contrato' => $contrato,
      'servico' => $servico->load(['tipo', 'pmqa_config_lista_parecer']),
      ...$response
    ]);
  }
}
