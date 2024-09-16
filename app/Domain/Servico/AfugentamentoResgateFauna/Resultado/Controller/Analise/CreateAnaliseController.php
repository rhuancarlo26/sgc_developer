<?php

namespace App\Domain\Servico\AfugentamentoResgateFauna\Resultado\Controller\Analise;

use App\Domain\Servico\AfugentamentoResgateFauna\Resultado\Service\Analise\AnaliseService;
use App\Models\AfugentFaunaResultadoModel;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CreateAnaliseController extends Controller
{
    public function __construct(private readonly AnaliseService $analiseService) {}

    public function index(AfugentFaunaResultadoModel $resultado, Request $request)
    {
        $analise = $request->all();

        $this->analiseService->create($resultado, $analise);

        return redirect()->back();
    }
}