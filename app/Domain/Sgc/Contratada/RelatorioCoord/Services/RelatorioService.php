<?php

namespace App\Domain\Sgc\Contratada\RelatorioCoord\Services;

use App\Models\SgcRelatorioCoordenacao;
use App\Models\SgcRelatorioUpload;
use App\Shared\Abstract\BaseModelService;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Contrato;

class RelatorioService extends BaseModelService
{
  protected string $modelClass = SgcRelatorioCoordenacao::class;

  public function index(Contrato $contrato): Response
    {
      return Inertia::render('Sgc/Contratada/Relatorio/Index', [
          'contrato' => $contrato
      ]);
    }
}
