<?php

namespace App\Domain\Sgc\Contratada\Produtos\Fauna\Services;

use App\Models\SgcFaunaAnaliseEtapa;
use App\Models\SgcFaunaCampanha;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FaunaFiscalService
{
    public function salvarAnaliseEtapa($contratoId, $campanhaId, array $data)
    {
        Log::info('FaunaFiscalService: Salvando análise de etapa', [
            'contrato_id' => $contratoId,
            'campanha_id' => $campanhaId,
            'etapa' => $data['etapa'],
            'status' => $data['status'],
            'fiscal_id' => Auth::id(),
        ]);

        // Validar etapa
        $validEtapas = [
            'apresentacao_geral',
            'caracterizacao_area',
            'modulos_amostrais',
            'pontos_quelo_crocod',
            'pontos_cavernicola',
            'metodologia',
            'resultados',
            'anexos'
        ];
        if (!in_array($data['etapa'], $validEtapas)) {
            Log::error('FaunaFiscalService: Etapa inválida', [
                'etapa' => $data['etapa'],
            ]);
            throw new \Exception('Etapa inválida.');
        }

        // Validar status
        if (!in_array($data['status'], ['Aprovada', 'Rejeitada'])) {
            Log::error('FaunaFiscalService: Status inválido', [
                'status' => $data['status'],
            ]);
            throw new \Exception('Status inválido.');
        }

        // Verificar se a campanha está em análise
        $campanha = SgcFaunaCampanha::where('id_contrato', $contratoId)
            ->where('id', $campanhaId)
            ->firstOrFail();

        if ($campanha->status !== 'Em análise') {
            Log::error('FaunaFiscalService: Campanha não está em análise', [
                'campanha_id' => $campanhaId,
                'status' => $campanha->status,
            ]);
            throw new \Exception('Campanha não está em análise.');
        }

        // Validar observações para rejeição
        if ($data['status'] === 'Rejeitada' && empty(trim($data['observacoes'] ?? ''))) {
            Log::error('FaunaFiscalService: Observações obrigatórias para rejeição', [
                'etapa' => $data['etapa'],
            ]);
            throw new \Exception('Observações são obrigatórias para rejeição.');
        }

        // Salvar ou atualizar análise da etapa
        $analise = SgcFaunaAnaliseEtapa::updateOrCreate(
            [
                'id_contrato' => $contratoId,
                'id_campanha' => $campanhaId,
                'etapa' => $data['etapa'],
                'fiscal_id' => Auth::id(),
            ],
            [
                'status' => $data['status'],
                'comentario' => $data['observacoes'] ?? null,
            ]
        );

        Log::info('FaunaFiscalService: Registro salvo em sgc_fauna_analise_etapas', [
            'analise_id' => $analise->id,
            'id_contrato' => $contratoId,
            'id_campanha' => $campanhaId,
            'etapa' => $data['etapa'],
            'status' => $data['status'],
            'comentario' => $data['observacoes'],
        ]);

        return $analise;
    }

    public function finalizarAvaliacaoCampanha($contratoId, $campanhaId)
    {
        Log::info('FaunaFiscalService: Finalizando avaliação da campanha', [
            'contrato_id' => $contratoId,
            'campanha_id' => $campanhaId,
            'fiscal_id' => Auth::id(),
        ]);

        // Verificar se a campanha está em análise
        $campanha = SgcFaunaCampanha::where('id_contrato', $contratoId)
            ->where('id', $campanhaId)
            ->firstOrFail();

        if ($campanha->status !== 'Em análise') {
            Log::error('FaunaFiscalService: Campanha não está em análise', [
                'campanha_id' => $campanhaId,
                'status' => $campanha->status,
            ]);
            throw new \Exception('Campanha não está em análise.');
        }

        // Validar se todas as etapas foram analisadas
        $validEtapas = [
            'apresentacao_geral',
            'caracterizacao_area',
            'modulos_amostrais',
            'pontos_quelo_crocod',
            'pontos_cavernicola',
            'metodologia',
            'resultados',
            'anexos'
        ];

        $etapasAnalisadas = SgcFaunaAnaliseEtapa::where('id_contrato', $contratoId)
            ->where('id_campanha', $campanhaId)
            ->where('fiscal_id', Auth::id())
            ->count();

        if ($etapasAnalisadas !== count($validEtapas)) {
            Log::error('FaunaFiscalService: Nem todas as etapas foram analisadas', [
                'etapas_analisadas' => $etapasAnalisadas,
                'etapas_esperadas' => count($validEtapas),
            ]);
            throw new \Exception('Todas as etapas devem ser analisadas antes de finalizar a avaliação.');
        }

        // Determinar o status da campanha
        $temRejeicao = SgcFaunaAnaliseEtapa::where('id_contrato', $contratoId)
            ->where('id_campanha', $campanhaId)
            ->where('fiscal_id', Auth::id())
            ->where('status', 'Rejeitada')
            ->exists();

        $novoStatus = $temRejeicao ? 'Rejeitada' : 'Aprovada';
        $campanha->update(['status' => $novoStatus]);

        Log::info('FaunaFiscalService: Status da campanha atualizado', [
            'campanha_id' => $campanhaId,
            'novo_status' => $novoStatus,
        ]);

        return $campanha;
    }

        public function getAnalisesByCampanha($contratoId, $campanhaId)
    {
        return SgcFaunaAnaliseEtapa::where('id_contrato', $contratoId)
            ->where('id_campanha', $campanhaId)
            ->where('fiscal_id', Auth::id())
            ->get(['id', 'etapa', 'status', 'comentario', 'created_at'])
            ->toArray();
    }
}