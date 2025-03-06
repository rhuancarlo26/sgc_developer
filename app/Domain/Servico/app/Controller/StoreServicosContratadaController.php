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
            'servico' => $request->tipo['id'],
            'tema_servico' => $request->tema['id']
        ];

        $response = $this->servicoService->storeServico($post);

        return to_route('contratos.contratada.servicos.create', [
            'contrato' => $request->id_contrato,
            'servico' => $response['servico']])
            ->with('message', $response['request']);
    }
}
