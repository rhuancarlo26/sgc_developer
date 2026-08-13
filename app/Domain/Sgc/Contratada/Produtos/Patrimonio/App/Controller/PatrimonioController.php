<?php

namespace App\Domain\Sgc\Contratada\Produtos\Patrimonio\App\Controller;

use App\Domain\Sgc\Contratada\Produtos\Patrimonio\App\Request\PatrimonioRequest;
use App\Domain\Sgc\Contratada\Produtos\Patrimonio\App\Services\PatrimonioService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use InvalidArgumentException;

class PatrimonioController extends Controller
{
    private PatrimonioService $patrimonioService;

    public function __construct(PatrimonioService $patrimonioService)
    {
        $this->patrimonioService = $patrimonioService;
    }

    public function store(PatrimonioRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            $subproduto = $this->patrimonioService->createSubProdutos(
                $validated['tipo'],
                $validated
            );

            return response()->json([
                'success' => true,
                'message' => 'Subproduto criado com sucesso',
                'data' => $subproduto->toArray(),
            ], 201);
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar subproduto',
            ], 500);
        }
    }
}
