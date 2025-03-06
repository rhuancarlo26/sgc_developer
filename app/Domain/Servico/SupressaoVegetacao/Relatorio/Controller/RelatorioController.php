<?php

namespace App\Domain\Servico\SupressaoVegetacao\Relatorio\Controller;

use App\Domain\Servico\SupressaoVegetacao\Relatorio\Services\GetRelatorioService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RelatorioController extends Controller
{

    public function __construct(
        private readonly GetRelatorioService $relatorioService,
    )
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        return response()->json(
            $this->relatorioService->getRelatorioData(request: $request->all())
        );
    }

}
