<?php

namespace App\Domain\Sgc\Contratada\Produtos\PMQA\Resultado\app\Controller;

use App\Domain\Sgc\Contratada\Produtos\PMQA\Resultado\app\Services\ResultadoService;
use App\Models\Contrato;
use App\Models\SgcPmqa;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IndexController extends Controller
{
    public function __construct(private readonly ResultadoService $resultadoService) {}

    public function index(Contrato $contrato, string $produto, SgcPmqa $pmqa, Request $request): Response
    {
        $searchParams = $request->all('columns', 'value');

        $response = $this->resultadoService->index(pmqa: $pmqa, searchParams: $searchParams);

        return Inertia::render('Sgc/Contratada/Produtos/Pmqa/Resultado/Index', [
            'contrato' => $contrato,
            'pmqa' => $pmqa,
            'produto' => $produto,
            'tab' => 'resultados',
            ...$response
        ]);
    }
}
