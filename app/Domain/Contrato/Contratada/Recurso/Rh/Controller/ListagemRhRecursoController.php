<?php

namespace App\Domain\Contrato\Contratada\Recurso\Rh\Controller;

use App\Domain\Contrato\Contratada\Recurso\Rh\Services\RhRecursoService;
use App\Models\Contrato;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ListagemRhRecursoController extends Controller
{
    public function __construct(private readonly RhRecursoService $rhRecursoService)
    {
    }

    public function index(Contrato $contrato, Request $request)
    {
        $searchParams = $request->all('searchColumn', 'searchValue');

        $response = $this->rhRecursoService->listagemRh($contrato, $searchParams);

        return Inertia::render('Contrato/Contratada/Recurso/Rh/Index', [
            'contrato' => $contrato,
            ...$response
        ]);
    }
}
