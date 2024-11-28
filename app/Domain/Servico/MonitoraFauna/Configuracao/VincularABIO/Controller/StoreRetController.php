<?php

namespace App\Domain\Servico\MonitoraFauna\Configuracao\VincularABIO\Controller;

use App\Domain\Servico\MonitoraFauna\Configuracao\VincularABIO\Requests\StoreRequest;
use App\Domain\Servico\MonitoraFauna\Configuracao\VincularABIO\Services\VincularABIOService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StoreRetController extends Controller
{
  public function __construct(private readonly VincularABIOService $vincularABIOService) {}

  public function index(Contrato $contrato, Servicos $servico, Request $request): RedirectResponse
  {
    $post = [
      'id_servico' => $servico->id,
      'id_abio' => $request->id_abio,
      'arquivo' => $request->arquivo_ret
    ];

    $response = $this->vincularABIOService->store_ret(post: $post);

    return to_route('contratos.contratada.servicos.monitora_fauna.configuracoes.vincular_abio.index', ['contrato' => $contrato->id, 'servico' => $servico->id])->with('message', $response['request']);
  }
}
