<?php

namespace App\Domain\Fiscal\Relatorio\PassagemFauna\Controller;

use App\Domain\Fiscal\Relatorio\PassagemFauna\Services\PassagemService;
use App\Models\Contrato;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IndexController extends Controller
{
  public function __construct(private readonly PassagemService $passagemService) {}

  public function index(Contrato $contrato, Request $request): Response
  {
    $searchParams = $request->all('columns', 'value');

    $response = $this->passagemService->index($contrato, $searchParams);

    return Inertia::render('Fiscal/Relatorio/PassagemFauna/Index', [
      'contrato' => $contrato,
      ...$response
    ]);
  }
}
