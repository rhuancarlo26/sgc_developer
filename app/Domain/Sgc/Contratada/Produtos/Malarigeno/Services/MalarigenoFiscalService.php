<?php

namespace App\Domain\Sgc\Contratada\Produtos\Malarigeno\Services;

use App\Models\SgcMalarigeno;
use App\Models\SgcMalarigenoAnalise;
use Illuminate\Support\Facades\Auth;

class MalarigenoFiscalService
{
    public function getAnalisesByCampanha($contrato, $campanhaId): array
    {
        $analises = SgcMalarigenoAnalise::where('id_contrato', $contrato)
            ->where('id_campanha', $campanhaId)
            ->with('fiscal:id,name')
            ->orderByDesc('versao_analise')
            ->orderByDesc('created_at')
            ->get()
            ->toArray();

        return $analises;
    }

    public function salvarAnalise($contrato, $campanhaId, array $validated): void
    {
        $campanha = SgcMalarigeno::where('id_contrato', $contrato)
            ->where('id', $campanhaId)
            ->firstOrFail();

        $analise = SgcMalarigenoAnalise::create([
            'id_contrato' => $contrato,
            'id_campanha' => $campanhaId,
            'versao_analise' => $campanha->versao_analise,
            'status' => $validated['status'],
            'observacoes' => $validated['observacoes'] ?? null,
            'fiscal_id' => Auth::id(),
        ]);

        return;
    }

    public function finalizarAvaliacaoCampanha($contrato, $campanhaId): void
    {
        $campanha = SgcMalarigeno::where('id_contrato', $contrato)
            ->where('id', $campanhaId)
            ->firstOrFail();

        $ultimaAnalise = SgcMalarigenoAnalise::where('id_contrato', $contrato)
            ->where('id_campanha', $campanhaId)
            ->where('versao_analise', $campanha->versao_analise)
            ->latest('created_at')
            ->first();

        if (!$ultimaAnalise) {
            throw new \Exception('Nenhuma análise encontrada para finalizar');
        }

        if ($ultimaAnalise->status === 'Aprovada') {
            $campanha->update([
                'status' => 'Aprovada',
                'aprovado_por' => Auth::id(),
                'data_aprovacao' => now(),
            ]);
        } else {
            $campanha->update([
                'status' => 'Rejeitada',
                'versao_analise' => $campanha->versao_analise + 1,
            ]);
        }
    }
}
