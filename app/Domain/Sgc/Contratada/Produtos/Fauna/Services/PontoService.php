<?php

namespace App\Domain\Sgc\Contratada\Produtos\Fauna\Services;

use App\Models\SgcFaunaQuelonios;
use App\Models\SgcFaunaCavernicola;

class PontoService
{
    public function salvarPontosQuelonios($contratoId, $campanhaId, array $pontos, $naoSeAplica)
    {
        if (!$naoSeAplica && !empty($pontos)) {
            $pontosEnviadosIds = collect($pontos)->pluck('id')->filter()->toArray();
            SgcFaunaQuelonios::where('id_campanha', $campanhaId)
                ->whereNotIn('id', $pontosEnviadosIds)
                ->delete();

            foreach ($pontos as $pontoData) {
                $ponto = array_filter([
                    'id_contrato' => $contratoId,
                    'ponto_de_coleta' => $pontoData['ponto_de_coleta'] ?? null,
                    'nome_curso_hidrico' => $pontoData['nome_curso_hidrico'] ?? null,
                    'latitude' => $pontoData['latitude'] ?? null,
                    'longitude' => $pontoData['longitude'] ?? null,
                    'bacia_hidrografica' => $pontoData['bacia'] ?? null,
                    'profundidade' => $pontoData['profundidade'] ?? null,
                    'largura' => $pontoData['largura'] ?? null,
                    'tipo_substrato' => $pontoData['tipo_substrato'] ?? null,
                ]);

                SgcFaunaQuelonios::updateOrCreate(
                    [
                        'id' => isset($pontoData['id']) ? (int) $pontoData['id'] : null,
                        'id_campanha' => $campanhaId,
                    ],
                    $ponto
                );
            }
        } else {
            SgcFaunaQuelonios::where('id_campanha', $campanhaId)->delete();
        }
    }

    public function salvarPontosCavernicola($contratoId, $campanhaId, array $pontos, $naoSeAplica)
    {
        if (!$naoSeAplica && !empty($pontos)) {
            $pontosEnviadosIds = collect($pontos)->pluck('id')->filter()->toArray();
            SgcFaunaCavernicola::where('id_campanha', $campanhaId)
                ->whereNotIn('id', $pontosEnviadosIds)
                ->delete();

            foreach ($pontos as $pontoData) {
                $ponto = array_filter([
                    'id_contrato' => $contratoId,
                    'cavidade' => $pontoData['cavidade'] ?? null,
                    'latitude' => $pontoData['latitude'] ?? null,
                    'longitude' => $pontoData['longitude'] ?? null,
                    'distancia_eixo_rodovia' => $pontoData['distancia_eixo_rodovia'] ?? null,
                    'formacao_associada' => $pontoData['formacao_associada'] ?? null,
                    'temperatura_media_interna' => $pontoData['temperatura_media_interna'] ?? null,
                    'temperatura_media_externa' => $pontoData['temperatura_media_externa'] ?? null,
                    'umidade_relativa_interna' => $pontoData['umidade_relativa_interna'] ?? null,
                    'umidade_relativa_externa' => $pontoData['umidade_relativa_externa'] ?? null,
                ]);

                SgcFaunaCavernicola::updateOrCreate(
                    [
                        'id' => isset($pontoData['id']) ? (int) $pontoData['id'] : null,
                        'id_campanha' => $campanhaId,
                    ],
                    $ponto
                );
            }
        } else {
            SgcFaunaCavernicola::where('id_campanha', $campanhaId)->delete();
        }
    }
}