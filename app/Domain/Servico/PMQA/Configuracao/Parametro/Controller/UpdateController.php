<?php

namespace App\Domain\Servico\PMQA\Configuracao\Parametro\Controller;

use App\Domain\Servico\PMQA\Configuracao\Parametro\Requests\StoreRequest;
use App\Domain\Servico\PMQA\Configuracao\Parametro\Requests\UpdateRequest;
use App\Domain\Servico\PMQA\Configuracao\Parametro\Services\ParametroService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UpdateController extends Controller
{
  public function __construct(private readonly ParametroService $parametroService)
  {
  }

  public function index(Contrato $contrato, Servicos $servico, UpdateRequest $request)
  {
    $response = $this->parametroService->update($servico, $request->validated());

    return to_route('contratos.contratada.servicos.pmqa.configuracao.parametro.index', ['contrato' => $contrato->id, 'servico' => $servico->id])->with('message', $response);
  }
}