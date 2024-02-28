<?php

namespace App\Domain\Licenca\AppModule\Controller;

use App\Domain\Licenca\AppModule\Services\LicencaService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ListagemLicencaController extends Controller
{
    public function __construct(private readonly LicencaService $listagemLicenca)
    {
    }

    public function index(Request $request): Response
    {
        return Inertia::render(
            component: 'Licenca/Index'
        );
    }
}