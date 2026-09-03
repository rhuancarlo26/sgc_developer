<?php

namespace App\Domain\Sgc\Contratada\Produtos\Patrimonio\App\Services;

use App\Domain\Licenca\Shapefile\Services\LicencaShapefileService;
use App\Domain\Sgc\Contratada\Produtos\Patrimonio\App\Contracts\ShapefileProcessorInterface;
use RuntimeException;
use Illuminate\Http\UploadedFile;

class PatrimonioShapefileProcessor implements ShapefileProcessorInterface
{
  public function __construct(private LicencaShapefileService $shapeFileService)
  {

  }

  public function process(UploadedFile $file): string
  {
    $geoJson = $this->shapeFileService->getFeatureCollection($file);

    if (!$geoJson) {
      throw new RuntimeException('Não foi possível processar o shapefile envia');
    }
    return $geoJson;
  }
}
