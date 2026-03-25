<?php

namespace App\Domain\Contrato\Contratada\DadosGerais\Historico\Controller;

use App\Domain\Contrato\Contratada\DadosGerais\Historico\Services\HistoricoService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UpdateHistoricoContratadaController extends Controller
{
  public function __construct(private readonly HistoricoService $historicoService)
  {
  }

  public function index(Request $request)
  {
    $response = $this->historicoService->update($request->all());

    return to_route('contratos.contratada.dados_gerais.index', ['contrato' => $request->contrato_id])->with('message', $response['request']);
  }
}
