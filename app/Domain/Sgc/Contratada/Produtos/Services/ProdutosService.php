<?php
// app/Domain/Sgc/Contratada/Produtos/Services/ProdutosService.php

namespace App\Domain\Sgc\Contratada\Produtos\Services;

use App\Models\SgcvwSubprodutos;
use App\Models\ServicoMonitoraFaunaConfigAbio;
use App\Models\SgcProdutoAbio;

class ProdutosService
{
    protected string $modelClass = SgcProduto::class;
    protected string $modelClassAbio = SgcProdutoAbio::class;

    public function createSubproduto($contratoId, $data)
    {
        SgcvwSubprodutos::create([
            'contrato_id' => $contratoId,
            'cod_emp' => $data['cod_emp'],
            'descricao_revisada' => $data['descricao_revisada'],
            'familia' => $data['familia'],
            'vincular_abio' => $data['vincular_abio'] ?? null,
        ]);
    }

    public function getSubprodutosByContrato($contrato, $produto)
    {
        return SgcvwSubprodutos::where('contrato_id', $contrato)
            ->where('familia', ucfirst($produto))
            ->get()
            ->toArray();
    }

    public function getAbios()
    {
        return ServicoMonitoraFaunaConfigAbio::with(['licenca:id,numero_licenca'])
            ->get(['id', 'id_licenca']);
    }

    public function store_abio(array $post)
    {
        // Criar registro em sgc_produto_abios
        return SgcProdutoAbio::create([
            'id_abio' => $post['id_abio'],
            // id_produto será associado após criar o produto
        ]);
    }

    public function delete_abio($produto_abio)
    {
        // Excluir registro em sgc_produto_abios
        $abio = SgcProdutoAbio::findOrFail($produto_abio);
        $abio->delete();
        return ['request' => 'ABIO removido com sucesso'];
    }
}