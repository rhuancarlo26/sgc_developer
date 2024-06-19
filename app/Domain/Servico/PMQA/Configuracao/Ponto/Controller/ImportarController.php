<?php

namespace App\Domain\Servico\PMQA\Configuracao\Ponto\Controller;

use App\Domain\Servico\PMQA\Configuracao\Ponto\Requests\ImportarRequest;
use App\Domain\Servico\PMQA\Configuracao\Ponto\Services\PontoService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class ImportarController extends Controller
{
  public function __construct(private readonly PontoService $pontoService)
  {
  }

  public function index(Contrato $contrato, Servicos $servico, ImportarRequest $request): RedirectResponse
  {
    $response = $this->pontoService->importar($servico, $request->validated('arquivo'));

    return to_route('contratos.contratada.servicos.pmqa.configuracao.ponto.index', ['contrato' => $contrato->id, 'servico' => $servico->id])->with('message', $response);
  }
}
