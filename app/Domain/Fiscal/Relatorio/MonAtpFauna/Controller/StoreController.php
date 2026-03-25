<?php

namespace App\Domain\Fiscal\Relatorio\MonAtpFauna\Controller;

use App\Domain\Fiscal\Relatorio\MonAtpFauna\Services\AtropelamentoService;
use App\Models\Contrato;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StoreController extends Controller
{
  public function __construct(private readonly AtropelamentoService $atropelamentoService) {}

  public function index(Contrato $contrato, Request $request): RedirectResponse
  {
    $response = $this->atropelamentoService->store($request->all());

    return to_route(route: 'fiscal.relatorio.atropelamento.index', parameters: ['contrato' => $contrato->id])
      ->with('message', $response['request']);
  }
}
