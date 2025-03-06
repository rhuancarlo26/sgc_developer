<?php

namespace App\Domain\Servico\AfugentamentoResgateFauna\Resultado\Controller\OutraAnalise;

use App\Domain\Servico\AfugentamentoResgateFauna\Resultado\Service\OutraAnalise\OutraAnaliseService;
use App\Models\AfugentFaunaResultadoModel;
use App\Shared\Http\Controllers\Controller;

class OutraAnaliseController extends Controller
{
    public function __construct(private readonly OutraAnaliseService $outraAnaliseService) {}

    public function index(AfugentFaunaResultadoModel $resultado) 
    {
        return $this->outraAnaliseService->getOutraAnalise($resultado);
    }
}
