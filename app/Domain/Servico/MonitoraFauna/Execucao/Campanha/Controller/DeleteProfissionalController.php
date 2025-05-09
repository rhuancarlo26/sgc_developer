<?php

namespace App\Domain\Servico\MonitoraFauna\Execucao\Campanha\Controller;

use App\Domain\Servico\MonitoraFauna\Execucao\Campanha\Services\CampanhaService;
use App\Models\Contrato;
use App\Models\ServicoMonitoraFaunaExecCampanhaProfissGrupo;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class DeleteProfissionalController extends Controller
{
  public function __construct(private readonly CampanhaService $campanhaService) {}

  public function index(Contrato $contrato, Servicos $servico, ServicoMonitoraFaunaExecCampanhaProfissGrupo $campanha_profissional): RedirectResponse
  {
    $response = $this->campanhaService->delete_profissional($campanha_profissional);

    return to_route('contratos.contratada.servicos.monitora_fauna.execucao.campanha.create', ['contrato' => $contrato->id, 'servico' => $servico->id, 'campanha' => $campanha_profissional->id_campanha])->with('message', $response['request']);
  }
}
