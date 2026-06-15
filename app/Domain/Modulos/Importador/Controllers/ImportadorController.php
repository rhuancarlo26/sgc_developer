<?php

namespace App\Domain\Modulos\Importador\Controllers;

use App\Domain\Modulos\Importador\Services\ImportadorService;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
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

    public function index(Request $request): Response|RedirectResponse
    {
        $searchParams = $request->all('columns', 'value');

        $contexto = $request->only([
            'contrato_id',
            'modulo_id',
            'servico_id',
            'tema_id',
            'origem_servico',
        ]);

        if (!$this->servicoAprovado($contexto['servico_id'] ?? null)) {
            return back()->with('message', [
                'type' => 'warning',
                'content' => 'O importador só será habilitado após a aprovação do fiscal no cadastro do serviço.',
            ]);
        }

        $filtros = $request->only([
            'filtro_modulo_id',
            'filtro_tema_id',
            'campanha',
            'updated_at',
        ]);

        $data = $this->service->buscarImportadores($searchParams, $contexto, $filtros);

        return Inertia::render('Modulos/Importador/Index', $data);
    }

    private function servicoAprovado(?int $servicoId): bool
    {
        if (!$servicoId) {
            return true;
        }

        return Servicos::query()
            ->where('id', $servicoId)
            ->where('status_aprovacao', 3)
            ->exists();
    }
}
