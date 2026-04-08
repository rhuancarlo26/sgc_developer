<?php

namespace App\Domain\Modulos\ConfiguracoesModulos\Controllers;

use App\Domain\Modulos\ConfiguracoesModulos\Services\ConfiguracoesModulosService;
use App\Models\User;
use App\Shared\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class ConfiguracoesModulosController extends Controller
{
    public function __construct(
        private ConfiguracoesModulosService $service
    ) {
        // 
    }

    public function index(): Response
    {
        $data = $this->service->buscarModulos();
        return Inertia::render('Modulos/ConfigModulos/Index', $data);
    }
}
