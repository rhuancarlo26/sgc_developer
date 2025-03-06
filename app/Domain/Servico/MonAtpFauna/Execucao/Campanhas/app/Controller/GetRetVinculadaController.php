<?php

namespace App\Domain\Servico\MonAtpFauna\Execucao\Campanhas\app\Controller;

use App\Domain\Servico\MonAtpFauna\Execucao\Campanhas\app\Services\ExecucaoCampanhaRetService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetRetVinculadaController extends Controller
{
    public function __construct(
        private readonly ExecucaoCampanhaRetService $execucaoCampanhaRetService,
    )
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        return response()->json(
            $this->execucaoCampanhaRetService->getRetsCampanhasVinculadas($request->get('campanha_id'))
        );
    }
}
