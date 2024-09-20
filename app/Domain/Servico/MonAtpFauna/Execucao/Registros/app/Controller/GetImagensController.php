<?php

namespace App\Domain\Servico\MonAtpFauna\Execucao\Registros\app\Controller;

use App\Domain\Servico\MonAtpFauna\Execucao\Registros\app\Services\RegistrosImagensService;
use App\Models\AtFaunaExecucaoRegistro;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class GetImagensController extends Controller
{
    public function __construct(private readonly RegistrosImagensService $registrosImagensService)
    {
    }

    public function __invoke(AtFaunaExecucaoRegistro $registro): JsonResponse
    {
        $response = $this->registrosImagensService->getImagens($registro->id);

        return response()->json($response);
    }
}
