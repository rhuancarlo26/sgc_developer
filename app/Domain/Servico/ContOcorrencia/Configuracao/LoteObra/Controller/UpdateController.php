<?php

namespace App\Domain\Servico\ContOcorrencia\Configuracao\LoteObra\Controller;

use App\Domain\Servico\ContOcorrencia\Configuracao\LoteObra\Requests\StoreRequest;
use App\Domain\Servico\ContOcorrencia\Configuracao\LoteObra\Services\LoteObraService;
use App\Models\Contrato;
use App\Models\ServicoContOcorrSupervisaoConfigLote;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UpdateController extends Controller
{
  public function __construct(private readonly LoteObraService $loteObraService)
  {
  }

  public function index(Contrato $contrato, Servicos $servico, Request $request): RedirectResponse
  {
    $post = [
      ...$request->all(),
      'id_rodovia' => $request->rodovia['id'],
      'id_uf' => $request->rodovia['uf']['id']
    ];
    $response = $this->loteObraService->update(post: $post);

    return to_route('contratos.contratada.servicos.cont_ocorrencia.configuracao.lote_obra.create', ['contrato' => $contrato->id, 'servico' => $servico->id, 'lote' => $request->id])->with('message', $response['request']);
  }
}
