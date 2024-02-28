<?php

namespace App\Domain\Licenca\LicencaSegmento\AppModule\Controller;

use App\Domain\Licenca\LicencaSegmento\AppModule\Requests\StoreLicencaSegmentoRequest;
use App\Domain\Licenca\LicencaSegmento\AppModule\Services\LicencaSegmentoService;
use App\Models\LicencaSegmento;
use App\Shared\Http\Controllers\Controller;

class UpdateLicencaSegmentoController extends Controller
{
    public function __construct(private readonly LicencaSegmentoService $licencaSegmentoService)
    {
    }

    public function index(LicencaSegmento $segmento): \Illuminate\Http\RedirectResponse
    {
        $parameters = $this->licencaSegmentoService->update(post: $segmento->getAttributes());

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
