<?php

namespace App\Domain\Sgc\Contratada\Cronograma\Controller;

use App\Shared\Http\Controllers\Controller;
use App\Models\Contrato;
use App\Domain\Sgc\Contratada\Cronograma\Services\CronogramaService;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;

class CronogController extends Controller
{
    protected $cronogramaService;

    public function __construct(CronogramaService $cronogramaService)
    {
        $this->cronogramaService = $cronogramaService;
    }

    public function index(Contrato $contrato): Response
    {
        $eventos = $this->cronogramaService->obterCronograma($contrato);
        return Inertia::render('Sgc/Contratada/Cronograma/Cronograma', [
            'eventos' => $eventos,
            'contrato' => $contrato->id,
        ]);
    }

    public function getOpcoesEvento(Contrato $contrato)
    {
        $opcoes = $this->cronogramaService->obterOpcoesEvento($contrato);
        return response()->json($opcoes);
    }

    public function storeEventoAuxiliar(Request $request, Contrato $contrato)
    {
        $data = $request->validate([
            'cod_emp' => 'required|string',
            'subproduto' => 'required|string',
            'data_de_inicio_previsto' => 'required|date',
            'data_de_entrega_previsto' => 'required|date|after_or_equal:data_de_inicio_previsto',
        ]);

        $data['contrato_id'] = $contrato->id;
        $data['contrato'] = $contrato->id; 
        $data['item_edital'] = ''; 

        $this->cronogramaService->salvarEventoAuxiliar($data);

        return redirect()->route('sgc.contratada.cronograma.index', $contrato->id)
            ->with('success', 'Evento auxiliar criado com sucesso!');
    }

}