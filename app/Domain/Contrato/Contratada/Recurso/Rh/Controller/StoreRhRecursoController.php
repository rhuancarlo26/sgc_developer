<?php

namespace App\Domain\Contrato\Contratada\Recurso\Rh\Controller;

use App\Domain\Contrato\Contratada\Recurso\Rh\Services\RhRecursoService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StoreRhRecursoController extends Controller
{
    public function __construct(private readonly RhRecursoService $rhRecursoService)
    {
    }

    public function index(Request $request): \Illuminate\Http\RedirectResponse
    {
        $response = $this->rhRecursoService->salvarRh($request->all());
        
        return to_route(route: 'contratos.contratada.recurso.rh.create', parameters: ['contrato' => $request->id_contrato, 'rh' => $response['rh']])
            ->with('message', $response['request']);
    }
}
