<?php

namespace App\Domain\Servico\PMQA\Resultado\app\Controller;

use App\Domain\Servico\PMQA\Resultado\app\Requests\StoreRequest;
use App\Domain\Servico\PMQA\Resultado\app\Services\ResultadoService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class StoreController extends Controller
{
  public function __construct(private readonly ResultadoService $resultadoService)
  {
  }

  public function index(Contrato $contrato, Servicos $servico, StoreRequest $request): RedirectResponse
  {
    $response = $this->resultadoService->store($request->validated());

    return to_route('contratos.contratada.servicos.pmqa.resultado.index', ['contrato' => $contrato->id, 'servico' => $servico->id])->with('message', $response['request']);
  }
}
