<?php

namespace App\Domain\Sgc\GestaoContrato\Controller;

use App\Domain\Sgc\GestaoContrato\Services\ListagemSgcService;
use App\Models\ContratoTipo;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ListagemContratoController extends Controller
{
    public function __construct(private readonly ListagemSgcService $listagemContrato)
    {
    }

    public function index(ContratoTipo $tipo, Request $request): Response
    {
        $searchParams = $request->all('columns', 'value');

        $contratos = $this->listagemContrato->ListagemContratos($tipo, $searchParams);

        return Inertia::render('Sgc/GestaoContrato/Index', [
            ...$contratos,
            'tipo' => $tipo,
        ]);
    }
}
