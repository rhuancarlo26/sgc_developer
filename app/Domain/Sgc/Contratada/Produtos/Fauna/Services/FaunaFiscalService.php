<?php

namespace App\Domain\Sgc\Contratada\Produtos\Fauna\Services;

use App\Models\SgcFaunaAnaliseEtapa;
use App\Models\SgcFaunaCampanha;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FaunaFiscalService
{
    public function getAnalisesByCampanha($contrato, $campanha)
    {
        $analises = SgcFaunaAnaliseEtapa::where('id_contrato', $contrato)
            ->where('id_campanha', $campanha)
            ->get(['id', 'etapa', 'analise', 'status', 'comentario', 'created_at'])
            ->map(function ($analise) {
                return [
                    'id' => $analise->id,
                    'etapa' => $analise->etapa,
                    'analise' => $analise->analise,
                    'status' => $analise->status,
                    'comentario' => $analise->comentario,
                    'created_at' => $analise->created_at,
                ];
            })
            ->toArray();

        Log::info('FaunaFiscalService: Dados retornados por getAnalisesByCampanha', [
            'contrato' => $contrato,
            'campanha' => $campanha,
            'analises' => $analises,
        ]);

        return $analises;
    }

    public function salvarAnaliseEtapa($contrato, $campanha, $data)
    {
        // Obter a versão atual da análise da campanha
        $versaoAnalise = SgcFaunaCampanha::where('id', $campanha)
            ->where('id_contrato', $contrato)
            ->value('versao_analise') ?? 1;

        // Verificar se já existe uma análise para a etapa na versão atual
        $existingAnalise = SgcFaunaAnaliseEtapa::where('id_contrato', $contrato)
            ->where('id_campanha', $campanha)
            ->where('etapa', $data['etapa'])
            ->where('analise', $versaoAnalise)
            ->first();

        $analiseData = [
            'id_contrato' => $contrato,
            'id_campanha' => $campanha,
            'etapa' => $data['etapa'],
            'analise' => $versaoAnalise,
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

        // Atualizar status e incrementar versao_analise se rejeitada
        $updateData = ['status' => $hasRejeitada ? 'Rejeitada' : 'Aprovada'];
        if ($hasRejeitada) {
            $updateData['versao_analise'] = SgcFaunaCampanha::where('id', $campanha)
                ->where('id_contrato', $contrato)
                ->value('versao_analise') + 1;
        }

        SgcFaunaCampanha::where('id', $campanha)
            ->where('id_contrato', $contrato)
            ->update($updateData);

        Log::info('FaunaFiscalService: Avaliação finalizada', [
            'contrato' => $contrato,
            'campanha' => $campanha,
            'status' => $hasRejeitada ? 'Rejeitada' : 'Aprovada',
            'versao_analise' => $updateData['versao_analise'] ?? 1,
        ]);
    }
}
