<?php

namespace App\Domain\Sgc\Contratada\Produtos\Patrimonio\App\Controller;

use App\Domain\Sgc\Contratada\Produtos\Patrimonio\App\Contracts\ShapefileRepositoryInterface;
use App\Domain\Sgc\Contratada\Produtos\Patrimonio\App\Request\StorePatrimonioShapefileRequest;
use App\Domain\Sgc\Contratada\Produtos\Services\GeoServerService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use ZipArchive;

class StorePatrimonioShapefileController extends Controller
{
  public function __construct(
    private ShapefileRepositoryInterface $repository,
    private GeoServerService $geoServer
  )
  {

  }

  public function __invoke(StorePatrimonioShapefileRequest $request): JsonResponse
  {
    try {
      $file = $request->file('shapefile');
      $paipaId = $request->integer('patrimonio_paipa_id');
      $nomeCampo = $request->input('nome_campo');
      $workspace = 'ecossistema';
      $layerName = 'patrimonio_paipa_' . $paipaId . '_' . Str::slug($nomeCampo, '_') . '_' . time();
      $datastore = 'ds_' . $layerName;
      $zipPath = $this->storeZipWithLayerName($file, $paipaId, $layerName);

      $this->geoServer->ensureWorkspace($workspace);
      $this->geoServer->uploadShapefileDatastore(
        $workspace,
        $datastore,
        storage_path("app/{$zipPath}")
      );

      $shapefile = $this->repository->salvarLayer(
        $paipaId,
        $nomeCampo,
        $workspace,
        $datastore,
        $layerName,
        $zipPath
      );

      return response()->json(['success' => true, 'data' => $shapefile]);
    } catch (\RuntimeException $e) {
      return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
    } catch (\Throwable $e) {
      return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
    }
  }

  private function getLayerNameFromZip(string $zipPath): ?string
  {
    $zip = new ZipArchive();

    if ($zip->open($zipPath) !== true) {
      return null;
    }

    for ($i = 0; $i < $zip->numFiles; $i++) {
      $filename = $zip->getNameIndex($i);

      if (strtolower(pathinfo($filename, PATHINFO_EXTENSION)) === 'shp') {
        $zip->close();

        return pathinfo(basename($filename), PATHINFO_FILENAME);
      }
    }

    $zip->close();

    return null;
  }

  private function storeZipWithLayerName(UploadedFile $file, int $paipaId, string $layerName): string
  {
    $sourceZip = new ZipArchive();

    if ($sourceZip->open($file->getRealPath()) !== true) {
      throw new \RuntimeException('Não foi possível abrir o ZIP do shapefile.');
    }

    $sourceShp = $this->getLayerNameFromZip($file->getRealPath());

    if (!$sourceShp) {
      $sourceZip->close();

      throw new \RuntimeException('Nenhum arquivo .shp encontrado no ZIP enviado.');
    }

    $relativeDir = 'shapes/patrimonio/' . $paipaId;
    $absoluteDir = storage_path('app/' . $relativeDir);

    if (!File::exists($absoluteDir)) {
      File::makeDirectory($absoluteDir, 0775, true);
    }

    $relativePath = $relativeDir . '/' . $layerName . '.zip';
    $absolutePath = storage_path('app/' . $relativePath);
    $targetZip = new ZipArchive();

    if ($targetZip->open($absolutePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
      $sourceZip->close();

      throw new \RuntimeException('Não foi possível criar o ZIP normalizado do shapefile.');
    }

    for ($i = 0; $i < $sourceZip->numFiles; $i++) {
      $entryName = $sourceZip->getNameIndex($i);

      if (str_ends_with($entryName, '/')) {
        continue;
      }

      $extension = pathinfo($entryName, PATHINFO_EXTENSION);
      $basename = pathinfo(basename($entryName), PATHINFO_FILENAME);
      $targetName = $basename === $sourceShp && $extension
        ? $layerName . '.' . $extension
        : basename($entryName);

      $targetZip->addFromString($targetName, $sourceZip->getFromIndex($i));
    }

    $targetZip->close();
    $sourceZip->close();

    return $relativePath;
  }
}
