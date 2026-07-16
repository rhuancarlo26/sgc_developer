<?php

namespace App\Domain\Sgc\Contratada\Produtos\PMQA\Resultado\app\Controller;

use App\Domain\Sgc\Contratada\Produtos\PMQA\Resultado\app\Services\ResultadoService;
use App\Models\Contrato;
use App\Models\SgcPmqa;
use App\Models\SgcPmqaResultado;
use App\Models\SgcPmqaResultadoOutraAnalise;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class DeleteOutraAnaliseController extends Controller
{
  public function __construct(private readonly ResultadoService $resultadoService)
  {
  }

  public function index(Contrato $contrato, string $produto, SgcPmqa $pmqa, SgcPmqaResultado $resultado, SgcPmqaResultadoOutraAnalise $outra_analise): RedirectResponse
  {
    $response = $this->resultadoService->destroyOutraAnalise(outra_analise: $outra_analise);

    return to_route('contratos.contratada.sgc.pmqa.resultado.resultado', ['contrato' => $contrato->id, 'produto' => $produto, 'pmqa' => $pmqa->id, 'resultado' => $resultado->id])->with('message', $response['request']);
  }
}
