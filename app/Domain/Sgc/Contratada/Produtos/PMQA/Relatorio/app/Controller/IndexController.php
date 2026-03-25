<?php

namespace App\Domain\Sgc\Contratada\Produtos\PMQA\Relatorio\app\Controller;

use App\Domain\Sgc\Contratada\Produtos\PMQA\Relatorio\app\Services\RelatorioService;
use App\Models\Contrato;
use App\Models\SgcPmqa;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IndexController extends Controller
{
    public function __construct(private readonly RelatorioService $relatorioService)
    {
    }

    public function index(Contrato $contrato, string $produto, SgcPmqa $pmqa, Request $request): Response
    {
        $searchParams = $request->all('columns', 'value');

        $response = $this->relatorioService->index(pmqa: $pmqa, searchParams: $searchParams);

        return Inertia::render('Sgc/Contratada/Produtos/Pmqa/Relatorio/Index', [
            'contrato' => $contrato,
            'produto' => $produto,
            'pmqa' => $pmqa,
            'tab' => 'resultados',
            ...$response
        ]);
    }
}
