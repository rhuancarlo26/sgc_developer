<?php

namespace App\Domain\Servico\PMQA\Configuracao\VinculacaoPonto\Controller;

use App\Domain\Servico\PMQA\Configuracao\Parametro\Services\ParametroService;
use App\Domain\Servico\PMQA\Configuracao\VinculacaoPonto\Services\VinculacaoPontoService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IndexController extends Controller
{
  public function __construct(private readonly VinculacaoPontoService $vinculacaoPontoService)
  {
  }

  public function index(Contrato $contrato, Servicos $servico, Request $request): Response
  {
    $searchParams = $request->all('columns', 'value');

    $response = $this->vinculacaoPontoService->index($servico, $searchParams);

    return Inertia::render('Servico/PMQA/Configuracao/VinculacaoPonto/Index', [
      'contrato' => $contrato,
      'servico' => $servico->load(['tipo']),
      ...$response
    ]);
  }
}
