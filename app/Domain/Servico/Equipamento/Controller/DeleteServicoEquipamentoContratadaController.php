<?php

namespace App\Domain\Servico\Equipamento\Controller;

use App\Domain\Servico\Equipamento\Services\ServicoEquipamentoService;
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

        return to_route('contratos.contratada.servicos.create', [
            'contrato' => $request->id_contrato,
            'servico' => $request->servico_id
        ])->with('message', $response['request']);
    }
}
