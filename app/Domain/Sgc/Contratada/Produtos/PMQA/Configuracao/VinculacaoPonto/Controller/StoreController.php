<?php

namespace App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\VinculacaoPonto\Controller;

use App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\VinculacaoPonto\Requests\StoreRequest;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\VinculacaoPonto\Services\VinculacaoPontoService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Models\SgcPmqa;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class StoreController extends Controller
{
    public function __construct(private readonly VinculacaoPontoService $vinculacaoPontoService) {}

    public function index(
        Contrato $contrato,
        string $produto,
        SgcPmqa $pmqa,
        StoreRequest $request
    ): JsonResponse {
        $response = $this->vinculacaoPontoService->store(
            $pmqa,
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => $response['request']['content'],
            'data' => [
                'contrato_id' => $contrato->id,
                'produto'     => $produto,
                'pmqa_id'     => $pmqa->id,
            ]
        ], 200);
    }
}
