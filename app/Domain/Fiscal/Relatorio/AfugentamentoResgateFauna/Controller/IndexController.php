<?php

namespace App\Domain\Fiscal\Relatorio\AfugentamentoResgateFauna\Controller;

use App\Domain\Fiscal\Relatorio\AfugentamentoResgateFauna\Services\AfugentamentoService;
use App\Domain\Fiscal\Relatorio\ContOcorrencia\Services\ContOcorrenciaService;
use App\Models\Contrato;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IndexController extends Controller
{
  public function __construct(private readonly AfugentamentoService $afugentamentoService) {}

  public function index(Contrato $contrato, Request $request): Response
  {
    $searchParams = $request->all('columns', 'value');

    $response = $this->afugentamentoService->index($contrato, $searchParams);

    return Inertia::render('Fiscal/Relatorio/AfugentamentoResgateFauna/Index', [
      'contrato' => $contrato,
      ...$response
    ]);
  }
}
