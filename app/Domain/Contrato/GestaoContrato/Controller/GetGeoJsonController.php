<?php

namespace App\Domain\Contrato\GestaoContrato\Controller;

use App\Models\Contrato;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class GetGeoJsonController extends Controller
{
  public function getGeoJson(): JsonResponse
  {
    $coordenadas = Contrato::all();

    return response()->json($coordenadas);
  }
}