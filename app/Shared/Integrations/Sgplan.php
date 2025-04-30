<?php

declare(strict_types=1);

namespace App\Shared\Integrations;

use App\Models\Rodovia;
use App\Models\Uf;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Location\Coordinate;
use Location\Formatter\Polyline\GeoJSON;
use Location\Polyline;
use Location\Processor\Polyline\SimplifyDouglasPeucker;
use Throwable;

class Sgplan
{

    private PendingRequest $clientSgplan;
    private PendingRequest $clientApiGeo;


    public function __construct()
    {
        $this->clientSgplan = Http::baseUrl(config('api-geo.sgplan'))
            ->withoutVerifying()
            ->withHeaders(['Accept' => 'application/json'])
            ->retry(3, 500);

        $this->clientApiGeo = Http::baseUrl(config('api-geo.url'))
            ->withoutVerifying()
            ->withHeaders(['Accept' => 'application/json'])
            ->retry(3, 500);
    }

    public function tipoPorUfBr(
        int|string $uf,
        int|string $rodovia,
        ?string    $versaoSNV = null
    ) {
        $payload = [
            "uf" => is_string($uf) ? $uf : Uf::find($uf)->uf,
            "br" => str_replace(' ', '', is_string($rodovia) ? $rodovia : Rodovia::find($rodovia)->rodovia),
            "data" => $versaoSNV ?? now()->format('Y-m-d')
        ];

        try {
            $response = $this->clientSgplan->get('listartipoporbruf', $payload);

            return $response->json();
        } catch (Throwable $th) {
            return $th;
        }
    }

    public function codigoPorUfBrTipo(
        int|string $uf,
        int|string $rodovia,
        string     $tipoTrecho,
        ?string    $versaoSNV = null
    ) {
        $payload = [
            "uf" => is_string($uf) ? $uf : Uf::find($uf)->uf,
            "br" => str_replace(' ', '', is_string($rodovia) ? $rodovia : Rodovia::find($rodovia)->rodovia),
            "tipo" => $tipoTrecho,
            "data" => $versaoSNV ?? now()->format('Y-m-d')
        ];

        try {
            $response = $this->clientSgplan->get('listarcdportipobruf', $payload);

            return $response->json();
        } catch (Throwable $th) {
            return $th;
        }
    }

    public function getGeoJsonLinestring(
        int|string $uf,
        int|string $rodovia,
        float      $kmInicial,
        float      $kmFinal,
        ?string    $tipoTrecho = 'B',
        ?string    $cod_tipo = 'null',
        ?bool      $simplificar = true,
        ?string    $versaoSNV = null
    ): string {

        $payload = [
            "uf" => is_string($uf) ? $uf : Uf::find($uf)->uf,
            "br" => str_replace(' ', '', is_string($rodovia) ? $rodovia : Rodovia::find($rodovia)->rodovia),
            "kmi" => $kmInicial,
            "kmf" => $kmFinal,
            "tipo" => $tipoTrecho,
            "cd_tipo" => $cod_tipo ?? 00,
            "data" => $versaoSNV ?? now()->format('Y-m-d')
        ];

        try {
            $response = $this->clientApiGeo->get('espacializarlinha', $payload);

            $geojson = $response->json();

            if ($simplificar && count($geojson['geometry']['coordinates']) === 1) {
                return $this->simplifyLinestring($geojson['geometry']['coordinates'][0]);
            }

            return json_encode($geojson);
        } catch (Throwable $th) {
            Log::error("Falha na espacialização: " . $th->getMessage());
            throw $th;
        }
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
}
