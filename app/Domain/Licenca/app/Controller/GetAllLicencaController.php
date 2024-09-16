<?php

namespace App\Domain\Licenca\app\Controller;

use App\Domain\Licenca\app\Services\LicencaService;
use App\Models\Licenca;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetAllLicencaController extends Controller
{
    public function __construct(private readonly LicencaService $listagemLicenca)
    {
    }

    public function index(): JsonResponse
    {
        $licencas = Licenca::with([
            'segmentos',
            'segmentos.uf_inicial_rel',
            'segmentos.uf_final_rel'
        ])->get();

        return response()->json($licencas);
    }
}
