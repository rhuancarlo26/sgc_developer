<?php

namespace App\Domain\Licenca\LicencaSegmento\Controller;

use App\Domain\Licenca\LicencaSegmento\Requests\StoreLicencaSegmentoRequest;
use App\Domain\Licenca\LicencaSegmento\Services\LicencaSegmentoService;
use App\Models\Licenca;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetLicencaSegmentoController extends Controller
{
  public function __construct(private readonly LicencaSegmentoService $licencaSegmentoService)
  {
  }

  public function index(Licenca $licenca): JsonResponse
  {
    return response()->json($this->licencaSegmentoService->get(licenca: $licenca));
  }
}
