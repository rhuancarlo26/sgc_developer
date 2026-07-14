<?php

namespace App\Domain\Sgc\Contratada\Produtos\Fauna\Controller;

use App\Shared\Http\Controllers\Controller;
use App\Domain\Sgc\Contratada\Produtos\Fauna\Requests\StoreRascunhoRequest;
use App\Models\SgcFaunaCampanha;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class InitializarRascunhoCampanhaController extends Controller
{
    /**
     * Cria uma campanha com status='rascunho' e retorna o campanhaId ao frontend.
     *
     * O frontend chama esta rota assim que o usuário preenche
     * empreendimento + subproduto e clica em "Avançar" na primeira sub-etapa.
     * A partir daí, todas as outras abas salvam contra esse campanhaId,
     * evitando perda de dados por expiração de sessão ou recarga de página.
     */
    public function __invoke(StoreRascunhoRequest $request, $contrato, $produto)
    {
        // Se já existe rascunho em aberto para este usuário/contrato/produto,
        // reaproveita em vez de criar duplicata.
        $existente = SgcFaunaCampanha::where('id_contrato', $contrato)
            ->where('subproduto', $request->subproduto)
            ->where('cod_emp', $request->cod_emp)
            ->where('status', 'Em elaboração')
            ->where('id_campanha', $request->id_campanha)
            ->latest()
            ->first();

        if ($existente) {

            return response()->json([
                'campanha_id' => $existente->id,
                'etapa_atual' => $existente->etapa_atual ?? 'apresentacao',
                'rascunho'    => true,
                'reaproveitado' => true,
            ]);
        }

        $campanha = SgcFaunaCampanha::create([
            'id_contrato'    => $contrato,
            'subproduto'     => $request->subproduto,
            'cod_emp'        => $request->cod_emp,
            'id_campanha' => (int) $request->id_campanha,
            'status'         => 'Em elaboração',
            'etapa_atual'    => 'apresentacao',
            'versao_analise' => 1,
            
        ]);

        return response()->json([
            'campanha_id'   => $campanha->id,
            'etapa_atual'   => 'apresentacao',
            'rascunho'      => true,
            'reaproveitado' => false,
        ]);
    }
}
