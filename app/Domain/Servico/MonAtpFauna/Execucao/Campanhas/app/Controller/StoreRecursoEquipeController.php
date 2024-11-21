<?php

namespace App\Domain\Servico\MonAtpFauna\Execucao\Campanhas\app\Controller;

use App\Domain\Servico\MonAtpFauna\Execucao\Campanhas\app\Requests\StoreRequest;
use App\Domain\Servico\MonAtpFauna\Execucao\Campanhas\app\Services\CampanhasService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StoreRecursoEquipeController extends Controller
{
  public function __construct(private readonly CampanhasService $campanhasService) {}

  public function index(Request $request): RedirectResponse
  {
    $response = $this->campanhasService->store_equipe(post: $request->all());

    return redirect()->back()->with('message', $response['request']);
  }
}
