<?php

namespace App\Domain\Sgc\Contratada\RelatorioCoord\Controller;

use App\Domain\Sgc\Contratada\RelatorioCoord\Services\RelatorioService;
use App\Models\Contrato;
use App\Models\SgcComentario;
use App\Models\SgcRelatorioCoordenacao;
use App\Models\SgcRelatorioUpload;
use App\Models\SgcHistoricoRelatorio;
use App\Shared\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;


class RelatorioCoordenacaoController extends Controller
{
  public function __construct(private readonly RelatorioService $relatorioService)
  {
  }
  
    public function index(Contrato $contrato, $relatorioNum): Response
    {
        $comentarios = SgcComentario::with('comment')->get();
        $dadosrelat = SgcRelatorioCoordenacao::where('contrato_id', $contrato->id)
            ->where('relatorio_num', $relatorioNum)
            ->get();
            
        $update_anexo = SgcRelatorioUpload::where('contrato_id', $contrato->id)
            ->where('num_relatorio', $relatorioNum)
            ->get()->keyBy('item_id'); 
    
        return Inertia::render('Sgc/Contratada/Relatorio/Index', [
            'contrato' => $contrato,
            'dadosrelat' => $dadosrelat,
            'update_anexo' => $update_anexo,
            'comentarios' => $comentarios
        ]);
    }

    public function relatorios(Contrato $contrato): Response
    {
        $dadosrelat = SgcRelatorioCoordenacao::where('contrato_id', $contrato->id)
            ->with(['historicos' => function($query) {
                $query->select('relatorio_num', 'versao');
            }])
            ->get();

        return Inertia::render('Sgc/Contratada/Relatorio/Relatorios', [
            'contrato' => $contrato,
            'dadosrelat' => $dadosrelat
        ]);
    }

    public function showHistorico($contrato_id, $relatorio_num, $versao): Response
    {
        $historico = SgcHistoricoRelatorio::where('relatorio_num', $relatorio_num)
            ->where('versao', $versao)
            ->get(); // Buscar todos os itens relacionados à versão

        $contrato = Contrato::findOrFail($contrato_id);

        return Inertia::render('Sgc/Contratada/Relatorio/Historico', [
            'historico' => $historico,
            'contrato' => $contrato,
        ]);
    }

    public function toggleAprovado(Request $request, $id)
    {
        $item = SgcRelatorioCoordenacao::where('id_item', $id)
            ->where('contrato_id', $request->input('contrato_id'))
            ->where('relatorio_num', $request->input('relatorio_num'))
            ->first();

        if ($item) {
            $item->aprovado = $request->input('aprovado');
            $item->save();

            return response()->json(['success' => true, 'aprovado' => $item->aprovado]);
        }

        return response()->json(['success' => false, 'message' => 'Item não encontrado'], 404);
    }


}
