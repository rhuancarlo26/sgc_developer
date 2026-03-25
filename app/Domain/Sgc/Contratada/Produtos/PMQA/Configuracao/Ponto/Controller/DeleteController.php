<?php

namespace App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\Ponto\Controller;

use App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\Ponto\Services\PontoService; // <-- service SGC
use App\Models\Contrato;
use App\Models\SgcPmqa;
use App\Models\SgcPmqaPonto;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class DeleteController extends Controller
{
    public function __construct(private readonly PontoService $pontoService) {}

    public function index(Contrato $contrato, $produto, $pmqa, $ponto): RedirectResponse
    {
        $pmqaModel = SgcPmqa::findOrFail($pmqa);
        $pontoModel = SgcPmqaPonto::findOrFail($ponto);
        $response = $this->pontoService->deletePonto($pontoModel);

        return redirect()->back()->with('message', $response['request'] ?? $response);
    }
}
