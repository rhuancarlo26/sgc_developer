<?php

namespace App\Domain\Sgc\Contratada\Produtos\Controller;

use App\Shared\Http\Controllers\Controller;
use App\Models\Contrato;
use App\Models\SgcvwEmpreendimentos;
use App\Models\SgcFaunaCampanha;
use App\Models\SgcModulo;
use App\Domain\Sgc\Contratada\Produtos\Services\ProdutosService;
use App\Domain\Sgc\Contratada\Produtos\Fauna\Services\FaunaService;
use App\Domain\Sgc\Contratada\Produtos\Fauna\Services\SgcFaunaEntregaSimplificadaService;
use App\Domain\Sgc\Contratada\Produtos\Fauna\Requests\StoreEntregaSimplificadaFaunaRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Response;

class ProdutosController extends Controller
{
    public function __construct(
        protected ProdutosService $produtosService,
        protected FaunaService $faunaService,
    ) {}

    public function index(Request $request, $contrato, $produto): Response|RedirectResponse
    {
        if ($produto !== 'fauna') {
            return to_route('sgc.contratada.produtos.index', [$contrato, 'fauna']);
        }

        $contratoObj = Contrato::findOrFail($contrato);
        $campanhas = $this->getCampanhasFauna($contrato, $request->boolean('arquivadas'));

        return inertia('Sgc/Contratada/Produtos/ListagemProdutos', [
            'subprodutos' => $this->produtosService->getSubprodutosByContrato($contrato, 'fauna'),
            'contrato' => $contrato,
            'produto' => 'Fauna',
            'contratos' => $contratoObj,
            'campanhas' => $campanhas,
            'pendenciasContrato' => $this->getCampanhasFauna($contrato)
                ->map(fn($campanha) => $this->normalizarPendenciaContrato($campanha, 'fauna', $contrato))
                ->filter()->sortBy('prioridade')->values(),
            'mostrarArquivadas' => $request->boolean('arquivadas'),
            'totalArquivadas' => SgcFaunaCampanha::where('id_contrato', $contrato)->whereNotNull('arquivada_em')->count(),
            'canApprove' => (int) (Auth::user()?->perfis_id ?? 0) === 3,
            'canDeleteAnyCampaign' => Auth::user()?->hasRole('Super Admin') ?? false,
            'canConfigure' => (int) (Auth::user()?->perfis_id ?? 0) === 3 || (Auth::user()?->hasRole('Super Admin') ?? false),
        ]);
    }

    public function create(Request $request, $contrato, $produto): Response|RedirectResponse
    {
        if ($produto !== 'fauna') {
            return to_route('sgc.contratada.produtos.index', [$contrato, 'fauna']);
        }
        $subproduto = $request->query('subproduto');
        abort_unless(is_string($subproduto) && $subproduto !== '', 422);
        return $this->createFauna($request, $contrato, $produto, Contrato::findOrFail($contrato), $subproduto);
    }

    public function store(StoreEntregaSimplificadaFaunaRequest $request, $contrato, $produto)
    {
        abort_unless($produto === 'fauna', 404);
        Contrato::findOrFail($contrato);
        $validated = $request->validated();
        $validated['contrato_id'] = $contrato;
        (new SgcFaunaEntregaSimplificadaService())->criar($validated);
        return to_route('sgc.contratada.produtos.index', [$contrato, 'fauna'])
            ->with('success', 'Campanha simplificada de fauna salva com sucesso.');
    }

    private function normalizarPendenciaContrato(array $campanha, string $produto, $contrato): ?array
    {
        $status = $campanha['status'] ?? $campanha['status_aprovacao'] ?? '';
        $isFiscal = (int) (Auth::user()?->perfis_id ?? 0) === 3;

        if ($isFiscal ? $status !== 'Em análise' : !in_array($status, ['Em elaboração', 'Reprovada', 'Rejeitada'], true)) {
            return null;
        }

        $acao = $this->acaoPendenciaContrato($campanha, $produto, $contrato, $isFiscal);
        if (!$acao) {
            return null;
        }

        return [
            'id' => $campanha['id'],
            'produto' => $produto,
            'produto_nome' => 'Fauna',
            'subproduto' => $campanha['subproduto'] ?? null,
            'titulo' => $campanha['id_campanha'] ?? "Campanha {$campanha['id']}",
            'empreendimento' => $campanha['empreendimento'] ?? null,
            'status' => $status,
            'status_exibicao' => in_array($status, ['Reprovada', 'Rejeitada'], true) ? 'Reprovada' : $status,
            'acao' => $acao['label'],
            'url_acao' => $acao['url'],
            'tipo_acao' => $acao['type'],
            'prioridade' => $status === 'Em análise' ? 1 : ($status === 'Rejeitada' || $status === 'Reprovada' ? 2 : 3),
        ];
    }

    private function acaoPendenciaContrato(array $campanha, string $produto, $contrato, bool $isFiscal): ?array
    {
        $simplificada = ($campanha['modo_preenchimento'] ?? null) === 'simplificado';
        $params = [$contrato, 'fauna', $campanha['id']];
        if ($isFiscal) {
            return ['label' => 'Analisar', 'type' => 'success', 'url' => route($simplificada
                ? 'sgc.contratada.produtos.fauna.simplificada.analise'
                : 'sgc.contratada.produtos.analise', $params)];
        }
        if (in_array($campanha['status'] ?? '', ['Reprovada', 'Rejeitada'], true) || $simplificada) {
            return ['label' => 'Editar', 'type' => 'warning', 'url' => route($simplificada
                ? 'sgc.contratada.produtos.fauna.simplificada.edit'
                : 'sgc.contratada.produtos.edit', $params)];
        }
        return ['label' => 'Continuar', 'type' => 'warning', 'url' => route('sgc.contratada.produtos.create', [$contrato, 'fauna'])
            . '?' . http_build_query(['subproduto' => $campanha['subproduto'], 'id' => $campanha['id'], 'modo' => 'completo'])];
    }

