<?php

namespace App\Domain\Servico\AfugentamentoResgateFauna\Resultado\Controller\OutraAnalise;

use App\Domain\Servico\AfugentamentoResgateFauna\Resultado\Service\OutraAnalise\OutraAnaliseService;
use App\Models\AfugentFaunaResultadoOutrasAnalisesModel;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UpdateOutraAnaliseController extends Controller
{
    public function __construct(private readonly OutraAnaliseService $outraAnaliseService) {}

    public function index(AfugentFaunaResultadoOutrasAnalisesModel $outraAnalise, Request $request)
    {
        $analise = $request->all();
        $this->outraAnaliseService->update($outraAnalise, $analise);

        return redirect()->back();
    }
}