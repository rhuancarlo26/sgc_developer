<?php

namespace App\Domain\Servico\MonAtpFauna\Execucao\Campanhas\app\Controller;

use App\Domain\Servico\MonAtpFauna\Execucao\Campanhas\app\Requests\StoreRequest;
use App\Domain\Servico\MonAtpFauna\Execucao\Campanhas\app\Services\CampanhasService;
use App\Domain\Servico\MonAtpFauna\Execucao\Campanhas\app\Services\ExecucaoCampanhaService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DeleteRecursoEquipeController extends Controller
{
  public function __construct(
    private readonly CampanhasService $campanhasService,
    private readonly ExecucaoCampanhaService $execucaoCampanhaService
  ) {}

  public function index(Request $request)
  {
    $response = $this->campanhasService->delete_equipe(post: $request->all());

    return response()->json([
      'data' => $this->execucaoCampanhaService->getRelationshipCampanha($request->equipe['at_fauna_execucao_campanha_id']),
      'message' => $response['request']['content']
    ]);
  }
}
