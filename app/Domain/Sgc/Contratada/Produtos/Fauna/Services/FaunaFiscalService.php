<?php

namespace App\Domain\Sgc\Contratada\Produtos\Fauna\Services;

use App\Models\SgcFaunaAnaliseEtapa;
use App\Models\SgcFaunaCampanha;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FaunaFiscalService
{
    public function salvarAnaliseEtapa($contrato, $campanha, $data)
    {
        // Encontrar o maior número de análise para a campanha
        $maxAnalise = SgcFaunaAnaliseEtapa::where('id_contrato', $contrato)
            ->where('id_campanha', $campanha)
            ->max('analise') ?? 0;

        // Verificar o status da campanha
        $campanhaStatus = SgcFaunaCampanha::where('id', $campanha)
            ->where('id_contrato', $contrato)
            ->value('status');

        // Se a campanha está em análise pela primeira vez ou após rejeição, determinar o número da análise
        $analiseNumero = $campanhaStatus === 'Em análise' && $maxAnalise > 0 ? $maxAnalise : $maxAnalise + 1;

        // Verificar se já existe uma análise para a etapa no ciclo atual
        $existingAnalise = SgcFaunaAnaliseEtapa::where('id_contrato', $contrato)
            ->where('id_campanha', $campanha)
            ->where('etapa', $data['etapa'])
            ->where('analise', $analiseNumero)
            ->first();

        $analiseData = [
            'id_contrato' => $contrato,
            'id_campanha' => $campanha,
            'etapa' => $data['etapa'],
            'analise' => $analiseNumero,
            'status' => $data['status'],
            'comentario' => $data['observacoes'] ?? null,
            'fiscal_id' => Auth::id(),
        ];

        // Atualizar ou criar a análise
        if ($existingAnalise) {
            $existingAnalise->update($analiseData);
            $analiseId = $existingAnalise->id;
        } else {
            $analise = SgcFaunaAnaliseEtapa::create($analiseData);
            $analiseId = $analise->id;
        }

        Log::info('FaunaFiscalService: Análise salva ou atualizada', [
            'analiseData' => $analiseData,
            'analiseId' => $analiseId,
        ]);

        return $analiseId;
    }

    public function finalizarAvaliacaoCampanha($contrato, $campanha)
    {
        $analises = SgcFaunaAnaliseEtapa::where('id_contrato', $contrato)
            ->where('id_campanha', $campanha)
            ->get();

        $hasRejeitada = $analises->contains('status', 'Rejeitada');
        
        SgcFaunaCampanha::where('id', $campanha)
            ->where('id_contrato', $contrato)
            ->update(['status' => $hasRejeitada ? 'Rejeitada' : 'Aprovada']);

        Log::info('FaunaFiscalService: Avaliação finalizada', [
            'contrato' => $contrato,
            'campanha' => $campanha,
            'status' => $hasRejeitada ? 'Rejeitada' : 'Aprovada',
        ]);
    }

        public function getAnalisesByCampanha($contratoId, $campanhaId)
    {
        return SgcFaunaAnaliseEtapa::where('id_contrato', $contratoId)
            ->where('id_campanha', $campanhaId)
            ->where('fiscal_id', Auth::id())
            ->get(['id', 'etapa','analise', 'status', 'comentario', 'created_at'])
            ->toArray();
    }
}