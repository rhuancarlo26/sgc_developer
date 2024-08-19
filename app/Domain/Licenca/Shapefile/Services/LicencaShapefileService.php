<?php

namespace App\Domain\Licenca\Shapefile\Services;

use App\Models\Licenca;
use App\Models\LicencaShapefile;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Shapefile\ShapefileException;
use Shapefile\ShapefileReader;
use ZipArchive;

class LicencaShapefileService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = Licenca::class;

    public function store($request)
    {
        $post = [
            'licenca_id' => $request['licenca_id'],
            'nome_file_shape' => $request['shapefile']->getClientOriginalName(),
            'geo_json' => $this->getFeatureCollection($request['shapefile'])
        ];
        
        $shapefile = $this->dataManagement->update(entity: $this->modelClass, infos: $post, id: $post['licenca_id']);

        return [
            'request' => $shapefile['request']
        ];
    }

    public function getFeatureCollection($file)
    {
        $zip = new ZipArchive;

        $tempPath = $file->storeAs('shapefile', $file->getClientOriginalName());
        $extractPath = storage_path('app' . DIRECTORY_SEPARATOR . 'shapefile' . DIRECTORY_SEPARATOR . pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));

        if ($zip->open(storage_path('app' . DIRECTORY_SEPARATOR . $tempPath)) === TRUE) {
            $zip->extractTo($extractPath);
            $zip->close();

            $shapefilePath = null;

            $files = scandir($extractPath);

            foreach ($files as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) == 'shp') {
                    $shapefilePath = $extractPath . '/' . $file;
                    break;
                }
            }

            if ($shapefilePath) {
                try {
                    $Shapefile = new ShapefileReader($shapefilePath);

                    $features = [];

                    while ($record = $Shapefile->fetchRecord()) {
                        if ($record->isDeleted()) {
                            continue;
                        }

                        $geometry = $record->getGeoJSON();
                        $attributes = (object)$record->getDataArray();

                        $features[] = [
                            'type' => 'Feature',
                            'geometry' => json_decode($geometry, true),
                            'properties' => $attributes
                        ];
                    }

                    $featureCollection = [
                        'type' => 'FeatureCollection',
                        'features' => $features
                    ];

                    $folderPath = storage_path('app' . DIRECTORY_SEPARATOR . 'shapefile'); // Caminho da pasta

                    if (File::exists($folderPath)) {
                        File::deleteDirectory($folderPath);
                    }

                    return json_encode($featureCollection);
                } catch (ShapefileException $e) {
                }
            }
        }
    }
}
