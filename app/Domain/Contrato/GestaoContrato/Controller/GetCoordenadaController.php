<?php

namespace App\Domain\Contrato\GestaoContrato\Controller;

use App\Models\SvnSegGeoV2;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetCoordenadaController extends Controller
{
  public function getCoordenada(Request $request): JsonResponse
  {
    $request->validate([
      'uf' => 'required',
      'rodovia' => 'required',
      'km_inicial' => 'required|numeric',
      'km_final' => 'required|numeric|gte:km_inicial',
      'trecho_tipo' => 'required|string'
    ]);

    $geojson = SvnSegGeoV2::getGeoJson(
      $request->uf['uf'],
      $request->rodovia['rodovia'],
      $request->km_inicial,
      $request->km_final,
      $request->trecho_tipo ?? 'B'
    );

    return response()->json($geojson);
  }
}