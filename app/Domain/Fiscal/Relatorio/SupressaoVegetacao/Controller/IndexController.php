<?php

namespace App\Domain\Fiscal\Relatorio\SupressaoVegetacao\Controller;

use App\Domain\Fiscal\Relatorio\SupressaoVegetacao\Services\SupressaoService;
use App\Models\Contrato;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IndexController extends Controller
{
  public function __construct(private readonly SupressaoService $supressaoService) {}

  public function index(Contrato $contrato, Request $request): Response
  {
    $searchParams = $request->all('columns', 'value');

    $response = $this->supressaoService->index($contrato, $searchParams);

    return Inertia::render('Fiscal/Relatorio/SupressaoVegetacao/Index', [
      'contrato' => $contrato,
      ...$response
    ]);
  }
}
