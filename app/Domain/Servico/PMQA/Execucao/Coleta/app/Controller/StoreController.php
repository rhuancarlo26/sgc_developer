<?php

namespace App\Domain\Servico\PMQA\Execucao\Coleta\app\Controller;

use App\Domain\Servico\PMQA\Execucao\Coleta\app\Requests\StoreRequest;
use App\Domain\Servico\PMQA\Execucao\Coleta\app\Services\ColetaService;
use App\Models\Contrato;
use App\Models\ServicoPmqaCampanha;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class StoreController extends Controller
{
    public function __construct(private readonly ColetaService $coletaService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, ServicoPmqaCampanha $campanha, StoreRequest $request): RedirectResponse
    {
        $post = [];

        $post['fk_campanha_ponto'] = $request->validated('fk_campanha_ponto');
        $post['dt_coleta'] = $request->validated('dt_coleta');
        $post['sem_coleta'] = $request->validated('sem_coleta');

        if ($request->validated('sem_coleta') === true) {
            $post['numero_amostra'] = '';
            $post['preservacao_amostra'] = '';
            $post['acondicionamento_amostra'] = '';
            $post['transporte_amostra'] = '';
            $post['observacao'] = $request->validated('observacao');
        } else {
            $post['numero_amostra'] = $request->validated('numero_amostra');
            $post['preservacao_amostra'] = $request->validated('preservacao_amostra');
            $post['acondicionamento_amostra'] = $request->validated('acondicionamento_amostra');
            $post['transporte_amostra'] = $request->validated('transporte_amostra');
            $post['observacao'] = '';
        }

        $response = $this->coletaService->store($post);

        return to_route('contratos.contratada.servicos.pmqa.execucao.coleta.create', ['contrato' => $contrato->id, 'servico' => $servico->id, 'campanha' => $campanha->id, 'ponto' => $request->fk_campanha_ponto])->with('message', $response['request']);
    }
}
