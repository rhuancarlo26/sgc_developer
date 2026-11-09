<?php

namespace App\Domain\Sgc\Contratada\Produtos\Fauna\Services;

use App\Models\SgcFaunaModuloAmostral;
use Illuminate\Support\Facades\Log;

class ModuloAmostralService
{
    public function salvarModulosAmostrais($contratoId, $campanhaId, array $modulos)
    {
        if (!empty($modulos)) {
            $modulosEnviadosIds = collect($modulos)->pluck('id')->filter()->toArray();
            SgcFaunaModuloAmostral::where('campanha_id', $campanhaId)
                ->whereNotIn('id', $modulosEnviadosIds)
                ->delete();

            foreach ($modulos as $moduloData) {
                $modulo = array_filter([
                    'id_contrato' => $contratoId,
                    'data_cadastro' => $moduloData['data_cadastro'] ?? null,
                    'tamanho_modulo' => $moduloData['tamanho_modulo'] ?? null,
                    'uf' => $moduloData['uf'] ?? null,
                    'municipio' => $moduloData['municipio'] ?? null,
                    'bioma' => $moduloData['bioma'] ?? null,
                    'fitofisionomia' => $moduloData['fitofisionomia'] ?? null,
                    'latitude_inicial' => $moduloData['latitude_inicial'] ?? null,
                    'longitude_inicial' => $moduloData['longitude_inicial'] ?? null,
                    'latitude_final' => $moduloData['latitude_final'] ?? null,
                    'longitude_final' => $moduloData['longitude_final'] ?? null,
                    'obs' => $moduloData['obs'] ?? null,
                ]);

                $moduloModel = SgcFaunaModuloAmostral::updateOrCreate(
                    [
                        'id' => isset($moduloData['id']) ? (int) $moduloData['id'] : null,
                        'campanha_id' => $campanhaId,
                    ],
                    $modulo
                );

                if (isset($moduloData['arquivo']) && $moduloData['arquivo'] && $moduloData['arquivo']->isValid()) {
                    $filename = time() . '_' . $moduloData['arquivo']->getClientOriginalName();
                    $path = $moduloData['arquivo']->storeAs('shapefiles', $filename, 'public');
                    $moduloModel->update(['nome_arquivo' => $filename]);
                }
            }
        }
    }
}