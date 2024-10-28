<?php

namespace App\Domain\Fiscal\Relatorio\PMQA\Controller;

use App\Domain\Fiscal\Relatorio\PMQA\Services\PMQAService;
use App\Models\Contrato;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IndexController extends Controller
{
  public function __construct(private readonly PMQAService $pmqaService) {}

  public function index(Contrato $contrato, Request $request): Response
  {
    $searchParams = $request->all('columns', 'value');

    $response = $this->pmqaService->index($contrato, $searchParams);

    return Inertia::render('Fiscal/Relatorio/PMQA/Index', [
      'contrato' => $contrato,
      ...$response
    ]);
  }
}
