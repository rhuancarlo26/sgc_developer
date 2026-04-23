<?php

namespace App\Domain\Modulos\Importador\Controllers;

use App\Domain\Modulos\Importador\Services\DadosImportadorService;
use App\Models\ModuloImportador;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DadosImportadorController extends Controller
{
    public function __construct(
        private DadosImportadorService $service
    ) {
        // 
    }

    public function buscarDados(ModuloImportador $importador, Request $request): JsonResponse
    {
        $data = $this->service->buscarDados($importador, $request);
        return response()->json($data);
    }
}
