<?php

namespace App\Domain\Sgc\Contratada\Produtos\Services;

use App\Models\SgcvwSubprodutos;

class ProdutosService
{
    public function getSubprodutosByContrato($contratoId, $produto = null)
    {
        $query = SgcvwSubprodutos::where('contrato_id', $contratoId);
        
        if ($produto === 'fauna') {
            $query->where('familia', 'Fauna');
        }

        return $query->get();
    }
}