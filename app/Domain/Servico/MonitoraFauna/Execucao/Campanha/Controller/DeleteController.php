<?php

namespace App\Domain\Servico\MonitoraFauna\Execucao\Campanha\Controller;

use App\Domain\Servico\MonitoraFauna\Execucao\Campanha\Services\CampanhaService;
use App\Models\Contrato;
use App\Models\ServicoMonitoraFaunaExecCampanha;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class DeleteController extends Controller
{
  public function __construct(private readonly CampanhaService $campanhaService) {}

  public function index(Contrato $contrato, Servicos $servico, ServicoMonitoraFaunaExecCampanha $campanha): RedirectResponse
  {
    $response = $this->campanhaService->delete($campanha);

    return to_route('contratos.contratada.servicos.monitora_fauna.execucao.campanha.index', ['contrato' => $contrato->id, 'servico' => $servico->id])->with('message', $response['request']);
  }
}
