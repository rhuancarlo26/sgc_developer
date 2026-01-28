<?php

namespace App\Domain\Sgc\Contratada\Produtos\PMQA\Execucao\app\Controller;

use App\Domain\Sgc\Contratada\Produtos\PMQA\Execucao\app\Requests\StoreRequest;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Execucao\app\Services\CampanhaService;
use App\Models\Contrato;
use App\Models\SgcPmqa;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class StoreController extends Controller
{
    public function __construct(private readonly CampanhaService $campanhaService) {}

    public function index(
        Contrato $contrato,
        string $produto,
        SgcPmqa $pmqa,
        StoreRequest $request
    ): RedirectResponse {
        $response = $this->campanhaService->store($request->validated());

        return to_route('contratos.contratada.sgc.pmqa.execucao.index', [
            'contrato' => $contrato->id,
            'produto'  => $produto,
            'pmqa'     => $pmqa->id,
            'tab'      => 'execucao',
            'subStep'  => 5, 
        ])->with('message', $response['request']);
    }
}
