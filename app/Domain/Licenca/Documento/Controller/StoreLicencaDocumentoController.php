<?php

namespace App\Domain\Licenca\Documento\Controller;

use App\Domain\Licenca\app\Services\LicencaService;
use App\Domain\Licenca\Documento\Services\LicencaDocumentoService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StoreLicencaDocumentoController extends Controller
{
  public function __construct(private readonly LicencaService $licencaService)
  {
  }

  public function index(Request $request)
  {
    $response = $this->licencaService->storeDocumento($request->documento, $request->licenca_id);

    return to_route('licenca.create', ['licenca' => $request->licenca_id])->with('message', $response);
  }
}