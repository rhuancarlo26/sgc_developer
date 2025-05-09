<?php

namespace App\Domain\Dashboard\SupressaoVegetal\Controller;

use App\Domain\Servico\PMQA\app\Utils\ConfigucacaoParecer;
use App\Domain\Servico\PMQA\Configuracao\Parametro\Services\ParametroService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IndexDashboardSupressaoVegetalController extends Controller
{
  public function __construct() {}

  public function index(): Response
  {
    return Inertia::render('Dashboard/SupressaoVegetal/Index');
  }
}
