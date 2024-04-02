<?php

namespace App\Domain\Licenca\app\Controller;

use App\Domain\Licenca\app\Requests\StoreLicencaRequest;
use App\Domain\Licenca\app\Services\LicencaService;
use App\Shared\Http\Controllers\Controller;

class StoreLicencaController extends Controller
{
    public function __construct(private readonly LicencaService $listagemLicenca)
    { }

    public function index(StoreLicencaRequest $request): \Illuminate\Http\RedirectResponse
    {
        $parameters = $this->listagemLicenca->create(post: $request->all());

        return to_route(route: 'licenca.create', parameters: $parameters['licenca'])
            ->with('message', $parameters['request']);
    }
}
