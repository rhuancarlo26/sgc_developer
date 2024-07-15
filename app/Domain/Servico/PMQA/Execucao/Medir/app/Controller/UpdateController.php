<?php

namespace App\Domain\Servico\PMQA\Execucao\Medir\app\Controller;

use App\Domain\Servico\PMQA\Execucao\Medir\app\Requests\UpdateRequest;
use App\Domain\Servico\PMQA\Execucao\Medir\app\Services\MedicaoService;
use App\Models\Contrato;
use App\Models\ServicoPmqaCampanha;
use App\Models\ServicoPmqaCampanhaPonto;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class UpdateController extends Controller
{
  public function __construct(private readonly MedicaoService $medicaoService)
  {
  }

  public function index(Contrato $contrato, Servicos $servico, ServicoPmqaCampanha $campanha, ServicoPmqaCampanhaPonto $ponto, UpdateRequest $request): RedirectResponse
  {
    $post = [];

    $post['id']                 = $request->validated('id');
    $post['campanha_id']        = $request->validated('campanha_id');
    $post['campanha_ponto_id']  = $request->validated('campanha_ponto_id');
    $post['sem_coleta']         = $request->validated('sem_coleta');

    if ($request->validated('sem_coleta') === true) {
      $post['iqa']              = null;
      $post['parametros']       = null;
      $post['justificativa']    = $request->validated('justificativa');
    } else {
      $post['iqa']              = $request->validated('iqa');
      $post['parametros']       = array_filter($request->validated('parametros'), function ($q) {
        return !is_null($q);
      });
      $post['justificativa']    = null;
    }

    $response = $this->medicaoService->update($post);

    return to_route('contratos.contratada.servicos.pmqa.execucao.medir.create', ['contrato' => $contrato->id, 'servico' => $servico->id, 'campanha' => $campanha->id, 'ponto' => $ponto->id])->with('message', $response['request']);
  }
}