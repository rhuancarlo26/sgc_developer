<?php

namespace App\Domain\Servico\MonitoraFauna\Execucao\Registro\Controller;

use App\Domain\Servico\MonitoraFauna\Execucao\Campanha\Services\CampanhaService;
use App\Domain\Servico\MonitoraFauna\Execucao\Registro\Services\RegistroService;
use App\Models\Contrato;
use App\Models\ServicoMonitoraFaunaExecCampanha;
use App\Models\ServicoMonitoraFaunaExecRegistro;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class DeleteController extends Controller
{
  public function __construct(private readonly RegistroService $registroService) {}

  public function index(Contrato $contrato, Servicos $servico, ServicoMonitoraFaunaExecRegistro $registro): RedirectResponse
  {
    $response = $this->registroService->delete($registro);

    return to_route('contratos.contratada.servicos.monitora_fauna.execucao.registro.index', ['contrato' => $contrato->id, 'servico' => $servico->id])->with('message', $response['request']);
  }
}
