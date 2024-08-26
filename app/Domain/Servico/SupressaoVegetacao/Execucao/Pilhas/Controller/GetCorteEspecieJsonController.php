<?php

namespace App\Domain\Servico\SupressaoVegetacao\Execucao\Pilhas\Controller;

use App\Models\AreaSupressao;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class GetCorteEspecieJsonController extends Controller
{

    public function __invoke(?AreaSupressao $supressao): JsonResponse
    {
        return response()->json(data: $supressao?->corteEspecies);
    }

}
