<?php

namespace App\Domain\Sgc\Contratada\Produtos\PMQA\Execucao\app\Controller;

use App\Domain\Sgc\Contratada\Produtos\PMQA\Execucao\app\Services\CampanhaService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Models\SgcPmqa;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IndexController extends Controller
{
    public function __construct(private readonly CampanhaService $campanhaService) {}

    public function index(Contrato $contrato, string $produto, SgcPmqa $pmqa, Request $request): Response
    {
        $searchParams = $request->all('columns', 'value');

        $response = $this->campanhaService->index($pmqa, $searchParams);

        return Inertia::render('Sgc/Contratada/Produtos/Pmqa/Execucao/Index', [
            'contrato' => $contrato,
            'produto'   => $produto,
            'pmqa'      => $pmqa,
            'tab' => 'execucao',
            ...$response
        ]);
    }
}
