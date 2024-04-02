<?php

namespace App\Domain\Licenca\LicencaSegmento\Controller;

use App\Domain\Licenca\LicencaSegmento\Requests\StoreLicencaSegmentoRequest;
use App\Domain\Licenca\LicencaSegmento\Services\LicencaSegmentoService;
use App\Shared\Http\Controllers\Controller;

class StoreLicencaSegmentoController extends Controller
{
    public function __construct(private readonly LicencaSegmentoService $listagemLicencaSegmento)
    { }

    public function index(StoreLicencaSegmentoRequest $request): \Illuminate\Http\RedirectResponse
    {
        $parameters = $this->listagemLicencaSegmento->create(post: $request->all());

        return to_route(
                route: 'licenca.create',
                parameters: [
                    'tipos'             => $parameters['licenca']['tipos'],
                    'licenca'           => $parameters['licenca']['licenca'],
                    'licencaSegmento'   => $parameters['licenca']['licencaSegmento'],
                    'brs'               => $parameters['licenca']['brs'],
                ])
            ->with('message', $parameters['request']);
    }
}
