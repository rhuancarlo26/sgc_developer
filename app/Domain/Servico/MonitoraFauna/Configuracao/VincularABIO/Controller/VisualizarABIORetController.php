<?php

namespace App\Domain\Servico\MonitoraFauna\Configuracao\VincularABIO\Controller;

use App\Domain\Servico\MonitoraFauna\Configuracao\VincularABIO\Requests\StoreRequest;
use App\Domain\Servico\MonitoraFauna\Configuracao\VincularABIO\Services\VincularABIOService;
use App\Models\Contrato;
use App\Models\ServicoMonitoraFaunaConfigAbioRet;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VisualizarABIORetController extends Controller
{
  public function index(Contrato $contrato, Servicos $servico, ServicoMonitoraFaunaConfigAbioRet $abio_ret)
  {
    return response()->file(storage_path('app') . DIRECTORY_SEPARATOR . $abio_ret->caminho_ret);
  }
}