    private function createFauna(Request $request, $contrato, $produto, $contratoObj, $subproduto): Response
    {
        $modo = $request->query('modo');

        if (!$modo) {
            return inertia('Sgc/Contratada/Produtos/Fauna/SelecionarModalidade', [
                'contrato' => $contrato,
                'produto' => ucfirst($produto),
                'contratos' => $contratoObj,
                'subproduto' => $subproduto,
            ]);
        }

        if ($modo === 'simplificado') {
            return $this->createFaunaSimplificada($contrato, $produto, $contratoObj, $subproduto);
        }

        abort_unless($modo === 'completo', 404);

        // Busca rascunho aberto para este contrato/subproduto
        $draft = SgcFaunaCampanha::where('id_contrato', $contrato)
            ->where('subproduto', $subproduto)
            ->where('status', 'Em elaboração')
            ->where('modo_preenchimento', 'completo')
            ->with([
                'abios.abio',
                'profissionais.profissional',
                'modulos_amostrais',
                'pontos_quelo_crocod',
                'pontos_cavernicola',
                'metodologias',
                'resultadosTerrestre',
                'resultadosAquatica',
                'resultadosCavernicola',
                'resultados_consideracoes',
                'anexos',
            ])
            ->latest()
            ->first();

        // Se não existe rascunho, cria um novo com o mínimo necessário.
        // O frontend vai chamar rascunho/inicializar para persistir cod_emp + subproduto,
        // mas já deixamos o registro criado aqui para ter o campanhaId imediatamente.
        if (!$draft) {
            $draft = SgcFaunaCampanha::create([
                'id_contrato'    => $contrato,
                'subproduto'     => $subproduto,
                'status'         => 'Em elaboração',
                'etapa_atual'    => 'apresentacao',
                'modo_preenchimento' => 'completo',
                'versao_analise' => 1,
            ]);

            Log::info('ProdutosController: rascunho fauna criado', [
                'draft_id' => $draft->id,
                'contrato' => $contrato,
            ]);
        }

        $empreendimentos = SgcvwEmpreendimentos::where('contrato_id', $contrato)
            ->pluck('cod_emp')
            ->toArray();

        $abios         = $this->produtosService->getAbios();
        $profissionais = $this->faunaService->getProfissionaisByContrato($contrato);

        // Se o draft já tem dados preenchidos, passa para o frontend pré-carregar
        $draftData = $draft->wasRecentlyCreated
            ? []
            : \App\Domain\Sgc\Contratada\Produtos\Fauna\Resources\CampanhaResource::toArray($draft);

        return inertia('Sgc/Contratada/Produtos/Fauna/Create', [
            'contrato'        => $contrato,
            'produto'         => ucfirst($produto),
            'contratos'       => $contratoObj,
            'subproduto'      => $subproduto,
            'empreendimentos' => $empreendimentos,
            'abios'           => $abios,
            'profissionais'   => $profissionais,
            'campanhaId'      => $draft->id,
            'etapaAtual'      => $draft->etapa_atual ?? 'apresentacao',
            'draftData'       => $draftData,
            'ufs'             => \App\Domain\Sgc\Contratada\Produtos\Fauna\Controller\CampanhaController::getUfs(),
            'biomas'          => \App\Domain\Sgc\Contratada\Produtos\Fauna\Controller\CampanhaController::getBiomas(),
        ]);
    }

    private function createFaunaSimplificada($contrato, $produto, $contratoObj, $subproduto): Response
    {
        return inertia('Sgc/Contratada/Produtos/Fauna/Simplificada/CreateSimplificada', [
            'contrato' => $contrato,
            'produto' => ucfirst($produto),
            'contratos' => $contratoObj,
            'subproduto' => $subproduto,
            'empreendimentos' => SgcvwEmpreendimentos::where('contrato_id', $contrato)->pluck('cod_emp')->toArray(),
            'modulos' => SgcModulo::query()->select(['id', 'nome', 'nome_planilha_modelo'])->get(),
        ]);
    }

    private function getCampanhasFauna($contrato, bool $mostrarArquivadas = false)
    {
        return SgcFaunaCampanha::where('id_contrato', $contrato)
            ->when(
                $mostrarArquivadas,
                fn($query) => $query->whereNotNull('arquivada_em'),
                fn($query) => $query->whereNull('arquivada_em')
            )
            ->get(['id', 'id_campanha', 'cod_emp', 'data_ini', 'data_fim', 'status', 'subproduto', 'modo_preenchimento'])
            ->map(fn($campanha) => [
                'id'           => $campanha->id,
                'id_campanha'  => $campanha->id_campanha ?? 'N/A',
                'empreendimento' => $campanha->cod_emp ?? 'N/A',
                'data_inicial' => $campanha->data_ini ?? 'N/A',
                'data_final'   => $campanha->data_fim ?? 'N/A',
                'status'       => $campanha->status ?? 'Em análise',
                'subproduto'   => $campanha->subproduto ?? 'N/A',
                'modo_preenchimento' => $campanha->modo_preenchimento ?? 'completo',
            ]);
    }
}
