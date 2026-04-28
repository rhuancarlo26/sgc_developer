<?php

namespace App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\Parametro\Controller;

use App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\Parametro\Services\ParametroService;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\Parametro\Requests\StoreRequest;
use App\Models\Contrato;
use App\Models\SgcPmqa;
use App\Shared\Http\Controllers\Controller;

class StoreController extends Controller
{
    public function __construct(private readonly ParametroService $parametroService) {}

    public function index(int $contrato, string $produto, int $pmqa, StoreRequest $request)
    {
        
        $contratoModel = Contrato::findOrFail($contrato);
        $pmqaModel = SgcPmqa::findOrFail($pmqa);
        $response = $this->parametroService->store($pmqaModel, $request->validated());

        // volta pra mesma tela (Create) na aba configuracao/subStep 3
        return to_route('contratos.contratada.sgc.pmqa.configuracao.ponto.index', [
            'contrato' => $contratoModel->id,
            'produto'  => $produto,
            'pmqa'     => $pmqaModel->id,
            'subStep'  => 3,
            'tab'      => 'configuracao',
        ])->with('message', $response);
    }
}
