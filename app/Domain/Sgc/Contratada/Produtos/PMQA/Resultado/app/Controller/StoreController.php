<?php

namespace App\Domain\Sgc\Contratada\Produtos\PMQA\Resultado\app\Controller;

use App\Domain\Sgc\Contratada\Produtos\PMQA\Resultado\app\Requests\StoreRequest;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Resultado\app\Services\ResultadoService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Models\SgcPmqa;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class StoreController extends Controller
{
  public function __construct(private readonly ResultadoService $resultadoService)
  {
  }

  public function index(Contrato $contrato, string $produto, SgcPmqa $pmqa, StoreRequest $request): RedirectResponse
  {
    $response = $this->resultadoService->store($request->validated());

    return to_route('contratos.contratada.sgc.pmqa.resultado.index', ['contrato' => $contrato->id, 'produto' => $produto, 'pmqa' => $pmqa->id])->with('message', $response['request']);
  }
}
