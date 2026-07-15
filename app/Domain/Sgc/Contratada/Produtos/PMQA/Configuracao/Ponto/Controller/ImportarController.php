<?php

namespace App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\Ponto\Controller;

use App\Domain\Servico\PMQA\Configuracao\Ponto\Requests\ImportarRequest;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\Ponto\Services\PontoService;
use App\Models\Contrato;
use App\Models\SgcPmqa;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class ImportarController extends Controller
{
    public function __construct(private readonly PontoService $pontoService) {}

    public function index(
        Contrato $contrato,
        string $produto,   // {produto}
        int $pmqa,         // {pmqa} (id)
        ImportarRequest $request
    ): RedirectResponse {

        $pmqaModel = SgcPmqa::where('id', $pmqa)
            ->where('id_contrato', $contrato->id) // opcional mas recomendado
            ->firstOrFail();

        $file = $request->file('arquivo');
        $response = $this->pontoService->importarParaCampanha($pmqaModel, $file);

        return redirect()->route('contratos.contratada.sgc.pmqa.configuracao.ponto.index', [
            'contrato' => $contrato->id,
            'produto'  => $produto,
            'pmqa'     => $pmqaModel->id,
        ])->with('message', $response['content'] ?? 'Importação concluída com sucesso!');
    }
}
