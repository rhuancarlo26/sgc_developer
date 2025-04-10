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

class SaveTrechosController extends Controller
{
  public function __construct(private readonly CampanhasService $campanhasService) {}

  public function __invoke(Request $request)
  {
    return response()->json($this->campanhasService->store_trechos(post: $request->all()));
  }
}
