<?php

namespace App\Domain\Sgc\Contratada\Produtos\Controller;

use App\Shared\Http\Controllers\Controller;
use App\Domain\Sgc\Contratada\Produtos\Services\ProdutosService;
use App\Models\Contrato;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StoreProdutoAbioController extends Controller
{
    public function __construct(private readonly ProdutosService $produtosService) {}

    public function store(Contrato $contrato, string $produto, Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id_abio' => 'required|exists:fauna_config_abio,id',
        ]);

        $response = $this->produtosService->store_abio($validated);

        return to_route('sgc.contratada.produtos.create', [$contrato->id, $produto])
            ->with('message', $response['request'] ?? 'ABIO vinculado com sucesso!');
    }

    public function destroy(Contrato $contrato, string $produto, $produto_abio): RedirectResponse
    {
        $response = $this->produtosService->delete_abio($produto_abio);

        return to_route('sgc.contratada.produtos.create', [$contrato->id, $produto])
            ->with('message', $response['request'] ?? 'ABIO removido com sucesso!');
    }
}