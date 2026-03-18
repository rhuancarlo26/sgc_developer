<?php

namespace App\Domain\Sgc\Contratada\Produtos\PMQA\Resultado\app\Controller;

use App\Domain\Sgc\Contratada\Produtos\PMQA\Resultado\app\Requests\StoreOutraAnaliseRequest;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Resultado\app\Services\ResultadoService;
use App\Models\Contrato;
use App\Models\ServicoPmqaResultado;
use App\Models\ServicoPmqaResultadoOutraAnalise;
use App\Models\Servicos;
use App\Models\SgcPmqa;
use App\Models\SgcPmqaResultado;
use App\Models\SgcPmqaResultadoOutraAnalise;
use App\Shared\Http\Controllers\Controller;
use App\Shared\Utils\ArquivoUtils;
use Illuminate\Http\Response;

class VisualizarOutraAnaliseController extends Controller
{
  public function __construct(private readonly ResultadoService $resultadoService)
  {
  }

  public function index(Contrato $contrato, SgcPmqa $pmqa, SgcPmqaResultado $resultado, SgcPmqaResultadoOutraAnalise $outra_analise): Response
  {
    $arquivo = new ArquivoUtils();
    return $arquivo->visualizar($outra_analise->caminho);
  }
}
