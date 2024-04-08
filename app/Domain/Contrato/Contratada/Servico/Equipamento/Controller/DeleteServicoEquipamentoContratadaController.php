<?php

namespace App\Domain\Contrato\Contratada\Servico\Equipamento\Controller;

use App\Domain\Contrato\Contratada\Servico\Equipamento\Services\ServicoEquipamentoService;
use App\Models\RecursoEquipamento;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DeleteServicoEquipamentoContratadaController extends Controller
{
  public function __construct(private readonly ServicoEquipamentoService $servicoEquipamento)
  {
  }

  public function index(RecursoEquipamento $equipamento, Request $request)
  {
    $response = $this->servicoEquipamento->deleteEquipamento($equipamento, $request->all());

    return to_route('contratos.contratada.servicos.create', ['contrato' => $request->contrato_id, 'servico' => $request->servico_id])->with('message', $response['request']);
  }
}