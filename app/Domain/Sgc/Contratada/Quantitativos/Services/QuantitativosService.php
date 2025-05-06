<?php

namespace App\Domain\Sgc\Contratada\Quantitativos\Services;

use Illuminate\Support\Facades\Http;

class QuantitativosService
{
    public function getQuantitativosData($contratoId)
    {
        try {
            // Ajuste o endpoint da API conforme necessário
            $apiUrl = "https://servicos.dnit.gov.br/DPP/api/contrato/dnit/{$contratoId}/quantitativos";
            $response = Http::get($apiUrl);

            if ($response->successful()) {
                return $response->json();
            } else {
                return ['error' => 'Erro ao acessar a API do DNIT'];
            }
        } catch (\Exception $e) {
            return ['error' => 'Erro na conexão com a API: ' . $e->getMessage()];
        }
    }
}