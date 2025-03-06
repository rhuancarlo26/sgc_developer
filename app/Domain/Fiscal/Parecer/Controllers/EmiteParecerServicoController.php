<?php

namespace App\Domain\Fiscal\Parecer\Controllers;

use App\Domain\Fiscal\Parecer\Services\ParecerService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;


class EmiteParecerServicoController extends Controller
{
    public function __construct(private readonly ParecerService $parecerService)
    {
    }

    public function index(Request $request)
    {

        $response = $this->parecerService->emiteParecerServico($request->all());

        return to_route(route: 'fiscal.dados.servicos.index', parameters: ['contrato' => 1])
            ->with('message', $response['request']);
    }
}
