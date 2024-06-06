<?php

namespace App\Domain\Licenca\Documento\Controller;

use App\Domain\Licenca\Documento\Services\LicencaDocumentoService;
use App\Models\LicencaDocumento;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DeleteLicencaDocumentoController extends Controller
{
  public function __construct(private readonly LicencaDocumentoService $licencaDocumentoService)
  {
  }

  public function index(LicencaDocumento $documento)
  {
    Storage::delete($documento->caminho);

    $response = $this->licencaDocumentoService->delete($documento);

    return to_route('licenca.create', ['licenca' => $documento->licenca_id])->with('message', $response);
  }
}