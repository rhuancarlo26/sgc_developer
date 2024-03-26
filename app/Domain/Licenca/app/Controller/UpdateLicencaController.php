<?php

namespace App\Domain\Licenca\app\Controller;

use App\Domain\Licenca\app\Requests\UpdateLicencaRequest;
use App\Domain\Licenca\app\Services\LicencaService;
use App\Shared\Http\Controllers\Controller;

class UpdateLicencaController extends Controller
{
    public function __construct(private readonly LicencaService $listagemLicenca)
    {
    }

    public function index(UpdateLicencaRequest $request): \Illuminate\Http\RedirectResponse
    {
        $parameters = $this->listagemLicenca->update(post: $request->all());
        return to_route(route: 'licenca.create', parameters: $parameters['licenca'])
            ->with('message', $parameters['request']);
    }
}
