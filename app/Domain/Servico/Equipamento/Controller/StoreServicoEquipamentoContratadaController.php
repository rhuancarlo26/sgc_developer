<?php

namespace App\Domain\Servico\Equipamento\Controller;

use App\Domain\Servico\Equipamento\Services\ServicoEquipamentoService;
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
            'id_servico' => $request->servico_id,
            'id_equipamento' => $request->equipamento['id']
        ];

        $response = $this->servicoEquipamento->StoreServicoEquipamento($post);

        return to_route('contratos.contratada.servicos.create', [
            'contrato' => $request->id_contrato,
            'servico' => $request->servico_id
        ])->with('message', $response['request']);
    }
}
