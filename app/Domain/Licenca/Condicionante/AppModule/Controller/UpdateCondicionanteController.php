<?php

namespace App\Domain\Licenca\Condicionante\AppModule\Controller;

use App\Domain\Licenca\Condicionante\AppModule\Request\UpdateCondicionanteRequest;
use App\Domain\Licenca\Condicionante\AppModule\Services\CondicionanteService;
use App\Models\LicencaCondicionante;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class UpdateCondicionanteController extends Controller
{
    public function __construct(private readonly CondicionanteService $condicionanteService)
    {
    }

    public function index(UpdateCondicionanteRequest $request): RedirectResponse
    {
        $response = $this->condicionanteService->update($request->all());

        return to_route(route: 'licenca.condicionante.index', parameters: $response['licenca'])
            ->with('message', $response['request']);
    }
}
