<?php

namespace App\Domain\Servico\MonAtpFauna\Configuracoes\VincualarABIO\app\Controller;

use App\Models\Contrato;
use App\Models\Licenca;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class GetLicencaController extends Controller
{
  public function index(Contrato $contrato, Servicos $servico, Licenca $licenca): JsonResponse
  {
    return response()->json($licenca);
  }
}
