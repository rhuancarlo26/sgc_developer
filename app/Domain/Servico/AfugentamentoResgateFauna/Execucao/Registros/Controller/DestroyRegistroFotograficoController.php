<?php

namespace App\Domain\Servico\AfugentamentoResgateFauna\Execucao\Registros\Controller;

use App\Domain\Servico\AfugentamentoResgateFauna\Execucao\Registros\Service\RegistrosService;
use App\Models\AfugentFaunaExecRegistroImagemModel;
use Illuminate\Http\JsonResponse;

class DestroyRegistroFotograficoController
{
    public function __construct(private readonly RegistrosService $registrosService) {}
    
    public function index(AfugentFaunaExecRegistroImagemModel $fotografia): JsonResponse
    {
        $response  = $this->registrosService->destroyRegistroFotografico($fotografia);

        return response()->json($response);
    }
}