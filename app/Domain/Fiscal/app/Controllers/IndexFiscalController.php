<?php

namespace App\Domain\Fiscal\app\Controllers;

use App\Domain\Contrato\GestaoContrato\Services\ListagemContratoService;
use App\Shared\Http\Controllers\Controller;
use App\Models\ContratoTipo;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Inertia\Response;


class IndexFiscalController extends Controller
{
    public function __construct(private readonly ListagemContratoService $fiscalService)
    {
    }

    public function index(ContratoTipo $tipo, Request $request): Response
    {
        $searchParams = $request->all('columns', 'value');

        $contratos = $this->fiscalService->ListagemContratos($tipo, $searchParams);

        return Inertia::render('Fiscal/Index', [
            ... $contratos,
            'tipo' => $tipo
        ]);
    }
}
