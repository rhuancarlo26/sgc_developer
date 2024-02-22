<?php

namespace App\Domain\Licenca\Controller;

use App\Domain\Licenca\Services\LicencaTipoService;
use App\Shared\Http\Controllers\Controller;
use App\Models\Licenca;
use Inertia\Inertia;

class CreateLicencaController extends Controller
{
    public function __construct(private readonly LicencaTipoService $licencaTipoService)
    {
    }

    public function index(Licenca|null $licenca): \Inertia\Response
    {
        $tipos = $this->licencaTipoService->getLicencaTipo();

        return Inertia::render(component: 'Licenca/Form', props: [
            'tipos'   => $tipos,
            'licenca' => $licenca
        ]);
    }
}
