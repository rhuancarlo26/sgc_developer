<?php

namespace App\Domain\Sgc\Contratada\RelatorioCoord\Controller;

use App\Models\SgcDocx;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class VisualizarDocxController extends Controller
{
    public function index(): JsonResponse
    {
        $documentos = SgcDocx::all();

        return response()->json($documentos);
    }
}