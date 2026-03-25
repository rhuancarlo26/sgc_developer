<?php

namespace App\Domain\Fiscal\Relatorio\ContOcorrencia\Controller;

use App\Domain\Fiscal\Relatorio\ContOcorrencia\Services\ContOcorrenciaService;
use App\Models\Contrato;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StoreController extends Controller
{
  public function __construct(private readonly ContOcorrenciaService $contOcorrenciaService) {}

  public function index(Contrato $contrato, Request $request): RedirectResponse
  {
    $response = $this->contOcorrenciaService->store($request->all());

    return to_route(route: 'fiscal.relatorio.cont_ocorrencia.index', parameters: ['contrato' => $contrato->id])
      ->with('message', $response['request']);
  }
}
