<?php

namespace App\Domain\Servico\PMQA\Execucao\Coleta\app\Controller;

use App\Domain\Servico\PMQA\Execucao\Coleta\app\Requests\UpdateRequest;
use App\Domain\Servico\PMQA\Execucao\Coleta\app\Services\ColetaService;
use App\Models\Contrato;
use App\Models\ServicoPmqaCampanha;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class UpdateController extends Controller
{
    public function __construct(private readonly ColetaService $coletaService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, ServicoPmqaCampanha $campanha, UpdateRequest $request): RedirectResponse
    {
        $post = [];

        $post['id']                = $request->validated('id');
        $post['campanha_ponto_id'] = $request->validated('campanha_ponto_id');
        $post['data_coleta']       = $request->validated('data_coleta');
        $post['sem_coleta']        = $request->validated('sem_coleta');

        if ($request->validated('sem_coleta') === true) {
            $post['numero_amostra']           = null;
            $post['preservacao_amostra']      = null;
            $post['acondicionamento_amostra'] = null;
            $post['transporte_amostra']       = null;
            $post['justificativa']            = $request->validated('justificativa');
        } else {
            $post['numero_amostra']           = $request->validated('numero_amostra');
            $post['preservacao_amostra']      = $request->validated('preservacao_amostra');
            $post['acondicionamento_amostra'] = $request->validated('acondicionamento_amostra');
            $post['transporte_amostra']       = $request->validated('transporte_amostra');
            $post['justificativa']            = null;
        }

        $response = $this->coletaService->update($post);

        return to_route('contratos.contratada.servicos.pmqa.execucao.coleta.create', [
            'contrato' => $contrato->id,
            'servico'  => $servico->id,
            'campanha' => $campanha->id,
            'ponto'    => $request->fk_campanha_ponto
        ])->with('message', $response['request']);
    }
}
