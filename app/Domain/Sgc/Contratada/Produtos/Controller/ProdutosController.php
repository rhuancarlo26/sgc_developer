<?php

namespace App\Domain\Sgc\Contratada\Produtos\Controller;

use App\Shared\Http\Controllers\Controller;
use App\Domain\Sgc\Contratada\Produtos\Services\ProdutosService;
use Inertia\Inertia;
use App\Models\Contrato;
use App\Models\SgcvwSubprodutos;
use App\Models\SgcvwEmpreendimentos;
use Inertia\Response;
use Illuminate\Http\Request;


class ProdutosController extends Controller
{
    protected $produtosService;

    public function __construct(ProdutosService $produtosService)
    {
        $this->produtosService = $produtosService;
    }

    public function index(Request $request, $contrato, $produto)
    {
        $contratoObj = Contrato::find($contrato);

        $subprodutos = $this->produtosService->getSubprodutosByContrato($contrato, $produto);
        return inertia('Sgc/Contratada/Produtos/Fauna', [
            'subprodutos' => $subprodutos,
            'contrato' => $contrato,
            'produto' => ucfirst($produto),
            'contratos' => $contratoObj
        ]);
    }

    // public function create(Request $request, $contrato, $produto): Response
    // {
    //     $contratoObj = Contrato::findOrFail($contrato);
    //     $subproduto = $request->query('subproduto');
        
    //     $empreendimentos = SgcvwEmpreendimentos::where('contrato_id', $contrato)
    //         ->pluck('cod_emp')
    //         ->toArray();
        
    //     return inertia('Sgc/Contratada/Produtos/Create', [
    //         'contrato' => $contrato,
    //         'produto' => ucfirst($produto),
    //         'contratos' => $contratoObj,
    //         'subproduto' => $subproduto,
    //         'empreendimentos' => $empreendimentos,
    //     ]);
    // }

    public function create(Request $request, $contrato, $produto): Response
    {
        $contratoObj = Contrato::findOrFail($contrato);
        $subproduto = $request->query('subproduto');
        
        $empreendimentos = SgcvwEmpreendimentos::where('contrato_id', $contrato)
            ->pluck('cod_emp')
            ->toArray();
        
        $abios = $this->produtosService->getAbios();

        return inertia('Sgc/Contratada/Produtos/Create', [
            'contrato' => $contrato,
            'produto' => ucfirst($produto),
            'contratos' => $contratoObj,
            'subproduto' => $subproduto,
            'empreendimentos' => $empreendimentos,
            'abios' => $abios,
        ]);
    }


    public function store(Request $request, $contrato, $produto)
    {
        $validated = $request->validate([
            'cod_emp' => 'required|string|max:255',
            'descricao_revisada' => 'required|string|max:255',
            'familia' => 'required|string|max:255',
            'vincular_abio' => 'nullable|string|max:255',
        ]);

        $this->produtosService->createSubproduto($contrato, array_merge($validated, [
            'familia' => $produto === 'fauna' ? 'Fauna' : $validated['familia'],
        ]));

        return redirect()->route('sgc.contratada.produtos.index', [$contrato, $produto])
            ->with('success', 'Subproduto cadastrado com sucesso!');
    }
}
