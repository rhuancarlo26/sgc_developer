<?php

namespace App\Domain\Servico\AfugentamentoResgateFauna\Execucao\Registros\Controller;

use App\Domain\Servico\AfugentamentoResgateFauna\Execucao\Registros\Service\RegistrosService;
use App\Models\AfugentFaunaExecRegistroModel;
use Illuminate\Http\JsonResponse;

class GetRegistroFotograficoController
{
    public function __construct(private readonly RegistrosService $registrosService) {}

    public function index(AfugentFaunaExecRegistroModel $registro): JsonResponse
    {
        $response  = $this->registrosService->getRegistroFotografico($registro);

        return response()->json($response);
    }
}
