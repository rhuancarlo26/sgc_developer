<?php

namespace App\Domain\Fiscal\app\Controllers;

use App\Domain\Fiscal\app\services\FiscalRncService;
use App\Domain\Servico\app\Services\ServicoService;
use App\Models\Contrato;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TabRNCController extends Controller
{
    public function __construct(private readonly FiscalRncService $fiscalRncService) {}

    public function index(Contrato $contrato, Request $request): Response
    {
        $searchParams = $request->all('columns', 'value');

        $response = $this->fiscalRncService->index($contrato->id, $searchParams);

        return Inertia::render('Fiscal/RNC/Index', [
            'contrato' => $contrato,
            ...$response
        ]);
    }
}
