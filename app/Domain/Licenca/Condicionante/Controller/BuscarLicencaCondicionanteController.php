<?php

namespace App\Domain\Licenca\Condicionante\Controller;

use App\Domain\Licenca\Condicionante\Services\CondicionanteService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BuscarLicencaCondicionanteController extends Controller
{
  public function __construct(private readonly CondicionanteService $condicionanteService)
  {
  }

  public function buscarLicencaCondicionante(Request $request): JsonResponse
  {
    return response()->json($this->condicionanteService->buscarLicenca($request->all()));
  }
}