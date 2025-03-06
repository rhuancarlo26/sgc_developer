<?php

namespace App\Domain\Licenca\app\Controller;

use App\Domain\Licenca\app\Services\LicencaService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IndexController extends Controller
{
    public function __construct(private readonly LicencaService $listagemLicenca)
    {
    }

    public function index(Request $request, $arquivo = false): Response
    {
        $searchParams = $request->all('columns', 'value');

        $licencas = $this->listagemLicenca->get(searchParams: $searchParams, arquivado: $arquivo);

        return Inertia::render(component: 'Licenca/Index', props: [
            "licencas" => $licencas
        ]);
    }
}
