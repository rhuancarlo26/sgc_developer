<?php

namespace App\Domain\Servico\AfugentamentoResgateFauna\Resultado\Controller\Analise;

use App\Domain\Servico\AfugentamentoResgateFauna\Resultado\Service\Analise\AnaliseService;
use App\Models\AfugentFaunaResultadoAnalisesModel;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UpdateAnaliseController extends Controller
{
    public function __construct(private readonly AnaliseService $analiseService) {}
    
    public function index(AfugentFaunaResultadoAnalisesModel $analise, Request $request)
    {
        $this->analiseService->update($analise, $request->all());
    }
}