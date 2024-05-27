<?php

namespace App\Domain\Licenca\app\Controller;

use App\Domain\Licenca\app\Services\LicencaService;
use App\Shared\Http\Controllers\Controller;
use App\Models\Licenca;
use Inertia\Inertia;
use Inertia\Response;

class CreateLicencaController extends Controller
{
    public function __construct(private readonly LicencaService $licencaService)
    {
    }

    public function index(Licenca|null $licenca): Response
    {
        $licenca?->load([
            'tipo',
            'documento',
            'shapefile',
            'segmentos'
        ]);

        $response = $this->licencaService->create();

        return Inertia::render(component: 'Licenca/Form', props: [
            'licenca'         => $licenca,
            ...$response
        ]);
    }
}
