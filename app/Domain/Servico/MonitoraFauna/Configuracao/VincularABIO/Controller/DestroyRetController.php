<?php

namespace App\Domain\Servico\MonitoraFauna\Configuracao\VincularABIO\Controller;

use App\Domain\Servico\MonitoraFauna\Configuracao\VincularABIO\Services\VincularABIOService;
use App\Models\Contrato;
use App\Models\ServicoMonitoraFaunaConfigAbioRet;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class DestroyRetController extends Controller
{
  public function __construct(private readonly VincularABIOService $vincularABIOService) {}

  public function index(Contrato $contrato, Servicos $servico, ServicoMonitoraFaunaConfigAbioRet $abio_ret): RedirectResponse
  {
    $response = $this->vincularABIOService->destroy_ret(abio_ret: $abio_ret);

    return to_route('contratos.contratada.servicos.monitora_fauna.configuracoes.vincular_abio.index', ['contrato' => $contrato->id, 'servico' => $servico->id])->with('message', $response['request']);
  }
}
