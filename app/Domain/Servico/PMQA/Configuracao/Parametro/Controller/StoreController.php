<?php

namespace App\Domain\Servico\PMQA\Configuracao\Parametro\Controller;

use App\Domain\Servico\PMQA\Configuracao\Parametro\Requests\StoreRequest;
use App\Domain\Servico\PMQA\Configuracao\Parametro\Services\ParametroService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;

class StoreController extends Controller
{
  public function __construct(private readonly ParametroService $parametroService)
  {
  }

  public function index(Contrato $contrato, Servicos $servico, StoreRequest $request)
  {
    $response = $this->parametroService->store($servico, $request->validated());

    return to_route('contratos.contratada.servicos.pmqa.configuracao.parametro.index', ['contrato' => $contrato->id, 'servico' => $servico->id])->with('message', $response);
  }
}
