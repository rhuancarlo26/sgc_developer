<?php

namespace App\Domain\Sgc\Contratada\Produtos\PMQA\Resultado\app\Controller;

use App\Domain\Sgc\Contratada\Produtos\PMQA\Resultado\app\Requests\UpdateRequest;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Resultado\app\Services\ResultadoService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Models\SgcPmqa;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class UpdateController extends Controller
{
  public function __construct(private readonly ResultadoService $resultadoService)
  {
  }

  public function index(Contrato $contrato, SgcPmqa $pmqa, UpdateRequest $request): RedirectResponse
  {
    $response = $this->resultadoService->update($request->validated());

    return to_route('contratos.contratada.servicos.pmqa.resultado.index', ['contrato' => $contrato->id, 'pmqa' => $pmqa->id])->with('message', $response['request']);
  }
}
