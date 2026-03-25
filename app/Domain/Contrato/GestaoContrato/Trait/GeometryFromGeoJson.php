<?php

namespace App\Domain\Contrato\GestaoContrato\Trait;

use Illuminate\Database\Eloquent\Model;


trait GeometryFromGeoJson
{
  public static function bootGeometryFromGeoJSON(): void
  {
    $setGeometry = function (Model $model) {
      [$geometryColumn, $geoJSONColumn] = [$model::$geometryColumn ?? 'geometria', $model::$geoJSONColumn ?? 'coordenada'];

      if ($model->$geoJSONColumn) {
        $model->$geometryColumn = \DB::raw("ST_GeomFromGeoJSON('{$model->$geoJSONColumn}')");
      }
    };

    static::updating($setGeometry);
    static::creating($setGeometry);
  }
}