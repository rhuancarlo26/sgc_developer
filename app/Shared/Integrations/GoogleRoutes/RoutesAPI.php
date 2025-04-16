<?php

namespace App\Shared\Integrations\GoogleRoutes;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Location\{Coordinate, Polyline};
use Location\Formatter\Polyline\GeoJSON;


class RoutesAPI
{
    /**
     * Resposta da API
     * @var array
     */
    private array $apiResponse;

    /**
     * Endereço de origem
     * @var string
     */
    private string $originAddress;

    /**
     * Endereço de destino
     * @var string
     */
    private string $destinationAddress;

    /**
     * Array com pares de coordenadas da rota
     * @var array
     */
    private array $decodedPolyline;

    /**
     * Transforma string polyline em array com pares de coordenadas.
     * *Adaptado de https://github.com/emcconville/google-map-polyline-encoding-tool*
     * @param string $string String com Polyline codificada
     * @return array
     */
    private function decodePolylineString(string $string, int $precision = 5): array
    {
        $points = [];
        $index = $i = 0;
        $previous = [0, 0];

        while ($i < strlen($string)) {

            $shift = $result = 0x00;

            do {
                $bit = ord(substr($string, $i++)) - 63;
                $result |= ($bit & 0x1f) << $shift;
                $shift += 5;
            } while ($bit >= 0x20);

            $diff = ($result & 1) ? ~($result >> 1) : ($result >> 1);
            $number = $previous[$index % 2] + $diff;
            $previous[$index % 2] = $number;
            $index++;
            $points[] = $number * 1 / pow(10, $precision);
        }

        return array_chunk($points, 2);
    }

    /**
     * Define endereços de origem e destino
     * @param string $originAddress Endereço de origem
     * @param string $destinationAddress Endereço de destino
     * @return self
     */
    public function setOriginAndDestination(string $originAdress, string $destinationAddress): self
    {
        $this->originAddress = $originAdress;
        $this->destinationAddress = $destinationAddress;

        return $this;
    }


    /**
     * Envia requisição p/ API que faz cálculo das rotas
     * @param string $travelMode Modos possíveis: https://developers.google.com/maps/documentation/routes/reference/rest/v2/RouteTravelMode?hl=pt-br
     * @param ?string $departureDateTime Hora de partida
     * @throws Exception Lança exceção quando uma rota não é encontrada
     * @return self
     */
    public function calculateRouteWithAddress(string $travelMode = 'DRIVE', ?string $departureDateTime = null): self
    {
        $this->apiResponse = Http::withoutVerifying()->withHeaders([
            'X-Goog-Api-Key' => config('google.api_key'),
            'Content-Type' => 'application/json',
            'X-Goog-FieldMask' => 'routes.duration,routes.distanceMeters,routes.polyline'
        ])->post(
            'https://routes.googleapis.com/directions/v2:computeRoutes',
            [
                'origin' => ['address' => $this->originAddress],
                'destination' => ['address' => $this->destinationAddress],
                'travelMode' => $travelMode,
                'departureTime' => $departureDateTime ? Carbon::parse($departureDateTime)->toIso8601ZuluString() : null
            ]
        )->json();

        if (!isset($this->apiResponse['routes'])) {
            Log::alert("Não foi possível encontrar rotas para os endereços", (array) $this);
            throw new Exception("Não foi possível encontrar rotas para os endereços");
        }

        $this->decodedPolyline = $this->decodePolylineString(($this->apiResponse['routes'][0]['polyline']['encodedPolyline']));

        return $this;
    }

    /**
     * Envia requisição p/ API que faz cálculo das rotas
     * @param string $travelMode Modos possíveis: https://developers.google.com/maps/documentation/routes/reference/rest/v2/RouteTravelMode?hl=pt-br
     * @param ?string $departureDateTime Hora de partida
     * @throws Exception Lança exceção quando uma rota não é encontrada
     * @return self
     */
    public function calculateRouteWithCoordinates(string $travelMode = 'DRIVE', ?string $departureDateTime = null): self
    {
        // Extrair latitude e longitude das strings
        list($originLat, $originLng) = explode(',', $this->originAddress);
        list($destLat, $destLng) = explode(',', $this->destinationAddress);

        $this->apiResponse = Http::withoutVerifying()->withHeaders([
            'X-Goog-Api-Key' => config('google.api_key'),
            'Content-Type' => 'application/json',
            'X-Goog-FieldMask' => 'routes.duration,routes.distanceMeters,routes.polyline'
        ])->post(
            'https://routes.googleapis.com/directions/v2:computeRoutes',
            [
                'origin' => [
                    'location' => [
                        'latLng' => [
                            'latitude' => (float) $originLat,
                            'longitude' => (float) $originLng
                        ]
                    ]
                ],
                'destination' => [
                    'location' => [
                        'latLng' => [
                            'latitude' => (float) $destLat,
                            'longitude' => (float) $destLng
                        ]
                    ]
                ],
                'travelMode' => $travelMode,
                'departureTime' => $departureDateTime ? Carbon::parse($departureDateTime)->toIso8601ZuluString() : null
            ]
        )->json();

        if (!isset($this->apiResponse['routes'])) {
            Log::alert("Não foi possível encontrar rotas para os endereços", (array) $this);
            throw new Exception("Não foi possível encontrar rotas para os endereços");
        }

        $this->decodedPolyline = $this->decodePolylineString(($this->apiResponse['routes'][0]['polyline']['encodedPolyline']));

        return $this;
    }


    /**
     * Retorna objeto com dados da rota
     * @return ?RouteDTO
     */
    public function get(): ?RouteDTO
    {
        if (!isset($this->apiResponse)) {
            return null;
        }

        return new RouteDTO(
            geoJson: $this->getRouteGeoJson(),
            originCoordinates: $this->getOriginCoordinates(),
            destinationCoordinates: $this->getDestinationCoordinates(),
            distanceInMeters: $this->apiResponse['routes'][0]['distanceMeters'] ?? null,
            esimatedDuration: $this->apiResponse['routes'][0]['duration'] ?? null
        );
    }


    /**
     * Retorna coordenadas do endereço de origem (longitude, latitude)
     * @return ?array
     */
    public function getOriginCoordinates(): ?array
    {
        return isset($this->decodedPolyline) ? $this->decodedPolyline[0] : null;
    }

    /**
     * Retorna coordenadas do endereço de destino (longitude, latitude)
     * @return ?array
     */
    public function getDestinationCoordinates(): ?array
    {
        return isset($this->decodedPolyline) ? end($this->decodedPolyline) : null;
    }

    /**
     * Retorna GeoJSON da rota calculada
     * @return ?string
     */
    public function getRouteGeoJson(): ?string
    {
        if (!isset($this->decodedPolyline)) {
            return null;
        }

        $polyline = new Polyline();

        // caso tenha somente um array, duplique com um milesimo a mais nas casas decimais
        if (count($this->decodedPolyline) == 1) {
            array_push(
                $this->decodedPolyline,
                [
                    $this->decodedPolyline[0][0] + 0.00001,
                    $this->decodedPolyline[0][1] + 0.00001
                ]
            );
        }

        foreach ($this->decodedPolyline as $coords) {
            $polyline->addPoint(new Coordinate($coords[0], $coords[1]));
        }

        return $polyline->format(new GeoJSON());
    }

    /**
     * Retorna array associativo com resposta da API Google
     * @return array
     */
    public function getRawApiResponse(): array
    {
        if (isset($this->apiResponse)) {
            return $this->apiResponse;
        }

        return [];
    }
}
