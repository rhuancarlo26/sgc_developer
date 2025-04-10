<?php

namespace App\Domain\Servico\AfugentamentoResgateFauna\Resultado\Controller\OutraAnalise;

use App\Domain\Servico\AfugentamentoResgateFauna\Resultado\Service\OutraAnalise\OutraAnaliseService;
use App\Models\AfugentFaunaResultadoModel;
use Illuminate\Http\Request;

class CreateOutraAnaliseController
{
    public function __construct(private readonly OutraAnaliseService $outraAnaliseService) {}

    public function index(AfugentFaunaResultadoModel $resultado, Request $request)
    {
        $outraAnalise = $request->all();

        $this->outraAnaliseService->create($resultado, $outraAnalise);

        return redirect()->back();
    }
}
