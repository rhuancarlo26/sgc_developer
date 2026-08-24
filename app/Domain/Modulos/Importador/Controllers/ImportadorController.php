<?php

namespace App\Domain\Modulos\Importador\Controllers;

use App\Domain\Modulos\Importador\Services\ImportadorService;
use App\Models\Modulo;
use App\Models\ServicoTema;
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

        $contexto = [
            'contrato_id' => $request->input('contrato_id'),
            'modulo_id' => $request->input('modulo_id'),
            'servico_id' => $request->input('servico_id'),
            'tema_id' => $request->input('tema_id'),
            'origem_servico' => $request->boolean('origem_servico'),
            'origem_fiscal' => $request->boolean('origem_fiscal'),
            'modo_fiscal' => $request->boolean('modo_fiscal') || $request->boolean('origem_fiscal'),
        ];

        $contexto = array_filter($contexto, function ($valor) {
            return $valor !== null && $valor !== '';
        });

        if (!empty($contexto['servico_id'])) {
            $servico = Servicos::query()
                ->with([
                    'tema:id,nome_tema',
                    'moduloImportado:id,nome,pmqa,contrato_id',
                    'moduloImportado.contrato:id,numero_contrato',
                ])
                ->find($contexto['servico_id']);

            if ($servico) {
                $contexto['contrato_id'] = $contexto['contrato_id'] ?? $servico->id_contrato;
                $contexto['tema_id'] = $contexto['tema_id'] ?? $servico->tema_servico;
                $contexto['modulo_id'] = $contexto['modulo_id'] ?? $servico->servico_mod_imp_id;
                $contexto['origem_servico'] = true;

                $contexto['tema'] = $servico->tema;
                $contexto['modulo'] = $servico->moduloImportado;
            }
        }

        if (empty($contexto['tema']) && !empty($contexto['tema_id'])) {
            $contexto['tema'] = ServicoTema::query()
                ->select(['id', 'nome_tema'])
                ->find($contexto['tema_id']);
        }

        if (empty($contexto['modulo']) && !empty($contexto['modulo_id'])) {
            $contexto['modulo'] = Modulo::query()
                ->with(['contrato:id,numero_contrato'])
                ->select(['id', 'nome', 'pmqa', 'contrato_id'])
                ->find($contexto['modulo_id']);
        }

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
