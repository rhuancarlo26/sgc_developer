<?php

namespace App\Domain\Contrato\GestaoContrato\Controller;


use App\Domain\Contrato\GestaoContrato\Services\TrechoContratoService;
use App\Shared\Http\Controllers\Controller;
use App\Shared\Integrations\Sgplan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EspacializarLinhaContratoTrechoController extends Controller
{
    public function __construct(private readonly TrechoContratoService $trechoContratoService)
    {
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */

     public function espacializarLinha(Request $request): JsonResponse
     {
         $sgplan = new Sgplan();
         return response()->json($sgplan->getGeoJsonLinestring(uf: $request->uf_id, rodovia: $request->rodovia_id, kmInicial: $request->km_inicial, kmFinal: $request->km_final, tipoTrecho: $request->tipo, cod_tipo: $request->cd_tipo, versaoSNV: $request->data));
     }
}
