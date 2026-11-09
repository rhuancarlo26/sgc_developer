<?php

namespace App\Domain\Sgc\Contratada\Produtos\Services;

use App\Models\SgcvwSubprodutos;
use App\Models\Licenca;

class ProdutosService
{
    public function getSubprodutosByContrato($contrato, $produto)
    {
        $familia = match (strtolower($produto)) {
            'indigena' => 'Indígena',
            'quilombola' => 'Quilombolas',
            'audiencia' => 'Audiência',
            'pba' => 'PBA',
            default => ucfirst($produto),
        };

        return SgcvwSubprodutos::where('contrato_id', $contrato)
            ->where('familia', $familia)
            ->get()
            ->toArray();
    }

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
}
