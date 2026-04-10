<?php

namespace App\Domain\Modulos\ConfiguracoesModulos\Controllers;

use App\Domain\Modulos\ConfiguracoesModulos\Services\ConfiguracoesModulosService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ConfiguracoesModulosController extends Controller
{
    public function __construct(
        private ConfiguracoesModulosService $service
    ) {
        // 
    }

    public function index(Request $request): Response
    {
        $searchParams = $request->all('columns', 'value');

        $data = $this->service->buscarModulos($searchParams);
        return Inertia::render('Modulos/ConfigModulos/Index', $data);
    }
}
