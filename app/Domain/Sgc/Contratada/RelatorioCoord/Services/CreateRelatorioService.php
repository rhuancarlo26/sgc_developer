<?php

namespace App\Domain\Sgc\Contratada\RelatorioCoord\Services;

use App\Models\SgcRelatorioUpload;
use App\Models\SgcRelatorioCoordenacao;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;
use Illuminate\Support\Facades\Storage;

class CreateRelatorioService extends BaseModelService
{
  use Searchable, Deletable;

  protected string $modelClass = SgcRelatorioUpload::class;

  public function obterItensSgcRelatorioCoordenacao()
  {
      return SgcRelatorioCoordenacao::all();
  }
  
  
  
}
