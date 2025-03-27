<?php

namespace App\Domain\Contrato\GestaoContrato\Services;

use App\Models\contratoTrecho;
use App\Models\SvnSegGeoV2;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use DB;

class TrechoContratoService extends BaseModelService
{
  use Deletable;

  protected string $modelClass = contratoTrecho::class;

  public function create($request)
  {
    $geometry = null;
    $coordenada = $this->getCoordenada($request);

    if ($coordenada) {
      $geometry = DB::raw("ST_GeomFromGeoJSON(?)", [$coordenada]);
    }

    return $this->dataManagement->create(entity: $this->modelClass, infos: [
      ...$request,
      'uf_id' => $request['uf']['id'],
      'rodovia_id' => $request['rodovia']['id'],
      'coordenada' => $coordenada,
      'geometria' => $geometry
    ]);
  }

  public function update($request)
  {
    $coordenada = $this->getCoordenada($request);
    $coordenada = json_encode(json_decode($coordenada, true), JSON_UNESCAPED_UNICODE);

    $geometry = $coordenada ? DB::raw("ST_GeomFromGeoJSON(?)", [$coordenada]) : null;

    return $this->dataManagement->update(entity: $this->modelClass, infos: [
      ...$request,
      'uf_id' => $request['uf']['id'],
      'rodovia_id' => $request['rodovia']['id'],
      'coordenada' => $coordenada,
      'geometria' => $geometry
    ], id: $request['id']);
  }

  public function getCoordenada($request)
  {
    return SvnSegGeoV2::getGeoJson(
      $request['uf']['uf'],
      $request['rodovia']['rodovia'],
      $request['km_inicial'],
      $request['km_final'],
      $request['tipo_trecho'] ?? 'B'
    );
  }
}