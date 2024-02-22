<?php

namespace App\Domain\Licenca\Condicionante\Controller;

use App\Domain\Licenca\Condicionante\Request\StoreCondicionanteRequest;
use App\Domain\Licenca\Condicionante\Services\CondicionanteService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class StoreCondicionanteController extends Controller
{
    public function __construct(private readonly CondicionanteService $condicionanteService)
    {
    }

    public function store(StoreCondicionanteRequest $request): RedirectResponse
    {
        $response = $this->condicionanteService->store($request->all());
        return to_route(route: 'licenca.condicionante.index', parameters: $response['licenca'])
            ->with('message', $response['request']);
    }
}
