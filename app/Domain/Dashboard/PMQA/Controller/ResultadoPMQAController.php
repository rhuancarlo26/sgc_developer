<?php

namespace App\Domain\Dashboard\PMQA\Controller;

use App\Domain\Servico\PMQA\Resultado\app\Services\ResultadoService;
use App\Models\Contrato;
use App\Models\ServicoPmqaResultado;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ResultadoPMQAController extends Controller
{
  public function __construct(private readonly ResultadoService $resultadoService)
  {
  }

  public function index(Contrato $contrato, Servicos $servico, ServicoPmqaResultado $resultado)
  {
      $response = $this->resultadoService->resultado($resultado);

      return response()->json([
          'contrato' => $contrato,
          'servico' => $servico->load(['tipo', 'pmqa_config_lista_parecer']),
          ...$response, 
      ]);
  }
  
}