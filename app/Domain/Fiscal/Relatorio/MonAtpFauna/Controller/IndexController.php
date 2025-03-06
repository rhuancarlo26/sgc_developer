<?php

namespace App\Domain\Fiscal\Relatorio\MonAtpFauna\Controller;

use App\Domain\Fiscal\Relatorio\MonAtpFauna\Services\AtropelamentoService;
use App\Models\Contrato;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IndexController extends Controller
{
  public function __construct(private readonly AtropelamentoService $atropelamentoService) {}

  public function index(Contrato $contrato, Request $request): Response
  {
    $searchParams = $request->all('columns', 'value');

    $response = $this->atropelamentoService->index($contrato, $searchParams);

    return Inertia::render('Fiscal/Relatorio/MontAtpFauna/Index', [
      'contrato' => $contrato,
      ...$response
    ]);
  }
}
