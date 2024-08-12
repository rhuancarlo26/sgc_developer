<?php

namespace App\Domain\Servico\AfugentamentoResgateFauna\app\Controller;

use App\Domain\Servico\AfugentamentoResgateFauna\app\Utils\ConfigucacaoParecer;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class AprovacaoConfigController extends Controller
{
    public function __construct(private readonly ConfigucacaoParecer $configucacaoParecer)
    {
    }

    public function index($id_servico): JsonResponse
    {
        return response()->json($this->configucacaoParecer->get(id_servico: $id_servico));
    }
}
