<?php

namespace App\Domain\Licenca\AppModule\Controller;

use App\Domain\Licenca\AppModule\Services\LicencaService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetLicencaController extends Controller
{
  public function __construct(private readonly LicencaService $listagemLicenca)
  {
  }

  public function index(Request $request): JsonResponse
  {
    $searchParams = $request->all(keys: ['searchColumn', 'searchValue']);

    return response()->json($this->listagemLicenca->get(searchParams: $searchParams, arquivado: $request->arquivado));
  }
}