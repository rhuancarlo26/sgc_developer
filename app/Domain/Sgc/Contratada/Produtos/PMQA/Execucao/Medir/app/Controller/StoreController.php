<?php

namespace App\Domain\Sgc\Contratada\Produtos\PMQA\Execucao\Medir\app\Controller;

use App\Domain\Sgc\Contratada\Produtos\PMQA\Execucao\Medir\app\Requests\StoreRequest;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Execucao\Medir\app\Services\MedicaoService;
use App\Models\Contrato;
use App\Models\ServicoPmqaCampanha;
use App\Models\ServicoPmqaCampanhaPonto;
use App\Models\Servicos;
use App\Models\SgcPmqa;
use App\Models\SgcPmqaCampanha;
use App\Models\SgcPmqaCampanhasPonto;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class StoreController extends Controller
{
    public function __construct(private readonly MedicaoService $medicaoService)
    {
    }

    public function index(Contrato $contrato, string $produto, SgcPmqa $pmqa, SgcPmqaCampanha $campanha, SgcPmqaCampanhasPonto $ponto, StoreRequest $request): RedirectResponse
    {
        $post = [];

        $post['campanha_ponto_id'] = $request->validated('campanha_ponto_id');
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

        return to_route('contratos.contratada.sgc.pmqa.execucao.medir.create', ['contrato' => $contrato->id, 'produto' => $produto, 'pmqa' => $pmqa->id, 'campanha' => $campanha->id, 'ponto' => $ponto->id])->with('message', $response['request']);
    }
}
