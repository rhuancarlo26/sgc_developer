<?php

namespace App\Domain\Contrato\GestaoContrato\Controller;

use App\Domain\Contrato\GestaoContrato\Requests\StoreContratoRequest;
use App\Domain\Contrato\GestaoContrato\Services\ListagemContratoService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class StoreContratoController extends Controller
{
    public function __construct(private readonly ListagemContratoService $listagemContrato)
    {
    }

    public function store(StoreContratoRequest $request)
    {
        $post = [
            ...$request->all(),
            'usuarios_id' => Auth::user()->id
        ];

        $response = $this->listagemContrato->store($post);

        return to_route('contratos.gestao.create', [
            'tipo'     => $response['model']->tipo_contrato,
            'contrato' => $response['model']->id
        ])->with('message', $response['request']);
    }
}
