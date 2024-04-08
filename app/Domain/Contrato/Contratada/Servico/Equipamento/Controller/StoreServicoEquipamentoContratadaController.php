<?php

namespace App\Domain\Contrato\Contratada\Servico\Equipamento\Controller;

use App\Domain\Contrato\Contratada\Servico\Equipamento\Services\ServicoEquipamentoService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StoreServicoEquipamentoContratadaController extends Controller
{
  public function __construct(private readonly ServicoEquipamentoService $servicoEquipamento)
  {
  }

  public function index(Request $request)
  {
    $post = [
      'servico_id' => $request->servico_id,
      'recurso_equipamento_id' => $request->equipamento['id']
    ];

    $response = $this->servicoEquipamento->StoreServicoEquipamento($post);

    return to_route('contratos.contratada.servicos.create', ['contrato' => $request->contrato_id, 'servico' => $request->servico_id])->with('message', $response['request']);
  }
}
