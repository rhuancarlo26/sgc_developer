<?php

namespace App\Domain\Contrato\GestaoContrato\Controller;

use App\Domain\Contrato\GestaoContrato\Requests\UpdateContratoTrechoRequest;
use App\Domain\Contrato\GestaoContrato\Services\TrechoContratoService;
use App\Shared\Http\Controllers\Controller;

class UpdateContratoTrechoController extends Controller
{
    public function __construct(private readonly TrechoContratoService $trechoContratoService)
    {
    }

    public function updateTrecho(UpdateContratoTrechoRequest $request)
    {
        $post = [
            ...$request->validated(),
            'extensao' => $request->km_final - $request->km_inicial,
        ];

        $response = $this->trechoContratoService->update($post);

        return to_route('contratos.gestao.create',
            [
                'tipo' => $request->tipo_contrato,
                'contrato' => $request->contrato_id
            ])->with('message', $response['request']);
    }
}
