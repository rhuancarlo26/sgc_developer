<?php

namespace App\Domain\Servico\SupressaoVegetacao\Execucao\Destinacao\Controller;

use App\Domain\Servico\SupressaoVegetacao\Execucao\Destinacao\Requests\StoreRequest;
use App\Domain\Servico\SupressaoVegetacao\Execucao\Destinacao\Services\DestinacaoService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class StoreController extends Controller
{

    public function __construct(
        private readonly DestinacaoService $destinacaoService
    ) {}

    public function __invoke(StoreRequest $request): JsonResponse
    {
        $response = $this->destinacaoService->store(request: $request->validated());

        return response()->json([
            'message' => 'Destinação cadastrada com sucesso.',
            'destinacao_id' => $response['model']->id,
            'destinacao_chave' => $response['model']->chave,
        ]);
    }
}
