<?php

namespace App\Domain\Licenca\app\Controller;

use App\Domain\Licenca\app\Services\LicencaBRService;
use App\Domain\Licenca\app\Services\LicencaTipoService;
use App\Shared\Http\Controllers\Controller;
use App\Models\Licenca;
use App\Models\LicencaSegmento;
use Inertia\Inertia;

class CreateLicencaController extends Controller
{
    public function __construct(
        private readonly LicencaTipoService $licencaTipoService,
        private readonly LicencaBRService   $licencaBRService
    )
    {
    }

    public function index(Licenca|null $licenca): \Inertia\Response
    {
        $tipos = $this->licencaTipoService->getLicencaTipo();
        $brs   = $this->licencaBRService->getLicencaBR();
        $licencaSegmento = LicencaSegmento::where('licenca_id', $licenca->id)->get();

        return Inertia::render(component: 'Licenca/Form', props: [
            'tipos'           => $tipos,
            'licenca'         => $licenca,
            'licencaSegmento' => $licencaSegmento,
            'brs'             => $brs,
        ]);
    }
}
