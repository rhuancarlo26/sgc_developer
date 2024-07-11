<?php

namespace App\Domain\Servico\PMQA\Relatorio\app\Controller;

use App\Domain\Servico\PMQA\Relatorio\app\Requests\StoreRequest;
use App\Domain\Servico\PMQA\Relatorio\app\Services\RelatorioService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StoreController extends Controller
{
  public function __construct(private readonly RelatorioService $relatorioService)
  {
  }

  public function index(Contrato $contrato, Servicos $servico, StoreRequest $request): RedirectResponse
  {
    dd($request->validated());
    $post = [
      ...$request->validated(),
      'resultado_id' => $request->resultado['id']
    ];
    dd($post);
    $response = $this->relatorioService->store(request: $request->validated());

    return to_route('contratos.contratada.servicos.pmqa.resultado.index', ['contrato' => $contrato->id, 'servico' => $servico->id])->with('message', $response['request']);
  }
}