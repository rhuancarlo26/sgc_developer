<?php

namespace App\Domain\Modulos\Importador\Controllers;

use App\Domain\Modulos\Importador\Services\ImportadorService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ImportadorController extends Controller
{
    public function __construct(
        private ImportadorService $service
    ) {
        // 
    }

    public function index(Request $request): Response
    {
        $searchParams = $request->all('columns', 'value');

        $data = $this->service->buscarImportadores($searchParams);
        return Inertia::render('Modulos/Importador/Index', $data);
    }
}
