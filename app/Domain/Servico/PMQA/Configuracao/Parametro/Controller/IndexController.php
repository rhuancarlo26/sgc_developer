<?php

namespace App\Domain\Servico\PMQA\Configuracao\Parametro\Controller;

use App\Domain\Servico\PMQA\Configuracao\Parametro\Services\ParametroService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class IndexController extends Controller
{
  public function __construct(private readonly ParametroService $parametroService)
  {
  }

  public function index(Contrato $contrato, Servicos $servico): Response
  {
    $response = $this->parametroService->index();

    return Inertia::render('Servico/PMQA/Configuracao/Parametro/Index', [
      'contrato' => $contrato,
      'servico' => $servico,
      ...$response
    ]);
  }
}
