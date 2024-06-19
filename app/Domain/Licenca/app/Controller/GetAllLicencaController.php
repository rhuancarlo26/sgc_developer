<?php

namespace App\Domain\Licenca\app\Controller;

use App\Domain\Licenca\app\Services\LicencaService;
use App\Models\Licenca;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetAllLicencaController extends Controller
{
  public function __construct(private readonly LicencaService $listagemLicenca)
  {
  }

  public function index(): JsonResponse
  {
    $licencas = Licenca::with([
      'segmentos',
      'segmentos.uf_inicial',
      'segmentos.uf_final',
      'shapefile'
    ])->get();

    return response()->json($licencas);
  }
}
