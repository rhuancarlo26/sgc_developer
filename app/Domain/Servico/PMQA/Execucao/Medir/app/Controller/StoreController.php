<?php

namespace App\Domain\Servico\PMQA\Execucao\Medir\app\Controller;

use App\Domain\Servico\PMQA\Execucao\Medir\app\Requests\StoreRequest;
use App\Domain\Servico\PMQA\Execucao\Medir\app\Services\MedicaoService;
use App\Models\Contrato;
use App\Models\ServicoPmqaCampanha;
use App\Models\ServicoPmqaCampanhaPonto;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class StoreController extends Controller
{
    public function __construct(private readonly MedicaoService $medicaoService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, ServicoPmqaCampanha $campanha, ServicoPmqaCampanhaPonto $ponto, StoreRequest $request): RedirectResponse
    {
        $post = [];

        $post['fk_campanha_ponto'] = $request->validated('fk_campanha_ponto');
        $post['campanha_id'] = $campanha->id;
        $post['medido'] = $request->validated('medido');

        if ($request->validated('medido') === true) {
            $post['iqa'] = null;
            $post['parametros'] = null;
            $post['observacao'] = $request->validated('observacao');
        } else {
            $post['iqa'] = $request->validated('iqa');
            $post['parametros'] = array_filter($request->validated('parametros'), function ($q) {
                return !is_null($q);
            });
            $post['observacao'] = null;
        }

        $response = $this->medicaoService->store($post);

        return to_route('contratos.contratada.servicos.pmqa.execucao.medir.create', ['contrato' => $contrato->id, 'servico' => $servico->id, 'campanha' => $campanha->id, 'ponto' => $ponto->id])->with('message', $response['request']);
    }
}
