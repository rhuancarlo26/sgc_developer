<?php

namespace App\Domain\Servico\PMQA\Relatorio\app\Controller;

use App\Domain\Servico\PMQA\Relatorio\app\Requests\UpdateRequest;
use App\Domain\Servico\PMQA\Relatorio\app\Services\RelatorioService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class UpdateController extends Controller
{
  public function __construct(private readonly RelatorioService $relatorioService)
  {
  }

  public function index(Contrato $contrato, Servicos $servico, UpdateRequest $request): RedirectResponse
  {
    $response = $this->relatorioService->update(request: $request->validated());

    return to_route('contratos.contratada.servicos.pmqa.relatorio.index', ['contrato' => $contrato->id, 'servico' => $servico->id])->with('message', $response['request']);
  }
}
