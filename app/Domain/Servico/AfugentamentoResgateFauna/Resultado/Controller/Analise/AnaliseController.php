<?php

namespace App\Domain\Servico\AfugentamentoResgateFauna\Resultado\Controller\Analise;

use App\Domain\Servico\AfugentamentoResgateFauna\Resultado\Service\Analise\AnaliseService;
use App\Models\AfugentFaunaResultadoModel;

class AnaliseController
{
    public function __construct(private readonly AnaliseService $analiseService) {}

    public function index(AfugentFaunaResultadoModel $resultado)
    {
        return $this->analiseService->getAnalise($resultado);
    }
}
