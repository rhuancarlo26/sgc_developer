<?php

namespace App\Domain\Fiscal\Parecer\Controllers;

use App\Domain\Fiscal\Parecer\Services\ParecerService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;


class EmiteParecerConfigAfugentamentoController extends Controller
{
    public function __construct(private readonly ParecerService $parecerService)
    {
    }

    public function index(Request $request)
    {
        $response = $this->parecerService->emiteParecerConfigAfugentamento($request->all());

        return to_route(route: 'fiscal.configuracoes.afugentamento.index', parameters: ['contrato' => $request->get('id_contrato')])
            ->with('message', $response['request']);
    }
}
