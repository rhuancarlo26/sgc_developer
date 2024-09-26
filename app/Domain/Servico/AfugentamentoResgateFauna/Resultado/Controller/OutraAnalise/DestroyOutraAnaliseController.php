<?php

namespace App\Domain\Servico\AfugentamentoResgateFauna\Resultado\Controller\OutraAnalise;

use App\Domain\Servico\AfugentamentoResgateFauna\Resultado\Service\OutraAnalise\OutraAnaliseService;
use App\Models\AfugentFaunaResultadoOutrasAnalisesModel;
use App\Shared\Http\Controllers\Controller;

class DestroyOutraAnaliseController extends Controller
{
    public function __construct(private readonly OutraAnaliseService $outraAnaliseService) {}

    public function index(AfugentFaunaResultadoOutrasAnalisesModel $outraAnalise)
    {
        return $this->outraAnaliseService->destroy($outraAnalise);
    }
}