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
}
