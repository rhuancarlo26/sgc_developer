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
        $servicoAtual = Servicos::query()
            ->where('id', $request->id)
            ->whereNull('deleted_at')
            ->firstOrFail();

        $acao = $request->input('acao');
        $temaId = $servicoAtual->tema_servico;
        $servicoModImpId = $servicoAtual->servico_mod_imp_id;
        $registroLegado = empty($servicoAtual->servico_mod_imp_id);
        $modoVincularServico =
            $registroLegado &&
            $acao === 'vincular-servico';

        if ($modoVincularServico) {
            $servicoModImpId = data_get(
                $request->input('tipo'),
                'id',
                $request->input('tipo')
            );

            $servicoModImpId = $servicoModImpId
                ?: $request->input('servico_mod_imp_id');

            if (!$servicoModImpId) {
                throw ValidationException::withMessages([
                    'tipo' => 'O campo Serviço é obrigatório.',
                ]);
            }
        }

        if (!$temaId) {
            throw ValidationException::withMessages([
                'tema' => 'O registro não possui Tema vinculado.',
            ]);
        }

        if ($servicoModImpId) {
            $duplicado = Servicos::query()
                ->where('id_contrato', $servicoAtual->id_contrato)
                ->where('tema_servico', $temaId)
                ->where('servico_mod_imp_id', $servicoModImpId)
                ->where('id', '!=', $servicoAtual->id)
                ->whereNull('deleted_at')
                ->exists();

            if ($duplicado) {
                throw ValidationException::withMessages([
                    'tipo' => 'Já existe um registro com este contrato, tema e serviço.',
                ]);
            }
        }

        $post = [
            ...$request->all(),

            'id_contrato' => $servicoAtual->id_contrato,
            'tema_servico' => $temaId,
            'servico_mod_imp_id' => $servicoModImpId,
            'servico' => $servicoAtual->servico,
        ];

        $response = $this->servicoService->updateServico($post);

        $parameters = [
            'contrato' => $servicoAtual->id_contrato,
            'servico' => $servicoAtual->id,
        ];

        if ($acao === 'editar') {
            $parameters['acao'] = 'editar';
        }

        return to_route(
            'contratos.contratada.servicos.create',
            $parameters
        )->with('message', $response['request']);
    }
}
