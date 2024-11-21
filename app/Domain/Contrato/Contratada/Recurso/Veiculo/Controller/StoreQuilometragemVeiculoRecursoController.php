<?php

namespace App\Domain\Contrato\Contratada\Recurso\Veiculo\Controller;

use App\Domain\Contrato\Contratada\Recurso\Veiculo\Services\VeiculoRecursoService;
use App\Shared\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StoreQuilometragemVeiculoRecursoController extends Controller
{
  public function __construct(private readonly VeiculoRecursoService $veiculoRecursoService)
  {
  }

  public function index(Request $request)
  {
    $post = [
      ...$request->all(),
      'mes_referencia' => Carbon::parse($request->mes_referencia)->addDays(now()->day())->format('Y-m-d')
    ];

    $response = $this->veiculoRecursoService->salvarQuilometragemVeiculo($post);

    return to_route('contratos.contratada.recurso.veiculo.create', ['contrato' => $request->contrato_id, 'veiculo' => $request->recurso_veiculo_id])->with('message', $response['request']);
  }
}
