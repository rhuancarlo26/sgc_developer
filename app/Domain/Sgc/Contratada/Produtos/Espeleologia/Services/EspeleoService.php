<?php

namespace App\Domain\Sgc\Contratada\Produtos\Espeleologia\Services;

use App\Models\SgcEspeleoCampanha;
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
            
            Log::info('Campanha salva', ['id' => $campanha->id]);
            return $campanha;
        });
    }
}