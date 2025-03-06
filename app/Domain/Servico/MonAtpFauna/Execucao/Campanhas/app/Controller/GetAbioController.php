<?php

namespace App\Domain\Servico\MonAtpFauna\Execucao\Campanhas\app\Controller;

use App\Domain\Servico\MonAtpFauna\Execucao\Campanhas\app\Services\ExecucaoCampanhaService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetAbioController extends Controller
{
    public function __construct(
        private readonly ExecucaoCampanhaService $execucaoCampanhaService,
    )
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        return response()->json(
            $this->execucaoCampanhaService->getAbioCampanhas($request->get('campanha_id'))
        );
    }
}
