<?php

namespace App\Domain\Servico\Veiculo\Controller;

use App\Domain\Servico\Veiculo\Services\ServicoVeiculoService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StoreServicoVeiculoContratadaController extends Controller
{
    public function __construct(private readonly ServicoVeiculoService $servicoVeiculoService)
    {
    }

    public function index(Request $request)
    {
        $post = [
            'id_servico' => $request->servico_id,
            'id_veiculo' => $request->veiculo['id']
        ];

        $response = $this->servicoVeiculoService->storeServicoVeiculo($post);

        return to_route('contratos.contratada.servicos.create', [
            'contrato' => $request->id_contrato,
            'servico' => $request->servico_id
        ])->with('message', $response['request']);
    }
}
