<?php

namespace App\Domain\Servico\MonitoraFauna\Execucao\Campanha\Controller;

use App\Domain\Servico\MonitoraFauna\Execucao\Campanha\Services\CampanhaService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StoreController extends Controller
{
  public function __construct(private readonly CampanhaService $campanhaService) {}

  public function index(Contrato $contrato, Servicos $servico, Request $request): RedirectResponse
  {
    $response = $this->campanhaService->store(post: $request->all());

    return to_route('contratos.contratada.servicos.monitora_fauna.execucao.campanha.create', ['contrato' => $contrato->id, 'servico' => $servico->id, 'campanha' => $response['model']['id'] ?? null])->with('message', $response['request']);
  }
}
