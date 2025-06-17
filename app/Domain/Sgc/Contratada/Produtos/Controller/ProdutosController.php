<?php

namespace App\Domain\Sgc\Contratada\Produtos\Controller;

use App\Shared\Http\Controllers\Controller;
use App\Domain\Sgc\Contratada\Produtos\Services\ProdutosService;
use Inertia\Inertia;
use App\Models\Contrato;
use App\Models\SgcvwSubprodutos;
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
}
