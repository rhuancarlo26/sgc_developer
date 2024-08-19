<?php

namespace App\Domain\Licenca\Documento\Controller;

use App\Domain\Licenca\Documento\Services\LicencaDocumentoService;
use App\Models\Licenca;
use App\Models\LicencaDocumento;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DeleteLicencaDocumentoController extends Controller
{

  public function index(Licenca $licenca)
  {
    $response = [];

    try {
      if ($licenca->arquivo_licenca) {
        if (Storage::fileExists($licenca->arquivo_licenca)) {
          Storage::delete($licenca->arquivo_licenca);
        }
      }

      $licenca->update([
        'arquivo_licenca' => null
      ]);

      $response = ['type' => 'success', 'content' => 'Documento excluido com sucesso!'];
    } catch (\Exception $th) {
      $response = ['type' => 'error', 'content' => $th->getMessage()];
    }

    return to_route('licenca.create', ['licenca' => $licenca->id])->with('message', $response);
  }
}