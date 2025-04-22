<?php

namespace App\Domain\Sgc\Contratada\Coordenadas\Services;

use App\Shared\Abstract\BaseModelService;

// Service otimizado
class CoordenadasServices
{

    private function normalizarParaArray(array|string $input): array
    {
    // Se já for array, retorne diretamente
        if (is_array($input)) {
            return $input;
        }
    
    // Se for string, decodifique o JSON
        if (is_string($input)) {
            $decoded = json_decode($input, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \InvalidArgumentException("JSON inválido: " . json_last_error_msg());
            }
            
            if (!is_array($decoded)) {
                throw new \InvalidArgumentException("O JSON decodificado não é um array");
            }
            
            return $decoded;
        }
    
        throw new \InvalidArgumentException("Entrada deve ser array ou string JSON");
    }

    public function processarGeoJson(array|string $geoJson): array
    {
        $dados = $this->normalizarParaArray($geoJson);

        if (!isset($dados['type']) || $dados['type'] !== 'LineString') {
            throw new \InvalidArgumentException("Tipo GeoJSON deve ser LineString");
        }

        if (empty($dados['coordinates']) || !is_array($dados['coordinates'])) {
            throw new \InvalidArgumentException("Coordenadas inválidas");
        }

        // Remove pontos duplicados consecutivos
        $coordenadas = $this->removerDuplicados($dados['coordinates']);
        
        // Ordena por proximidade geográfica
        $coordenadas = $this->ordenarPorProximidade($coordenadas);

        return [
            'type' => 'LineString',
            'coordinates' => $coordenadas
        ];
    }

    private function removerDuplicados(array $coordenadas): array
    {
        return array_reduce($coordenadas, function($acumulado, $atual) {
            if (empty($acumulado) || $atual !== end($acumulado)) {
                $acumulado[] = $atual;
            }
            return $acumulado;
        }, []);
    }

    private function ordenarPorProximidade(array $coordenadas): array
    {
        if (count($coordenadas) <= 1) {
            return $coordenadas;
        }

        $ordenadas = [array_shift($coordenadas)];
        
        while (!empty($coordenadas)) {
            $ultima = end($ordenadas);
            $indiceMaisProximo = $this->encontrarMaisProximo($ultima, $coordenadas);
            $ordenadas[] = $coordenadas[$indiceMaisProximo];
            array_splice($coordenadas, $indiceMaisProximo, 1);
        }

        return $ordenadas;
    }

    private function encontrarMaisProximo(array $referencia, array $coordenadas): int
    {
        $indiceMaisProximo = 0;
        $menorDistancia = PHP_FLOAT_MAX;

        foreach ($coordenadas as $i => $coord) {
            $distancia = $this->calcularDistancia($referencia, $coord);
            if ($distancia < $menorDistancia) {
                $menorDistancia = $distancia;
                $indiceMaisProximo = $i;
            }
        }

        return $indiceMaisProximo;
    }

    private function calcularDistancia(array $coord1, array $coord2): float
    {
        [$lon1, $lat1] = $coord1;
        [$lon2, $lat2] = $coord2;
        $R = 6371; // Raio da Terra em km
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;
        return $R * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}