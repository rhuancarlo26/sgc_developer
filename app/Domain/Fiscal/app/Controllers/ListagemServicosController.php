<?php

namespace App\Domain\Fiscal\app\Controllers;

use App\Domain\Fiscal\app\services\FiscalService;
use App\Models\Contrato;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ListagemServicosController extends Controller
{
    public function __construct(private readonly FiscalService $fiscalService)
    {
    }

    public function index(Contrato $contrato, Request $request): Response
    {
        $searchParams = $request->all('searchColumn', 'searchValue');

        $response = $this->fiscalService->listagemServicos($contrato, $searchParams, );

        return Inertia::render('Fiscal/Servico/TabServicos', [
            'contrato' => $contrato,
            ...$response
        ]);
    }
}
