<?php

namespace App\Domain\Contrato\Contratada\Recurso\Veiculo\Controller;

use App\Domain\Contrato\Contratada\Recurso\Veiculo\Services\VeiculoRecursoService;
use App\Models\Contrato;
use App\Models\RecursoVeiculo;
use App\Shared\Http\Controllers\Controller;
use Inertia\Inertia;

class CreateVeiculoRecursoController extends Controller
{
  public function __construct(private readonly VeiculoRecursoService $veiculoRecursoService)
  {
  }

  public function index(Contrato $contrato, RecursoVeiculo $veiculo)
  {
    $response = $this->veiculoRecursoService->createForm();

    return Inertia::render('Contrato/Contratada/Recurso/Veiculo/Form', [
      'contrato' => $contrato,
      'veiculo' => $veiculo->load(['codigo', 'documentos']),
      ...$response
    ]);
  }
}
