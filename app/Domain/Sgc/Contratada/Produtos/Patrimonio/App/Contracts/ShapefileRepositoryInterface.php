<?php
namespace App\Domain\Sgc\Contratada\Produtos\Patrimonio\App\Contracts;

use App\Models\SgcPatrimonioShapefile;

interface ShapefileRepositoryInterface
{
  public function salvar(int $patrimonioId, string $nomeCampo, string $geoJson): SgcPatrimonioShapefile;

  public function salvarLayer(
    int $patrimonioId,
    string $nomeCampo,
    string $workspace,
    string $datastore,
    string $layerName,
    string $storagePath
  ): SgcPatrimonioShapefile;
}
