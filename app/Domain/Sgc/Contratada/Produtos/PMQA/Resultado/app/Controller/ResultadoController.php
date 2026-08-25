<?php

namespace App\Domain\Sgc\Contratada\Produtos\PMQA\Resultado\app\Controller;

use App\Domain\Sgc\Contratada\Produtos\PMQA\Resultado\app\Services\ResultadoService;
use App\Models\Contrato;
use App\Models\SgcPmqa;
use App\Models\SgcPmqaResultado;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ResultadoController extends Controller
{
  public function __construct(private readonly ResultadoService $resultadoService)
  {
  }

  public function index(Contrato $contrato, string $produto, SgcPmqa $pmqa, SgcPmqaResultado $resultado): Response
  {
    $response = $this->resultadoService->resultado($resultado);

    return Inertia::render('Sgc/Contratada/Produtos/Pmqa/Resultado/Resultado', [
      'contrato' => $contrato,
      'produto' => $produto,
      'pmqa' => $pmqa,
      'canApprove' => auth()->user()->hasAnyRole(['Administrador', 'Fiscal']),
      ...$response
    ]);
  }
}
