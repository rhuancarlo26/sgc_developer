<?php

namespace App\Domain\Sgc\Contratada\Ficha\Services;

use Illuminate\Support\Facades\Http;

class FichaService
{
    public function getFichaData($contratoId)
    {
        try {
            $apiUrl = "https://servicos.dnit.gov.br/DPP/api/contrato/dnit/{$contratoId}";
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