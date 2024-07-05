<?php

namespace App\Domain\Servico\PMQA\Configuracao\VinculacaoPonto\Controller;

use App\Domain\Servico\PMQA\Configuracao\VinculacaoPonto\Requests\StoreRequest;
use App\Domain\Servico\PMQA\Configuracao\VinculacaoPonto\Services\VinculacaoPontoService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;

class StoreController extends Controller
{
  public function __construct(private readonly VinculacaoPontoService $vinculacaoPontoService)
  {
  }

  public function index(Contrato $contrato, Servicos $servico, StoreRequest $request)
  {
    $response = $this->vinculacaoPontoService->store($servico, $request->validated());

    return to_route('contratos.contratada.servicos.pmqa.configuracao.vinculacao_ponto.index', ['contrato' => $contrato->id, 'servico' => $servico->id])->with('message', $response['request']);
  }
}