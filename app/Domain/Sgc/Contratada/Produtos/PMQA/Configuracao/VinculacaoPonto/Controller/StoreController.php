<?php

namespace App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\VinculacaoPonto\Controller;

use App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\VinculacaoPonto\Requests\StoreRequest;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\VinculacaoPonto\Services\VinculacaoPontoService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Models\SgcPmqa;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class StoreController extends Controller
{
    public function __construct(private readonly VinculacaoPontoService $vinculacaoPontoService) {}

    public function index(
        Contrato $contrato,
        string $produto,
        SgcPmqa $pmqa,
        StoreRequest $request
    ) {
        $response = $this->vinculacaoPontoService->store(
            $pmqa,
            $request->validated()
        );

        return back()->with('success', $response['request']['content']);
    }
}
