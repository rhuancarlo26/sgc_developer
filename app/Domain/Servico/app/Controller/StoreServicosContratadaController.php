<?php

namespace App\Domain\Servico\app\Controller;

use App\Domain\Servico\app\Services\ServicoService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StoreServicosContratadaController extends Controller
{
  public function __construct(private readonly ServicoService $servicoService)
  {
  }

  public function index(Request $request)
  {
    $post = [
      ...$request->all(),
      'servico_tipo_id' => $request->tipo['id'],
      'servico_tema_id' => $request->tema['id']
    ];

    $response = $this->servicoService->storeServico($post);

    return to_route('contratos.contratada.servicos.create', ['contrato' => $request->contrato_id, 'servico' => $response['servico']])->with('message', $response['request']);
  }
}
