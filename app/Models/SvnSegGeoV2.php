<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Location\Coordinate;
use Location\Formatter\Polyline\GeoJSON;
use Location\Polyline;
use Location\Processor\Polyline\SimplifyDouglasPeucker;

class SvnSegGeoV2 extends Model
{
    use HasFactory;

    protected $table = 'snv_seg_geo_v2';

    /**
     * Retorna o geoJSON LineString do trecho|segmento de acordo com os parametros informados.
     * @param string $UF UF da Rodovia
     * @param int $rodovia Numero da Rodovia | BR
     * @param float $km_inicial KM Inicial do trecho
     * @param float $km_final KM Final do trecho
     * @param string $tipo_trecho
     * @return string String geoJSON
     */
    public static function getGeoJsonFromApi(
        string $UF_inicial,
        int    $rodovia,
        float  $km_inicial,
        float  $km_final,
        string $tipo_trecho = 'B',
        string $data = '2025-04-09',
        bool   $simplificar = true,
    ): string {


        $response = Http::get('https://servicos.dnit.gov.br/sgplan/apigeo/rotas/espacializarlinha', [
            'br' => str_pad($rodovia, 3, '0', STR_PAD_LEFT),
            'tipo' => $tipo_trecho,
            'uf' => strtoupper($UF_inicial),
            'cd_tipo' => 'null',
            'data' => $data,
            'kmi' => $km_inicial,
            'kmf' => $km_final,
        ]);

        if ($response->failed()) {
            return json_encode(['error' => 'Falha ao obter dados da API do DNIT']);
        }

        $apiData = $response->json();
        if (!isset($apiData['geometry']['coordinates']) || !is_array($apiData['geometry']['coordinates'])) {
            return json_encode(['error' => 'Dados de coordenadas inválidos na resposta da API']);
        }
        $polyline = new Polyline();

        foreach ($apiData['geometry']['coordinates'][0] as $coordinate) {
            $polyline->addPoint(new Coordinate((float)$coordinate[1], (float)$coordinate[0]));
        }

        if ($simplificar) {
            $processor = new SimplifyDouglasPeucker(5);
            $simplified = $processor->simplify($polyline);
            return $simplified->format(new GeoJSON);
        }

        return $polyline->format(new GeoJSON);
    }
}