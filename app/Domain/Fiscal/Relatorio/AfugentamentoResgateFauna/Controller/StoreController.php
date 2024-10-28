<?php

namespace App\Domain\Fiscal\Relatorio\AfugentamentoResgateFauna\Controller;

use App\Domain\Fiscal\Relatorio\AfugentamentoResgateFauna\Services\AfugentamentoService;
use App\Domain\Fiscal\Relatorio\ContOcorrencia\Services\ContOcorrenciaService;
use App\Models\Contrato;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StoreController extends Controller
{
  public function __construct(private readonly AfugentamentoService $afugentamentoService) {}

  public function index(Contrato $contrato, Request $request): RedirectResponse
  {
    $response = $this->afugentamentoService->store($request->all());

    return to_route(route: 'fiscal.relatorio.afugentamento.index', parameters: ['contrato' => $contrato->id])
      ->with('message', $response['request']);
  }
}
