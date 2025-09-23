<?php

namespace App\Domain\Sgc\Contratada\Produtos\Espeleologia\Services;

use App\Models\SgcEspeleoCampanha;
use App\Models\SgcEspeleoCampanhaProfissional;
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
                : new SgcEspeleoCampanha();
            
            $campanha->fill($data);
            $campanha->id_contrato = $contratoId;
            $campanha->versao_analise = $campanha->versao_analise ?? 1;
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
            } else {
                Log::warning('Nenhum profissional encontrado no payload', ['data' => $data]);
            }
            
            Log::info('Campanha salva', ['id' => $campanha->id]);
            return $campanha;
        });
    }
    

}