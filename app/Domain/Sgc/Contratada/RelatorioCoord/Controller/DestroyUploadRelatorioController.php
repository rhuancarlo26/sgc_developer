<?php

namespace App\Domain\Sgc\Contratada\RelatorioCoord\Controller;

use App\Domain\Contrato\Contratada\DadosGerais\Anexo\Services\AnexoService;
use App\Models\ContratoAnexo;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class DestroyUploadRelatorioController extends Controller
{
  public function __construct(private readonly AnexoService $anexoService)
  {
  }

  public function index(ContratoAnexo $anexo)
  {
    $contrato_id = $anexo->contrato_id;

    try {
      Storage::delete($anexo['caminho']);

      $this->anexoService->delete($anexo);

      return to_route('contratos.contratada.dados_gerais.index', ['contrato' => $contrato_id])->with('message', ['type' => 'success', 'content' => 'Registro atualizado!']);
    } catch (\Exception $e) {
      return to_route('contratos.contratada.dados_gerais.index', ['contrato' => $contrato_id])->with('message', ['type' => 'error', 'content' => $e->getMessage()]);
    }
  }
}
