<?php

namespace App\Shared\Integrations\GoogleRoutes;

class RouteDTO
{
    public function __construct(
        public readonly string $geoJson,
        public readonly array $originCoordinates,
        public readonly array $destinationCoordinates,
        public readonly ?float $distanceInMeters,
        public readonly ?string $esimatedDuration
    ) {
    }

    /**
     * Transforma objeto em array associativo
     */
    public function toArray(): array
    {
        return (array) $this;
    }
}
