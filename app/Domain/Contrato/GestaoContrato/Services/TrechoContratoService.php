<?php

namespace App\Domain\Contrato\GestaoContrato\Services;

use App\Models\contratoTrecho;
use App\Models\SvnSegGeoV2;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use Illuminate\Support\Facades\DB;

class TrechoContratoService extends BaseModelService
{
  use Deletable;

  protected string $modelClass = contratoTrecho::class;

  public function create($request)
  {
    $geometry = null;
    $coordenada = $request['coordenada'] ?? null;

    if ($coordenada) {
      $geometry = DB::raw("ST_GeomFromGeoJSON(?)", [$coordenada]);
    }

    return $this->dataManagement->create(entity: $this->modelClass, infos: [
      ...$request,
      'uf_id' => $request['uf_id'],
      'rodovia_id' => $request['rodovia_id'],
      'coordenada' => $coordenada,
      'geometria' => $geometry
    ]);
  }

  public function update($request)
  {
    $coordenada = $request['coordenada'] ?? null;

    $geometry = $coordenada ? DB::raw("ST_GeomFromGeoJSON(?)", [$coordenada]) : null;

    return $this->dataManagement->update(entity: $this->modelClass, infos: [
      ...$request,
      'uf_id' => $request['uf_id'],
      'rodovia_id' => $request['rodovia_id'],
      'coordenada' => $coordenada,
      'geometria' => $geometry
    ], id: $request['id']);
  }

  /**
   * Após a alteração para consumir a coordenada da API SGPLAN do DNIT
   * não se faz necessário mais essa função.
   * Entretanto, por não saber quais outros módulos podem usar essa função,
   * optei por deixá-la ativa.
   */
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
