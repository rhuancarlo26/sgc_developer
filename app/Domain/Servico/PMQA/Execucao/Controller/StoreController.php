<?php

namespace App\Domain\Servico\PMQA\Execucao\Controller;

use App\Domain\Servico\PMQA\Execucao\Requests\StoreRequest;
use App\Domain\Servico\PMQA\Execucao\Services\CampanhaService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class StoreController extends Controller
{
  public function __construct(private readonly CampanhaService $campanhaService)
  {
  }

  public function index(Contrato $contrato, Servicos $servico, StoreRequest $request): RedirectResponse
  {
    $response = $this->campanhaService->store($request->validated());

    return to_route('contratos.contratada.servicos.pmqa.execucao.index', ['contrato' => $contrato->id, 'servico' => $servico->id])->with('message', $response['request']);
  }
}