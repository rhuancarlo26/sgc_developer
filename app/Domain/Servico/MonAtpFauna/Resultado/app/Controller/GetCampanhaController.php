<?php

namespace App\Domain\Servico\MonAtpFauna\Resultado\app\Controller;

use App\Domain\Servico\MonAtpFauna\Resultado\app\Services\ResultadoCampanhaService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetCampanhaController extends Controller
{
    public function __construct(private readonly ResultadoCampanhaService $resultadoService)
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        return response()->json($this->resultadoService->getResultadoCampanhas(id: $request->id));
    }
}
