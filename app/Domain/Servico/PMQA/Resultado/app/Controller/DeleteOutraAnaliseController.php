<?php

namespace App\Domain\Servico\PMQA\Resultado\app\Controller;

use App\Domain\Servico\PMQA\Resultado\app\Services\ResultadoService;
use App\Models\Contrato;
use App\Models\ServicoPmqaResultado;
use App\Models\ServicoPmqaResultadoOutraAnalise;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class DeleteOutraAnaliseController extends Controller
{
  public function __construct(private readonly ResultadoService $resultadoService)
  {
  }

  public function index(Contrato $contrato, Servicos $servico, ServicoPmqaResultado $resultado, ServicoPmqaResultadoOutraAnalise $outra_analise): RedirectResponse
  {
    $response = $this->resultadoService->destroyOutraAnalise(outra_analise: $outra_analise);

    return to_route('contratos.contratada.servicos.pmqa.resultado.resultado', ['contrato' => $contrato->id, 'servico' => $servico->id, 'resultado' => $resultado->id])->with('message', $response['request']);
  }
}