<?php

namespace App\Domain\Servico\MonitoraFauna\Configuracao\ModuloAmostral\Controller;

use App\Domain\Servico\MonitoraFauna\Configuracao\ModuloAmostral\Requests\StoreRequest;
use App\Domain\Servico\MonitoraFauna\Configuracao\ModuloAmostral\Services\ModuloAmostralService;
use App\Models\Contrato;
use App\Models\ServicoMonitoraFaunaConfigModuloAmostral;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DeleteController extends Controller
{
  public function __construct(private readonly ModuloAmostralService $moduloAmostralService) {}

  public function index(Contrato $contrato, Servicos $servico, ServicoMonitoraFaunaConfigModuloAmostral $modulo): RedirectResponse
  {
    $response = $this->moduloAmostralService->destroy($modulo);

    return to_route('contratos.contratada.servicos.monitora_fauna.configuracoes.modulo_amostral.index', ['contrato' => $contrato->id, 'servico' => $servico->id])->with('message', $response['request']);
  }
}
