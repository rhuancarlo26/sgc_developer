<?php

namespace App\Domain\Licenca\LicencaSegmento\AppModule\Controller;

use App\Domain\Licenca\LicencaSegmento\AppModule\Services\LicencaSegmentoService;
use App\Models\LicencaSegmento;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class DeleteLicencaSegmentoController extends Controller
{
    public function __construct(private readonly LicencaSegmentoService $licencaSegmentoService)
    {
    }

    public function index(LicencaSegmento $segmento): RedirectResponse
    {
        $parameters = $this->licencaSegmentoService->delete($segmento->getAttributes());

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
