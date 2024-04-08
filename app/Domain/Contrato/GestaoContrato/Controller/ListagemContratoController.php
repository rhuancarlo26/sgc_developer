<?php

namespace App\Domain\Contrato\GestaoContrato\Controller;

use App\Domain\Contrato\GestaoContrato\Services\ListagemContratoService;
use App\Models\ContratoTipo;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ListagemContratoController extends Controller
{
    public function __construct(private readonly ListagemContratoService $listagemContrato)
    {
    }

    public function index(ContratoTipo $tipo, Request $request): Response
    {
        $searchParams = $request->all('searchColumn', 'searchValue');

        $contratos = $this->listagemContrato->ListagemContratos($tipo, $searchParams);

        return Inertia::render('Contrato/GestaoContrato/Index', [
            ...$contratos,
            'tipo' => $tipo,
        ]);
    }
}
