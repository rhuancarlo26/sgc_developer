<?php

namespace App\Domain\Modulos\Importador\Controllers;

use App\Domain\Modulos\Importador\Services\ImportadorService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CreateImportadorController extends Controller
{
    public function __construct(
        private ImportadorService $service
    ) {
        // 
    }

    public function create(): Response
    {
        return Inertia::render('Modulos/Importador/Form', [
            'modulos' => $this->service->buscarModulos(),
            'contratos' => $this->service->buscarContratos(),
        ]);
    }
}
