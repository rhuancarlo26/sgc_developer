<?php

namespace App\Domain\Sgc\Contratada\RelatorioCoord\Controller;

use App\Models\Docx; // Importe o modelo correto, se necessário
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class VisualizarDocxController extends Controller
{
    public function index(): JsonResponse
    {
        // Realize a consulta diretamente no controller
        $documentos = Docx::all();

        return response()->json($documentos);
    }
}