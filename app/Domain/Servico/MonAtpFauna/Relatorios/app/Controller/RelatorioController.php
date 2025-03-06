<?php

namespace App\Domain\Servico\MonAtpFauna\Relatorios\app\Controller;

use App\Domain\Servico\MonAtpFauna\Relatorios\app\Services\AnaliseRelatorioService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RelatorioController extends Controller
{
    public function __construct(
        private readonly AnaliseRelatorioService $analiseRelatorioService,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        return response()->json(
            $this->analiseRelatorioService->getAnalise(request: $request->all())
        );
    }
}
