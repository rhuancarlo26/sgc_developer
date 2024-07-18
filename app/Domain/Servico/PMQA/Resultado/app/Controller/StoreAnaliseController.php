<?php

namespace App\Domain\Servico\PMQA\Resultado\app\Controller;

use App\Domain\Servico\PMQA\Resultado\app\Requests\StoreAnaliseRequest;
use App\Domain\Servico\PMQA\Resultado\app\Services\ResultadoService;
use App\Models\Contrato;
use App\Models\ServicoPmqaResultado;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StoreAnaliseController extends Controller
{
  public function __construct(private readonly ResultadoService $resultadoService)
  {
  }

  public function index(Contrato $contrato, Servicos $servico, ServicoPmqaResultado $resultado, StoreAnaliseRequest $request): RedirectResponse
  {
    $analisesFiltradas = array_filter($request->validated('analises'), function ($value) {
      return !is_null($value);
    });

    $post = [
      'resultado_id' => $request->validated('resultado_id'),
      'analises' => $analisesFiltradas
    ];

    $response = $this->resultadoService->storeAnalises($request->validated());

    return to_route('contratos.contratada.servicos.pmqa.resultado.resultado', ['contrato' => $contrato->id, 'servico' => $servico->id, 'resultado' => $resultado->id])->with('message', $response['request']);
  }
}