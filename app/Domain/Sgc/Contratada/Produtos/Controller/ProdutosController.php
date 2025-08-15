<?php

namespace App\Domain\Sgc\Contratada\Produtos\Controller;

use App\Shared\Http\Controllers\Controller;
use App\Models\Contrato;
use App\Models\SgcvwEmpreendimentos;
use App\Domain\Sgc\Contratada\Produtos\Services\ProdutosService;
use App\Domain\Sgc\Contratada\Produtos\Fauna\Services\FaunaService;
use App\Models\SgcFaunaCampanha;
use Illuminate\Http\Request;
use Inertia\Response;
use Illuminate\Support\Facades\Auth;

class ProdutosController extends Controller
{
    protected $produtosService;
    protected $faunaService;

    public function __construct(ProdutosService $produtosService, FaunaService $faunaService)
    {
        $this->produtosService = $produtosService;
        $this->faunaService = $faunaService;
    }

    public function index(Request $request, $contrato, $produto): Response
    {
        $subprodutos = $this->produtosService->getSubprodutosByContrato($contrato, $produto);
        $contratoObj = Contrato::findOrFail($contrato);

        // Buscar todas as campanhas para o contrato, sem filtro por subproduto
        $campanhas = SgcFaunaCampanha::where('id_contrato', $contrato)
            ->get(['id', 'id_campanha', 'cod_emp', 'data_ini', 'data_fim', 'status', 'subproduto'])
            ->map(function ($campanha) {
                return [
                    'id' => $campanha->id,
                    'id_campanha' => $campanha->id_campanha ?? 'N/A',
                    'empreendimento' => $campanha->cod_emp ?? 'N/A',
                    'data_inicial' => $campanha->data_ini ?? 'N/A',
                    'data_final' => $campanha->data_fim ?? 'N/A',
                    'status' => $campanha->status ?? 'Em análise',
                    'subproduto' => $campanha->subproduto ?? 'N/A',
                ];
            });

        return inertia('Sgc/Contratada/Produtos/Fauna/Fauna', [
            'subprodutos' => $subprodutos,
            'contrato' => $contrato,
            'produto' => ucfirst($produto),
            'contratos' => $contratoObj,
            'campanhas' => $campanhas,
            'canApprove' => Auth::user()->perfis_id === 2 && count(array_filter($campanhas->toArray(), fn($c) => $c['status'] === 'Em análise')) > 0,
        ]);
    }

    public function create(Request $request, $contrato, $produto): Response
    {
        $contratoObj = Contrato::findOrFail($contrato);
        $subproduto = $request->query('subproduto');

        $empreendimentos = SgcvwEmpreendimentos::where('contrato_id', $contrato)
            ->pluck('cod_emp')
            ->toArray();

        $abios = $this->produtosService->getAbios();
        $profissionais = $this->faunaService->getProfissionaisByContrato($contrato);

        return inertia('Sgc/Contratada/Produtos/Fauna/Create', [
            'contrato' => $contrato,
            'produto' => ucfirst($produto),
            'contratos' => $contratoObj,
            'subproduto' => $subproduto,
            'empreendimentos' => $empreendimentos,
            'abios' => $abios,
            'profissionais' => $profissionais,
        ]);
    }
}