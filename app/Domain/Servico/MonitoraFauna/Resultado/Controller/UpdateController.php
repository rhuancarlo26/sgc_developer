<?php

namespace App\Domain\Servico\MonitoraFauna\Resultado\Controller;

use App\Domain\Servico\MonitoraFauna\Execucao\Campanha\Services\CampanhaService;
use App\Domain\Servico\MonitoraFauna\Resultado\Services\ResultadoService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UpdateController extends Controller
{
  public function __construct(private readonly ResultadoService $resultadoService) {}

  public function index(Contrato $contrato, Servicos $servico, Request $request): RedirectResponse
  {
    $response = $this->resultadoService->update($request->all());

    return to_route('contratos.contratada.servicos.monitora_fauna.resultado.index', ['contrato' => $contrato->id, 'servico' => $servico->id])->with('message', $response['request']);
  }
}
