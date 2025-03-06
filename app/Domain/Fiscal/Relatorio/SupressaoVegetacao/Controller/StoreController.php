<?php

namespace App\Domain\Fiscal\Relatorio\SupressaoVegetacao\Controller;

use App\Domain\Fiscal\Relatorio\SupressaoVegetacao\Services\SupressaoService;
use App\Models\Contrato;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StoreController extends Controller
{
  public function __construct(private readonly SupressaoService $supressaoService) {}

  public function index(Contrato $contrato, Request $request): RedirectResponse
  {
    $response = $this->supressaoService->store($request->all());

    return to_route(route: 'fiscal.relatorio.supressao_vegetacao.index', parameters: ['contrato' => $contrato->id])
      ->with('message', $response['request']);
  }
}
