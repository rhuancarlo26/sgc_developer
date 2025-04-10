<?php

namespace App\Domain\Sgc\GestaoContrato\Controller;

use App\Domain\Contrato\GestaoContrato\Services\ListagemContratoService;
use App\Models\Contrato;
use App\Models\ContratoTipo;
use App\Shared\Http\Controllers\Controller;
use Inertia\Inertia;

class CreateContratoController extends Controller
{
    public function __construct(private readonly ListagemContratoService $listagemContrato)
    {
    }

    public function create(ContratoTipo $tipo, Contrato|null $contrato)
    {
        $response = $this->listagemContrato->create($contrato);

        return Inertia::render('Contrato/GestaoContrato/Form', [
            ...$response,
            'tipo' => $tipo
        ]);
    }
}