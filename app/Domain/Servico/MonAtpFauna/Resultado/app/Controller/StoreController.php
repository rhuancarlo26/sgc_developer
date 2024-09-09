<?php

namespace App\Domain\Servico\MonAtpFauna\Resultado\app\Controller;

use App\Domain\Servico\MonAtpFauna\Resultado\app\Requests\StoreRequest;
use App\Domain\Servico\MonAtpFauna\Resultado\app\Services\ResultadoService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StoreController extends Controller
{
  public function __construct(private readonly ResultadoService $resultadoService)
  {
  }

  public function index(Contrato $contrato, Servicos $servico, StoreRequest $request): RedirectResponse
  {
    $post = [
      'fk_servico' => $servico->id,
      'fk_licenca' => $request->validated('licenca_id')
    ];

    $response = $this->resultadoService->store(post: $post);

    return to_route('contratos.contratada.servicos.mon_atp_fauna.configuracoes.malha_viaria.index', ['contrato' => $contrato->id, 'servico' => $servico->id])->with('message', $response['request']);
  }
}
