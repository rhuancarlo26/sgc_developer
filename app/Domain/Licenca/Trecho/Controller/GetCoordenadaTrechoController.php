<?php

namespace App\Domain\Licenca\Trecho\Controller;

use App\Models\LicencaTrecho;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetCoordenadaTrechoController extends Controller
{
  public function index(Request $request): JsonResponse
  {
    $coordenadas = LicencaTrecho::with(['uf', 'rodovia'])
      ->when($request->licenca_id, function ($q) use ($request) {
        $q->where('licenca_id', $request->licenca_id);
      })->get();

    return Response()->json($coordenadas);
  }
}
