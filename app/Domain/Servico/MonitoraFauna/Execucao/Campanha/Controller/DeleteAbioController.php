<?php

namespace App\Domain\Servico\MonitoraFauna\Execucao\Campanha\Controller;

use App\Domain\Servico\MonitoraFauna\Execucao\Campanha\Services\CampanhaService;
use App\Models\Contrato;
use App\Models\ServicoMonitoraFaunaExecCampanhaAbio;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DeleteAbioController extends Controller
{
  public function __construct(private readonly CampanhaService $campanhaService) {}

  public function index(Contrato $contrato, Servicos $servico, ServicoMonitoraFaunaExecCampanhaAbio $campanha_abio): RedirectResponse
  {
    $response = $this->campanhaService->delete_abio($campanha_abio);

    return to_route('contratos.contratada.servicos.monitora_fauna.execucao.campanha.create', ['contrato' => $contrato->id, 'servico' => $servico->id, 'campanha' => $campanha_abio->id_campanha])->with('message', $response['request']);
  }
}
