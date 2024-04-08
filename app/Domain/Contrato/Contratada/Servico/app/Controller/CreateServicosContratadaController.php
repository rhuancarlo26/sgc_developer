<?php

namespace App\Domain\Contrato\Contratada\Servico\app\Controller;

use App\Domain\Contrato\Contratada\Servico\app\Services\ServicoService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CreateServicosContratadaController extends Controller
{
  public function __construct(private readonly ServicoService $servicoService)
  {
  }

  public function index(Contrato $contrato, Servicos $servico): Response
  {
    $response = $this->servicoService->createServicos($contrato, $servico);

    return Inertia::render('Contrato/Contratada/Servicos/Form', [
      'contrato' => $contrato,
      ...$response
    ]);
  }
}
