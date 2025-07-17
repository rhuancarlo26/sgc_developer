<?php

namespace App\Domain\Sgc\Contratada\Produtos\Services;

use App\Models\SgcvwSubprodutos;
use App\Models\ServicoMonitoraFaunaConfigAbio;
use App\Models\SgcProdutoAbio;
use App\Models\Licenca;
use Illuminate\Support\Facades\Log;

class ProdutosService
{
    public function getSubprodutosByContrato($contrato, $produto)
    {
        return SgcvwSubprodutos::where('contrato_id', $contrato)
            ->where('familia', ucfirst($produto))
            ->get()
            ->toArray();
    }

    // public function getAbios()
    // {
        
    //     return ServicoMonitoraFaunaConfigAbio::with(['licenca:id,numero_licenca'])
    //         ->get(['id', 'id_licenca'])
    //         ->map(function ($abio) {
    //             return [
    //                 'id' => $abio->id,
    //                 'licenca' => $abio->licenca ? [
    //                     'id' => $abio->licenca->id,
    //                     'numero_licenca' => $abio->licenca->numero_licenca
    //                 ] : null
    //             ];
    //         });
    // }

    public function getAbios()
    {
        return Licenca::where('tipo', 12)
            ->get(['id', 'numero_licenca'])
            ->map(function ($licenca) {
                return [
                    'id' => $licenca->id,
                    'licenca' => [
                        'id' => $licenca->id,
                        'numero_licenca' => $licenca->numero_licenca
                    ]
                ];
            });
    }

    public function store_abio(array $post)
    {
        return SgcProdutoAbio::create([
            'id_abio' => $post['id_abio'],
        ]);
    }

    public function delete_abio($produto_abio)
    {
        $abio = SgcProdutoAbio::findOrFail($produto_abio);
        $abio->delete();
        return ['request' => 'ABIO removido com sucesso'];
    }
}