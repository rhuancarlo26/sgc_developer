<?php

namespace App\Domain\Licenca\Condicionante\Controller;

use App\Domain\Licenca\Condicionante\Request\UpdateCondicionanteRequest;
use App\Domain\Licenca\Condicionante\Services\CondicionanteService;
use App\Models\LicencaCondicionante;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class UpdateCondicionanteController extends Controller
{
    public function __construct(private readonly CondicionanteService $condicionanteService)
    {
    }

    public function update(UpdateCondicionanteRequest $request): RedirectResponse
    {
        $response = $this->condicionanteService->update($request->all());

        return to_route(route: 'licenca.condicionante.index', parameters: $response['licenca'])
            ->with('message', $response['request']);
    }
}
