<?php
namespace App\Domain\Sgc\Contratada\Produtos\Patrimonio\App\Contracts;

use App\Models\SgcPatrimonioShapefile;

interface ShapefileRepositoryInterface
{
  public function salvar(int $patrimonioId, string $nomeCampo, string $geoJson): SgcPatrimonioShapefile;
}
