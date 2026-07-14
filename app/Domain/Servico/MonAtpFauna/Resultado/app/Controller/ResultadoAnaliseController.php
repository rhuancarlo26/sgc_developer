<?php

namespace App\Domain\Servico\MonAtpFauna\Resultado\app\Controller;

use App\Domain\Servico\MonAtpFauna\Resultado\app\Services\ResultadoService;
use App\Models\AtFaunaResultado;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class ResultadoAnaliseController extends Controller
{
    public function __construct(
        private readonly ResultadoService $resultadoService,
    ) {
    }

    public function __invoke(AtFaunaResultado $resultado): JsonResponse
    {
        return response()->json(
            $this->resultadoService->getResultadoAnalise($resultado)
        );
    }
}
