<?php

namespace App\Domain\Sgc\Contratada\Produtos\Patrimonio\App\Services;

use App\Domain\Sgc\Contratada\Produtos\Patrimonio\App\Contracts\ShapefileRepositoryInterface;
use App\Models\SgcPatrimonioShapefile;

class PatrimonioShapefileRepository implements ShapefileRepositoryInterface
{
  public function salvar(int $patrimonioId, string $nomeCampo, string $geoJson): SgcPatrimonioShapefile
  {
    return SgcPatrimonioShapefile::updateOrCreate(
      ['patrimonio_paipa_id' => $patrimonioId, 'nome_campo' => $nomeCampo],
      ['geo_json' => $geoJson]
    );
  }

  public function salvarLayer(
    int $patrimonioId,
    string $nomeCampo,
    string $workspace,
    string $datastore,
    string $layerName,
    string $storagePath
  ): SgcPatrimonioShapefile {
    return SgcPatrimonioShapefile::updateOrCreate(
      ['patrimonio_paipa_id' => $patrimonioId, 'nome_campo' => $nomeCampo],
      [
        'geo_json' => ['type' => 'FeatureCollection', 'features' => []],
        'workspace' => $workspace,
        'datastore' => $datastore,
        'layer_name' => $layerName,
        'storage_path' => $storagePath,
        'published_at' => now(),
      ]
    );
  }
}
