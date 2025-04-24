<?php

namespace App\Domain\Licenca\LicencaSegmento\Controller;

use App\Domain\Licenca\LicencaSegmento\Services\LicencaSegmentoService;
use App\Shared\Http\Controllers\Controller;
use App\Shared\Integrations\Sgplan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetTipoPorUfBrLicencaSegmentoController extends Controller
{
    public function __construct(private readonly LicencaSegmentoService $licencaSegmentoService) {}

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
