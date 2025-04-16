<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Location\Coordinate;
use Location\Formatter\Polyline\GeoJSON;
use Location\Polyline;
use Location\Processor\Polyline\SimplifyDouglasPeucker;

class SNVGeo extends Model
{
    protected $table = 'snv_seg_geo_v2';

    /**
     * Retorna o geoJSON LineString do trecho|segmento de acordo com os parametros informados.
     * @param string $UF UF da Rodovia
     * @param int $rodovia Numero da Rodovia | BR
     * @param float $KmInicial KM Inicial do trecho
     * @param float $KmFinal KM Final do trecho
     * @param string $TipoTrecho
     * @param bool $simplificar
     * @return string String geoJSON
     */
    public static function getGeoJson(
        string $UF,
        string $rodovia,
        float  $KmInicial,
        float  $KmFinal,
        string $TipoTrecho = 'B',
        bool   $simplificar = true
    ): string
    {

        $segmentos = self::select(['Lat', 'Lng'])->where([
            ['UF', '=', strtoupper($UF)],
            ['BR', '=', $rodovia],
            ['TipoTrecho', strtoupper($TipoTrecho)],
            ['KmInicial', '>=', $KmInicial],
            ['KmFinal', '<=', $KmFinal],
        ])->orderBy('KmInicial')->cursor();

        $polyline = new Polyline();

        foreach ($segmentos as $segmento) {
            $polyline->addPoint(new Coordinate((float)$segmento->Lat, (float)$segmento->Lng));
        };


        if ($simplificar) {
            $processor = new SimplifyDouglasPeucker(50);
            $simplified = $processor->simplify($polyline);
            return $simplified->format(new GeoJSON);
        }

        return $polyline->format(new GeoJSON);
    }

    /**
     * Retorna latitude e longitude de acordo com os parametros informados
     * @param string $UF UF da Rodovia
     * @param int $rodovia Numero da Rodovia | BR
     * @param float $km KM  do trecho
     * @param string $TipoTrecho
     * @return array String geoJSON
     */
    public static function getLatLng(
        string $UF,
        int    $rodovia,
        float  $km,
        string $TipoTrecho = 'B'
    ): array
    {

        $segmento = self::select(['Lat', 'Lng'])->where([
            ['UF', '=', strtoupper($UF)],
            ['BR', '=', $rodovia],
            ['TipoTrecho', strtoupper($TipoTrecho)],
        ])
            ->orderBy('KmInicial')
            ->whereRaw("$km BETWEEN KmInicial and KmFinal")
            ->first();

        return [$segmento?->Lat, $segmento?->Lng];
    }
}
