<?php

namespace App\Domain\Sgc\Contratada\Produtos\Espeleologia\Services;

use App\Models\SgcEspeleoCampanha;
use App\Models\SgcEspeleoCampanhaProfissional;
use App\Models\SgcEspeleoJustificativa;
use App\Models\SgcEspeleoMetodologia;
use App\Models\SgcEspeleoResultadoAnexo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EspeleoService
{
    public function getProfissionaisByContrato($contratoId)
    {
        return [];
    }

    public function salvarCampanha(array $data, $contratoId, $campanhaId = null)
    {
        return DB::transaction(function () use ($data, $contratoId, $campanhaId) {
            Log::info('Iniciando salvamento de campanha', ['campanhaId' => $campanhaId, 'contratoId' => $contratoId, 'data' => $data]);
            
            $campanha = $campanhaId
                ? SgcEspeleoCampanha::findOrFail($campanhaId)
                : SgcEspeleoCampanha::where('id_contrato', $contratoId)
                    ->where('subproduto', $data['subproduto'] ?? '')
                    ->where('status', 'Em elaboração')
                    ->first();

            if (!$campanha) {
                $campanha = SgcEspeleoCampanha::create([
                    'id_contrato' => $contratoId,
                    'id_campanha' => '3',
                    'subproduto' => $data['subproduto'] ?? '',
                    'status' => 'Em elaboração',
                ]);
            }

            $campanha->fill($data);
            $campanha->id_contrato = $contratoId;
            $campanha->versao_analise = $campanha->versao_analise ?? 1;
            $campanha->status = 'Em análise';
            $campanha->save();

            // Vincular profissionais
            if (isset($data['profissionais']) && is_array($data['profissionais'])) {
                foreach ($data['profissionais'] as $prof) {
                    Log::info('Tentando vincular profissional', ['prof' => $prof]);
                    SgcEspeleoCampanhaProfissional::create([
                        'campanha_id' => $campanha->id,
                        'id_modulo' => $prof['id_modulo'] ?? null,
                        'id_contrato' => $contratoId,
                        'profissional_id' => $prof['profissional_id'],
                    ]);
                }
            }

            // Vincular justificativas
            if (isset($data['justificativas']) && is_array($data['justificativas'])) {
                foreach ($data['justificativas'] as $just) {
                    Log::info('Criando justificativa', ['justificativa' => $just]);
                    $tipo = $just['tipo'] ?? 'complementar'; 
                    SgcEspeleoJustificativa::create([
                        'campanha_id' => $campanha->id,
                        'codigo_sei' => $just['codigo_sei'] ?? null,
                        'id_contrato' => $contratoId,
                        'titulo' => $just['titulo'] ?? null,
                        'justificativa' => $just['justificativa'] ?? '',
                        'tipo' => $tipo,
                    ]);
                }
            } else {
                // Fallback obsoleto, removido para alinhamento com o novo formato
                Log::warning('Formato de justificativas não encontrado, esperando array "justificativas"');
            }

            // Salvar metodologia se fornecida
            if (isset($data['metodologia']) && !empty($data['metodologia'])) {
                // Remove se já existir uma para esta campanha (para update)
                SgcEspeleoMetodologia::where('campanha_id', $campanha->id)->delete();
                
                SgcEspeleoMetodologia::create([
                    'campanha_id' => $campanha->id,
                    'id_contrato' => $contratoId,
                    'titulo' => $data['titulo_metodologia'] ?? null,
                    'metodologia' => $data['metodologia'],
                ]);
                Log::info('Metodologia salva', ['campanha_id' => $campanha->id, 'metodologia_length' => strlen($data['metodologia'])]);
            }

            if (isset($data['resultados_anexos']) && is_array($data['resultados_anexos'])) {
                foreach ($data['resultados_anexos'] as $anexoData) {
                    SgcEspeleoResultadoAnexo::updateOrCreate(
                        ['id' => $anexoData['id'] ?? null],
                        [
                            'campanha_id' => $campanha->id,
                            'id_contrato' => $contratoId,
                            'nome_arquivo' => $anexoData['nome_arquivo'],
                            'caminho' => $anexoData['caminho'],
                            'tipo' => 'shapefile',
                        ]
                    );
                }
            }

            Log::info('Campanha salva', ['id' => $campanha->id]);
            return $campanha;
        });
    }
}