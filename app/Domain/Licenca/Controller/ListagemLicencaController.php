<?php

namespace App\Domain\Licenca\Controller;

use App\Domain\Licenca\Services\ListagemLicencaService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ListagemLicencaController extends Controller
{
    public function __construct(private readonly ListagemLicencaService $listagemLicenca)
    {
    }

    public function index(Request $request): Response
    {
        $searchParams = $request->all('searchColumn', 'searchValue');

        return Inertia::render('Licenca/Index', [
            'licencas' => $this->listagemLicenca
                ->search(...$searchParams)
                ->with(['tipo', 'requerimentos'])
                ->paginate()
                ->appends($searchParams),
        ]);
    }
}