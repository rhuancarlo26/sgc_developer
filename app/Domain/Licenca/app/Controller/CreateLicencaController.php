<?php

namespace App\Domain\Licenca\app\Controller;

use App\Domain\Licenca\app\Services\LicencaBRService;
use App\Domain\Licenca\app\Services\LicencaTipoService;
use App\Domain\Licenca\LicencaSegmento\Services\LicencaSegmentoService;
use App\Models\LicencaSegmento;
use App\Shared\Http\Controllers\Controller;
use App\Models\Licenca;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CreateLicencaController extends Controller
{
    public function __construct(
        private readonly LicencaTipoService     $licencaTipoService,
        private readonly LicencaBRService       $licencaBRService,
        private readonly LicencaSegmentoService $licencaSegmentoService,
    )
    {
    }

    public function index(Licenca|null $licenca, Request $request): \Inertia\Response
    {
        $searchParams = $request->all('searchColumn', 'searchValue');

        $tipos           = $this->licencaTipoService->getLicencaTipo();
        $brs             = $this->licencaBRService->getLicencaBR();
        $licencaSegmento = $this->licencaSegmentoService->get($licenca->id, $searchParams);
        $licenca?->load([
            'documento'
        ]);

        return Inertia::render(component: 'Licenca/Form', props: [
            'tipos'           => $tipos,
            'licenca'         => $licenca,
            'licencaSegmento' => $licencaSegmento,
            'brs'             => $brs,
        ]);
    }
}
