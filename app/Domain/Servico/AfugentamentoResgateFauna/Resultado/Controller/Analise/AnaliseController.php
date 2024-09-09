<?php

namespace App\Domain\Servico\AfugentamentoResgateFauna\Resultado\Controller\Analise;

use App\Domain\Servico\AfugentamentoResgateFauna\Resultado\Service\Analise\AnaliseService;
use App\Models\AfugentFaunaResultadoModel;
use App\Shared\Http\Controllers\Controller;

class AnaliseController extends Controller
{
    public function __construct(private readonly AnaliseService $analiseService) {}

    public function index(AfugentFaunaResultadoModel $resultado)
    {
        return $this->analiseService->getAnalise($resultado);
    }
}
