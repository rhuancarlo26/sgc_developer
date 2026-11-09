<?php

namespace App\Domain\Sgc\Contratada\Produtos\Fauna\Services;

use App\Models\SgcFaunaMetodologia;
use Illuminate\Support\Facades\Log;

class MetodologiaService
{
    public function salvarMetodologias($contratoId, $campanhaId, array $metodologias)
    {
        if (!empty($metodologias)) {
            $receivedIds = collect($metodologias)->pluck('id')->filter()->toArray();
            SgcFaunaMetodologia::where('campanha_id', $campanhaId)
                ->whereNotIn('id', $receivedIds)
                ->delete();

            foreach ($metodologias as $metodologiaData) {
                $metodologia = array_filter([
                    'id_contrato' => $contratoId,
                    'grupo_faunistico' => $metodologiaData['grupo_faunistico'] ?? null,
                    'metodologia' => $metodologiaData['metodologia'] ?? null,
                ]);

                SgcFaunaMetodologia::updateOrCreate(
                    [
                        'id' => isset($metodologiaData['id']) ? (int) $metodologiaData['id'] : null,
                        'campanha_id' => $campanhaId,
                    ],
                    $metodologia
                );
            }
        } else {
            SgcFaunaMetodologia::where('campanha_id', $campanhaId)->delete();
        }
    }
}