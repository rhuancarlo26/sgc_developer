<?php

namespace App\Domain\Servico\app\Controller;

use App\Domain\Servico\app\Services\ServicoService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\Servicos;

class StoreServicosContratadaController extends Controller
{
    public function __construct(private readonly ServicoService $servicoService) {}

    public function index(Request $request)
    {
        $servicoId = data_get($request->input('tipo'), 'id', $request->input('tipo'));
        $temaId = data_get($request->input('tema'), 'id', $request->input('tema'));

        if (!$servicoId || !$temaId) {
            throw ValidationException::withMessages([
                'tipo' => 'O campo Serviço é obrigatório.',
                'tema' => 'O campo Tema é obrigatório.',
            ]);
        }

        $duplicado = Servicos::query()
            ->where('id_contrato', $request->id_contrato)
            ->where('tema_servico', $temaId)
            ->where('servico', $servicoId)
            ->whereNull('deleted_at')
            ->exists();

        if ($duplicado) {
            throw ValidationException::withMessages([
                'tipo' => 'Já existe um registro com este contrato, tema e serviço.',
            ]);
        }

        $post = [
            ...$request->all(),
            'servico' => $servicoId,
            'tema_servico' => $temaId,
        ];

        $response = $this->servicoService->storeServico($post);

        return to_route('contratos.contratada.servicos.create', [
            'contrato' => $request->id_contrato,
            'servico' => $response['servico']
        ])->with('message', $response['request']);
    }
}
