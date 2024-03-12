<?php

namespace App\Domain\Licenca\Requerimento\LicencaSegmento\Controller;

use App\Domain\Licenca\Requerimento\LicencaSegmento\Services\LicencaSegmentoService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UpdateLicencaSegmentoController extends Controller
{
    public function __construct(private readonly LicencaSegmentoService $licencaSegmentoService)
    {
    }

    public function index(Request $request): \Illuminate\Http\RedirectResponse
    {
        $parameters = $this->licencaSegmentoService->update(post: $request->All());

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
