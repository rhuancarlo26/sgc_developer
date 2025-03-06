<?php

namespace App\Domain\Servico\ContOcorrencia\Execucao\ACA\Controller;

use App\Domain\Servico\ContOcorrencia\Execucao\ACA\Requests\StoreRequest;
use App\Domain\Servico\ContOcorrencia\Execucao\ACA\Services\ACAService;
use App\Domain\Servico\ContOcorrencia\Execucao\ControleRNC\Services\ControleRNCService;
use App\Models\Contrato;
use App\Models\ServicoConOcorrSupervisaoExecAca;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class StoreController extends Controller
{
    public function __construct(private readonly ACAService $ACAService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, StoreRequest $request): RedirectResponse
    {
        $aca = ServicoConOcorrSupervisaoExecAca::where('id_servico', $servico->id)->orderBy('id', 'desc')->first()->num_por_servico ?? 0;
        $num_por_servico = str_pad($aca + 1, 2, '0', STR_PAD_LEFT);

        $post = [
            ...$request->validated(),
            'id_servico' => $servico->id,
            'num_por_servico' => $num_por_servico,
            'nome_id' => 'ACA.' . $num_por_servico,
            'enviado' => 'Não'
        ];

        $response = $this->ACAService->store(post: $post);

        return to_route('contratos.contratada.servicos.cont_ocorrencia.execucao.aca.index', ['contrato' => $contrato->id, 'servico' => $servico->id])->with('message', $response['request']);
    }
}
