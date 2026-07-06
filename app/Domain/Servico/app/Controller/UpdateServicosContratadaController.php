<?php

namespace App\Domain\Servico\app\Controller;

use App\Domain\Servico\app\Services\ServicoService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\Servicos;

class UpdateServicosContratadaController extends Controller
{
    public function __construct(private readonly ServicoService $servicoService) {}

    public function index(Request $request)
    {
        $servicoModImpId = data_get($request->input('tipo'), 'id', $request->input('tipo'));
        $temaId = data_get($request->input('tema'), 'id', $request->input('tema'));

        if (!$servicoModImpId || !$temaId) {
            throw ValidationException::withMessages([
                'tipo' => 'O campo Serviço é obrigatório.',
                'tema' => 'O campo Tema é obrigatório.',
            ]);
        }

        $duplicado = Servicos::query()
            ->where('id_contrato', $request->id_contrato)
            ->where('tema_servico', $temaId)
            ->where('servico_mod_imp_id', $servicoModImpId)
            ->where('id', '!=', $request->id)
            ->whereNull('deleted_at')
            ->exists();

        if ($duplicado) {
            throw ValidationException::withMessages([
                'tipo' => 'Já existe um registro com este contrato, tema e serviço.',
            ]);
        }

        $post = [
            ...$request->all(),
            'tema_servico' => $temaId,
            'servico_mod_imp_id' => $servicoModImpId,
        ];

        $response = $this->servicoService->updateServico($post);

        return to_route('contratos.contratada.servicos.create', [
            'contrato' => $request->id_contrato,
            'servico' => $request->id
        ])->with('message', $response['request']);
    }
}
