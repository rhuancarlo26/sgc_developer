<?php

namespace App\Domain\Contrato\GestaoContrato\Controller;

use App\Domain\Contrato\GestaoContrato\Requests\StoreContratoTrechoRequest;
use App\Domain\Contrato\GestaoContrato\Services\TrechoContratoService;
use App\Shared\Http\Controllers\Controller;

class StoreContratoTrechoController extends Controller
{
    public function __construct(private readonly TrechoContratoService $trechoContratoService)
    {
    }

    public function storeTrecho(StoreContratoTrechoRequest $request)
    {
        $post = [
            ...$request->validated(),
            'extensao' => $request->km_final - $request->km_inicial,
        ];

        $response = $this->trechoContratoService->create($post);

        return to_route('contratos.gestao.create',
            [
                'tipo' => $request->tipo_contrato,
                'contrato' => $request->contrato_id])
            ->with('message', $response['request']);
    }
}
