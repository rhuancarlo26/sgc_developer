<?php

namespace App\Domain\Servico\Rh\Controller;

use App\Domain\Servico\Rh\Services\ServicoRhService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StoreServicoRhContratadaController extends Controller
{
  public function __construct(private readonly ServicoRhService $servicoRhService)
  {
  }

  public function index(Request $request)
  {
    $post = [
      'servico_id' => $request->servico_id,
      'recurso_rh_id' => $request->rh['id']
    ];

    $response = $this->servicoRhService->storeServicoRh($post);

    return to_route('contratos.contratada.servicos.create', ['contrato' => $request->contrato_id, 'servico' => $request->servico_id])->with('message', $response['request']);
  }
}
