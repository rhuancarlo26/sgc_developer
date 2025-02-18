<?php

namespace App\Domain\Sgc\Contratada\Dav\Controller;

use App\Models\Contrato;
use App\Models\DashexcelEmpreendimentos;
use App\Models\Dav;
use App\Models\SgcDavProfissionais;
use App\Models\SgcProdutos;
use App\Models\SgcvwSubprodutos;
use App\Shared\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\HistoricoDav;

class ListagemDavController extends Controller
{
    public function index(Contrato $contrato): Response
    {
        $dav = Dav::where('contrato_id', $contrato->id)->get();
        $subprodutos = SgcvwSubprodutos::all();
        $produtos = SgcProdutos::all();
        $empreendimentos = DashexcelEmpreendimentos::all();
        $profissionais = SgcDavProfissionais::where('contrato_id', $contrato->id)->get();

        return Inertia::render('Sgc/Contratada/Dav/Index', [
            'dav' => $dav,
            'contrato' => $contrato,
            'subprodutos' => $subprodutos,
            'produtos' => $produtos,
            'empreendimentos' => $empreendimentos,
            'profissionais' => $profissionais,
        ]);
    }

    public function details(Contrato $contrato, $id): Response
    {
        $dav = Dav::findOrFail($id);
        $subprodutos = SgcvwSubprodutos::all();
        $produtos = SgcProdutos::all();
        $empreendimentos = DashexcelEmpreendimentos::all();

        return Inertia::render('Sgc/Contratada/Dav/DetalheDav', [
            'dav' => $dav,
            'contrato' => $contrato,
            'subprodutos' => $subprodutos,
            'produtos' => $produtos,
            'empreendimentos' => $empreendimentos
        ]);
    }

    public function aprovar($id)
    {
        $dav = Dav::findOrFail($id);
        $dav->status = 'aprovado';
        $dav->save();

        return redirect()->back()->with('success', 'DAV aprovada com sucesso.');
    }

    public function reprovar($id)
    {
        $dav = Dav::findOrFail($id);
        $status_anterior = $dav->status;

        $dav->status = 'reprovado';
        $dav->save();

        // Definição do Histórico AQUI
        HistoricoDav::create([
            'dav_id' => $dav->id,
            'status_anterior' => $status_anterior,
            'status_novo' => 'reprovado',
            'observacao' => 'DAV reprovada pelo usuário.',
            'user_id' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'DAV reprovada com sucesso.');
    }


}
