<?php

namespace App\Domain\Servico\SupressaoVegetacao\Resultado\Controller;

use App\Domain\Servico\SupressaoVegetacao\Resultado\Services\ResultadoService;
use App\Models\ResultadoSupressao;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class PilhaAnaliseController extends Controller
{

    public function __construct(
        private readonly ResultadoService $resultadoService
    )
    {
    }

    public function __invoke(Servicos $servico, ResultadoSupressao $resultado): JsonResponse
    {
        return response()->json(
            data: $this->resultadoService->getResultadoAnalisePilha($servico, $resultado)
        );
    }

}
