<?php

namespace App\Domain\Fiscal\Relatorio\ContOcorrencia\Controller;

use App\Domain\Fiscal\Relatorio\ContOcorrencia\Services\ContOcorrenciaService;
use App\Models\Contrato;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IndexController extends Controller
{
  public function __construct(private readonly ContOcorrenciaService $contOcorrenciaService) {}

  public function index(Contrato $contrato, Request $request): Response
  {
    $searchParams = $request->all('columns', 'value');

    $response = $this->contOcorrenciaService->index($contrato, $searchParams);

    return Inertia::render('Fiscal/Relatorio/ContOcorrencia/Index', [
      'contrato' => $contrato,
      ...$response
    ]);
  }
}
