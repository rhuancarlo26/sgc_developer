<?php

namespace App\Domain\Servico\MonitoraFauna\Configuracao\ModuloAmostral\Services;

use App\Domain\Licenca\Shapefile\Services\LicencaShapefileService;
use App\Models\ServicoMonitoraFaunaConfigModuloAmostral;
use App\Models\Servicos;
use App\Models\Uf;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Searchable;
use Cache;
use File;
use Shapefile\ShapefileException;
use Shapefile\ShapefileReader;
use ZipArchive;

class ModuloAmostralService extends BaseModelService
{
  use Searchable;

  protected string $modelClass = ServicoMonitoraFaunaConfigModuloAmostral::class;

  public function index(Servicos $servico, array $searchParams): array
  {
    return [
      'modulos' => $this->searchAllColumns(...$searchParams)
        ->where('id_servico', $servico->id)
        ->paginate()
        ->appends($searchParams),
      'ufs' => Cache::rememberForever('ufs', fn() => Uf::all())
    ];
  }

  public function store(array $post)
  {
    $post['nome_arquivo'] = null;
    $post['shape_file'] = null;

    if (isset($post['arquivo'])) {
      $post['nome_arquivo'] = $post['arquivo']->getClientOriginalName();
      $post['shape_file'] = $this->getFeatureCollection($post['arquivo']);
    }

    return $this->dataManagement->create($this->modelClass, $post);
  }

  public function update(array $post)
  {
    $post['nome_arquivo'] = null;
    $post['shape_file'] = null;

    if (isset($post['arquivo'])) {
      $post['nome_arquivo'] = $post['arquivo']->getClientOriginalName();
      $post['shape_file'] = $this->getFeatureCollection($post['arquivo']);
    }

    return $this->dataManagement->update(entity: $this->modelClass, infos: $post, id: $post['id']);
  }

  public function destroy(object $modulo)
  {
    return $this->dataManagement->delete(entity: $this->modelClass, id: $modulo->id);
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

          $folderPath = storage_path('app' . DIRECTORY_SEPARATOR . 'shapefile');

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
