<?php

namespace App\Domain\Servico\PMQA\Resultado\app\Controller;

use App\Domain\Servico\PMQA\Resultado\app\Requests\StoreOutraAnaliseRequest;
use App\Domain\Servico\PMQA\Resultado\app\Services\ResultadoService;
use App\Models\Contrato;
use App\Models\ServicoPmqaResultado;
use App\Models\ServicoPmqaResultadoOutraAnalise;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use App\Shared\Utils\ArquivoUtils;
use Illuminate\Http\Response;

class VisualizarOutraAnaliseController extends Controller
{
  public function __construct(private readonly ResultadoService $resultadoService)
  {
  }

  public function index(Contrato $contrato, Servicos $servico, ServicoPmqaResultado $resultado, ServicoPmqaResultadoOutraAnalise $outra_analise): Response
  {
    $arquivo = new ArquivoUtils();
    return $arquivo->visualizar($outra_analise->caminho);
  }
}