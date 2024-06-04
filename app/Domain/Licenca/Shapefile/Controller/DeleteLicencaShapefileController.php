<?php

namespace App\Domain\Licenca\Shapefile\Controller;

use App\Domain\Licenca\Shapefile\Services\LicencaShapefileService;
use App\Models\LicencaShapefile;
use App\Shared\Http\Controllers\Controller;

class DeleteLicencaShapefileController extends Controller
{
  public function __construct(private readonly LicencaShapefileService $licencaShapefileService)
  {
  }

  public function index(LicencaShapefile $shapefile)
  {
    try {
      $this->licencaShapefileService->delete($shapefile);

      return to_route('licenca.create', ['licenca' => $shapefile->licenca_id])->with('message', ['type' => 'success', 'content' => 'Shapefile deletado!']);
    } catch (\Exception $e) {
      return to_route('licenca.create', ['licenca' => $shapefile->licenca_id])->with('message', ['type' => 'error', 'content' => $e->getMessage()]);
    }
  }
}
