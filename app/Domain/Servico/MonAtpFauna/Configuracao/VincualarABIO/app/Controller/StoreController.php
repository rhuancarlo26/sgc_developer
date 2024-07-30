<?php

namespace App\Domain\Servico\MonAtpFauna\Configuracao\VincualarABIO\app\Controller;

use App\Domain\Servico\MonAtpFauna\Configuracao\VincualarABIO\app\Requests\StoreRequest;
use App\Domain\Servico\MonAtpFauna\Configuracao\VincualarABIO\app\Services\VincularABIOService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StoreController extends Controller
{
  public function __construct(private readonly VincularABIOService $vincularABIOService)
  {
  }

  public function index(Contrato $contrato, Servicos $servico, StoreRequest $request): RedirectResponse
  {
    $post = [
      'fk_servico' => $servico->id,
      'fk_licenca' => $request->validated('licenca_id')
    ];

    $response = $this->vincularABIOService->store(post: $post);

    return to_route('contratos.contratada.servicos.mon_atp_fauna.configuracao.vincular_abio.index', ['contrato' => $contrato->id, 'servico' => $servico->id])->with('message', $response['request']);
  }
}
