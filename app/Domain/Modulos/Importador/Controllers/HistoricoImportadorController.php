<?php

namespace App\Domain\Modulos\Importador\Controllers;

use App\Models\ModuloImportador;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class HistoricoImportadorController extends Controller
{
    public function buscarHistorico(ModuloImportador $importador): JsonResponse
    {
        $importador->historicos->load('usuario:id,name');
        $importador->historicos->append('status_historico_formatado');

        $historicos = $importador->historicos?->sortBy('created_at');

        return response()->json([
            'historicos' => $historicos
        ]);
    }
}
