<?php

namespace App\Shared\Traits;

use App\Domain\Licenca\Shapefile\Services\LicencaShapefileService;
use App\Shared\Utils\DataManagement;
use Illuminate\Http\UploadedFile;

trait ShapefileHandler
{
    private function handleShapefile(array &$request, string $fileKey = 'shapefile', string $localKey = 'local_shape'): void
    {
        $shapefile = $request[$fileKey];
        if (!($shapefile instanceof UploadedFile)) return;
        try {
            $licencaShapefileService = new LicencaShapefileService(new DataManagement());
            $shape = $licencaShapefileService->getFeatureCollection(file: $shapefile);
            $request[$fileKey] = $shape;
            $path = storage_path('app' . DIRECTORY_SEPARATOR . 'file_shape' . DIRECTORY_SEPARATOR . uniqid() . '.json');
            file_put_contents($path, $shape);
            $request[$localKey] = $path;
        } catch (\Exception $e) {
        }
    }
}
