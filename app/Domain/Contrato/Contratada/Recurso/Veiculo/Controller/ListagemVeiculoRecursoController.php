<?php

namespace App\Domain\Contrato\Contratada\Recurso\Veiculo\Controller;

use App\Domain\Contrato\Contratada\Recurso\Veiculo\Services\VeiculoRecursoService;
use App\Models\Contrato;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ListagemVeiculoRecursoController extends Controller
{
  public function __construct(private readonly VeiculoRecursoService $veiculoRecursoService)
  {
  }

  public function index(Contrato $contrato, Request $request)
  {
    $searchParams = $request->all('searchColumn', 'searchValue');

    $response = $this->veiculoRecursoService->listagemVeiculos($searchParams);

    return Inertia::render('Contrato/Contratada/Recurso/Veiculo/Index', [
      'contrato' => $contrato,
      ...$response
    ]);
  }
}