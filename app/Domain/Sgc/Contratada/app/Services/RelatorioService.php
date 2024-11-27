<?php

namespace App\Domain\Sgc\Contratada\app\Services;

use App\Models\SgcRelatorioCoordenacao;
use App\Models\SgcRelatorioUpload;
use App\Shared\Abstract\BaseModelService;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Contrato;
use App\Shared\Traits\Searchable;

class RelatorioService extends BaseModelService
{
  use Searchable;
  protected string $modelClass = SgcRelatorioCoordenacao::class;

  public function index(Contrato $contrato): Response
    {
      return Inertia::render('Sgc/Contratada/Relatorio/Index', [
          'contrato' => $contrato
      ]);
    }
}
