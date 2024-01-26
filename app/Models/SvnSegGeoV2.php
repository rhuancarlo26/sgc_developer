<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
    public static function getGeoJson(
        string $UF,
        int    $rodovia,
        float  $km_inicial,
        float  $km_final,
        string $tipo_trecho = 'B',
        bool   $simplificar = true
    ): string {

        $segmentos = self::select(['Lat', 'Lng'])->where([
            ['UF', '=', strtoupper($UF)],
            ['BR', '=', $rodovia],
            ['TipoTrecho', strtoupper($tipo_trecho)],
            ['KmInicial', '>=', $km_inicial],
            ['KmFinal', '<=', $km_final],
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
}