<?php

namespace App\Domain\Licenca\Documento\Controller;

use App\Models\LicencaDocumento;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;

class visualizarDocumentoController extends Controller
{
  public function visualizar(LicencaDocumento $documento)
  {
    return Response::make(file_get_contents(storage_path('app') . DIRECTORY_SEPARATOR . $documento->caminho), 200, [
      "Content-type" => "application/pdf"
    ]);
  }
}