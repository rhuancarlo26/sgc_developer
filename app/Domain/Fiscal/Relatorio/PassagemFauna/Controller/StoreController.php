<?php

namespace App\Domain\Fiscal\Relatorio\PassagemFauna\Controller;

use App\Domain\Fiscal\Relatorio\ContOcorrencia\Services\ContOcorrenciaService;
use App\Domain\Fiscal\Relatorio\PassagemFauna\Services\PassagemService;
use App\Models\Contrato;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StoreController extends Controller
{
  public function __construct(private readonly PassagemService $passagemService) {}

  public function index(Contrato $contrato, Request $request): RedirectResponse
  {
    $response = $this->passagemService->store($request->all());

    return to_route(route: 'fiscal.relatorio.passagem_fauna.index', parameters: ['contrato' => $contrato->id])
      ->with('message', $response['request']);
  }
}
