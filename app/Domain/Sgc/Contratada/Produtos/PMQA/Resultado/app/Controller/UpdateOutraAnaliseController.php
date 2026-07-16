<?php

namespace App\Domain\Sgc\Contratada\Produtos\PMQA\Resultado\app\Controller;

use App\Domain\Servico\PMQA\Resultado\app\Requests\StoreRequest;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Resultado\app\Requests\UpdateOutraAnaliseRequest;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Resultado\app\Services\ResultadoService;
use App\Models\Contrato;
use App\Models\ServicoPmqaResultado;
use App\Models\Servicos;
use App\Models\SgcPmqa;
use App\Models\SgcPmqaResultado;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UpdateOutraAnaliseController extends Controller
{
  public function __construct(private readonly ResultadoService $resultadoService)
  {
  }

  public function index(Contrato $contrato, string $produto, SgcPmqa $pmqa, SgcPmqaResultado $resultado, UpdateOutraAnaliseRequest $request): RedirectResponse
  {
    $response = $this->resultadoService->updateOutraAnalise($request->validated());

    return to_route('contratos.contratada.sgc.pmqa.resultado.resultado', ['contrato' => $contrato->id, 'produto' => $produto, 'pmqa' => $pmqa->id, 'resultado' => $resultado->id])->with('message', $response['request']);
  }
}
