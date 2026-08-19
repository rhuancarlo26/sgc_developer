<?php

namespace App\Domain\Modulos\ConfiguracoesModulos\Controllers;

use App\Domain\Modulos\ConfiguracoesModulos\Requests\ProcessarCamposPlanilhaRequest;
use App\Domain\Modulos\ConfiguracoesModulos\Services\ProcessarCamposPlanilhaService;
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

        return response()->json([
            'colunas' => $data['colunas'],
        ]);
    }
}
