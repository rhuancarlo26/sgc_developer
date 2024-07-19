<?php

namespace App\Domain\Fiscal\app\Controllers;

use App\Domain\Servico\app\Services\ServicoService;
use App\Models\Contrato;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TabConfiguracoesController extends Controller
{
    public function __construct(private readonly ServicoService $servicoService)
    {
    }

    public function index(Contrato $contrato, Request $request): Response
    {
        $searchParams = $request->all('searchColumn', 'searchValue');

        $response = $this->servicoService->listarServicos($contrato, $searchParams, $statusIds = [2, 3, 4]);

        return Inertia::render('Fiscal/Tab/TabConfiguracoes', [
            'contrato' => $contrato,
            ...$response
        ]);
    }
}
