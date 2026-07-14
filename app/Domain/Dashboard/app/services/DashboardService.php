<?php

namespace App\Domain\Dashboard\app\services;

use App\Models\Contrato;
use App\Models\Rodovia;
use App\Models\ServicoLicencaCondicionante;
use App\Models\Uf;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;
use Illuminate\Support\Facades\Cache;

class DashboardService extends BaseModelService
{
  use Searchable, Deletable;

  protected string $modelClass = ServicoLicencaCondicionante::class;

  public function index(): array
  {
    $ufs = Cache::rememberForever('ufs', fn() => Uf::all());
    $rodovias = Cache::rememberForever('rodovias', fn() => Rodovia::all());
    $contratos = Contrato::all(['numero_contrato']);

    return [
      'ufs' => $ufs,
      'rodovias' => $rodovias,
      'contratos' => $contratos
    ];
  }
}
