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

        $contexto = $request->only([
            'contrato_id',
            'modulo_id',
            'servico_id',
            'tema_id',
            'origem_servico',
        ]);

        $filtros = $request->only([
            'filtro_modulo_id',
            'filtro_tema_id',
            'campanha',
            'updated_at',
        ]);

        $data = $this->service->buscarImportadores($searchParams, $contexto, $filtros);

        return Inertia::render('Modulos/Importador/Index', $data);
    }
}
