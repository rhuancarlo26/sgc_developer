<?php

namespace App\Domain\Servico\PMQA\Resultado\app\Controller;

use App\Domain\Servico\PMQA\Resultado\app\Services\ResultadoService;
use App\Models\Contrato;
use App\Models\ServicoPmqaResultado;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ResultadoController extends Controller
{
  public function __construct(private readonly ResultadoService $resultadoService)
  {
  }

  public function index(Contrato $contrato, Servicos $servico, ServicoPmqaResultado $resultado): Response
  {
    $response = $this->resultadoService->resultado();

    return Inertia::render('Servico/PMQA/Resultado/Resultado', [
      'contrato' => $contrato,
      'servico' => $servico->load(['tipo']),
      'resultado' => $resultado->load([
        'analises',
        'campanhas.pontos.lista.parametros_vinculados.medicao'
      ]),
      ...$response
    ]);
  }
}
