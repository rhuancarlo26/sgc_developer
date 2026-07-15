<?php

namespace App\Domain\Sgc\Contratada\Produtos\PMQA\Relatorio\app\Controller;

use App\Domain\Sgc\Contratada\Produtos\PMQA\Relatorio\app\Requests\StoreRequest;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Relatorio\app\Services\RelatorioService;
use App\Models\Contrato;
use App\Models\SgcPmqa;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;


class StoreController extends Controller
{
    public function __construct(private readonly RelatorioService $relatorioService)
    {
    }

    public function index(Contrato $contrato, $produto, SgcPmqa $pmqa, StoreRequest $request): RedirectResponse
    {
        $post = [
            ...$request->validated(),
            'status_id' => 4
        ];

        $response = $this->relatorioService->store(request: $post);
        return to_route('contratos.contratada.relatorio.pmqa.relatorio.index', ['contrato' => $contrato->id, 'produto' => $produto, 'pmqa' => $pmqa->id])->with('message', $response['request']);
    }
}
