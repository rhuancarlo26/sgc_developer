<?php

namespace App\Domain\Licenca\LicencaSegmento\Controller;

use App\Domain\Licenca\LicencaSegmento\Services\LicencaSegmentoService;
use App\Models\Rodovia;
use App\Shared\Http\Controllers\Controller;
use App\Shared\Integrations\Sgplan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EspacializarLinhaLicencaSegmentoController extends Controller
{
    public function __construct(private readonly LicencaSegmentoService $licencaSegmentoService) {}

    /**
     * @param Request $request
     * @return JsonResponse
     */

    public function espacializarLinha(Request $request): JsonResponse
    {
        $sgplan = new Sgplan();
        // if ($request->filled('rodovia_id')) {
        //     $rodoviaId = $request->rodovia_id;
        //     if (is_numeric($rodoviaId) && (int)$rodoviaId == $rodoviaId) {
        //         $rodovia = $request->rodovia_id;
        //     } else {
        //         $rodovia = Rodovia::where('rodovia', $rodoviaId)->first()?->id;
        //     }
        // }

        return response()->json($sgplan->getGeoJsonLinestring(uf: $request->uf_id, rodovia: $request->rodovia_id, kmInicial: $request->km_inicial, kmFinal: $request->km_final, tipoTrecho: $request->tipo, cod_tipo: $request->cd_tipo, versaoSNV: $request->data));
    }
}
