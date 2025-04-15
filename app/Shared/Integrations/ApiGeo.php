<?php

declare(strict_types=1);

namespace App\Shared\Integrations;

use App\Models\Rodovia;
use App\Models\SNVGeo;
use App\Models\Uf;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Location\Coordinate;
use Location\Formatter\Polyline\GeoJSON;
use Location\Polyline;
use Location\Processor\Polyline\SimplifyDouglasPeucker;
use Throwable;

class ApiGeo
{

    private PendingRequest $client;


    public function __construct()
    {
        $this->client = Http::baseUrl(config('api-geo.url'))
            ->withoutVerifying()
            ->withHeaders(['Accept' => 'application/json'])
            ->retry(3, 500);
    }

    private function simplifyLinestring(array $coordinates): string
    {
        $polyline = new Polyline();

        foreach ($coordinates as $coord) {
            $polyline->addPoint(new Coordinate((float)$coord[1], (float)$coord[0]));
        };

        $processor = new SimplifyDouglasPeucker(50);
        $simplified = $processor->simplify($polyline);

        return $simplified->format(new GeoJSON);
    }

    public function getGeoJsonLinestring(
        int|string $uf,
        int|string $rodovia,
        float      $kmInicial,
        float      $kmFinal,
        ?string    $tipoTrecho = 'B',
        ?bool      $simplificar = true,
        ?string    $versaoSNV = null
    ): string
    {

        $payload = [
            "uf" => is_string($uf) ? $uf : Uf::find($uf)->uf,
            "br" => is_string($rodovia) ? $rodovia : Rodovia::find($rodovia)->rodovia,
            "kmi" => $kmInicial,
            "kmf" => $kmFinal,
            "tipo" => $tipoTrecho ?? 'B',
            "data" => $versaoSNV ?? now()->format('Y-m-d')
        ];

        try {
            $response = $this->client->get('espacializarlinha', [
                ...$payload, ...["cd_tipo" => 'null', "data" => now()->format('Y-m-d')]
            ]);

            $geojson = $response->json();

            if ($simplificar && count($geojson['geometry']['coordinates']) === 1) {
                return $this->simplifyLinestring($geojson['geometry']['coordinates'][0]);
            }

            return json_encode($geojson);
        } catch (Throwable $th) {
            unset($payload['data']);
            return SNVGeo::getGeoJson(...array_values($payload));
        }
    }
}
