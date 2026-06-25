<?php

namespace App\Domain\Sgc\Contratada\Modulo\ConfigPlanilha\Controllers;

use App\Domain\Sgc\Contratada\Modulo\ConfigPlanilha\Requests\ProcessarCamposPlanilhaRequest;
use App\Domain\Sgc\Contratada\Modulo\ConfigPlanilha\Services\ProcessarCamposPlanilhaService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class ProcessarCamposPlanilhaController extends Controller
{
    public function __construct(
        private ProcessarCamposPlanilhaService $service
    ) {
        // 
    }

    public function processarCamposPlanilha(ProcessarCamposPlanilhaRequest $request): JsonResponse
    {
        $data = $this->service->processarCamposPlanilha($request->validated());

        if (isset($data['error']) && $data['error']) {
            return response()->json(['message' => $data['message']], 422);
        }

        return response()->json($data['colunas']);
    }
}
