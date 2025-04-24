<?php

namespace App\Domain\Contrato\GestaoContrato\Controller;

use App\Domain\Contrato\GestaoContrato\Services\TrechoContratoService;
use App\Shared\Http\Controllers\Controller;
use App\Shared\Integrations\Sgplan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class GetTipoPorUfBrContratoTrechoController extends Controller
{
    public function __construct(private readonly TrechoContratoService $trechoContratoService)
    {
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */

     public function getTipoPorUfBr(Request $request): JsonResponse
     {
        $sgplan = new Sgplan();
        return response()->json($sgplan->tipoPorUfBr(uf: $request->uf_id, rodovia: $request->rodovia_id, versaoSNV: $request->data));
     }
}
