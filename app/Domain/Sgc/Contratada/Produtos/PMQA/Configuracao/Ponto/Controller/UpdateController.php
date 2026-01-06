<?php

namespace App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\Ponto\Controller;

use App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\Ponto\Requests\UpdateRequest;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\Ponto\Services\PontoService;
use App\Models\Contrato;
use App\Models\SgcPmqaCampanha;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class UpdateController extends Controller
{
  public function __construct(private readonly PontoService $pontoService)
  {
  }

  public function index(Contrato $contrato, SgcPmqaCampanha $campanha, UpdateRequest $updateRequest): RedirectResponse
  {
    $data = $updateRequest->validated();

    // Garanta que campanha_id esteja presente — tanto para create quanto update
    $data['campanha_id'] = $campanha->id;

    $response = $this->pontoService->updateParaCampanha($data);

    return to_route('contratos.contratada.servicos.pmqa.configuracao.ponto.index', [
      'contrato' => $contrato->id,
      'campanha' => $campanha->id
    ])->with('message', $response['request'] ?? $response);
  }
}
