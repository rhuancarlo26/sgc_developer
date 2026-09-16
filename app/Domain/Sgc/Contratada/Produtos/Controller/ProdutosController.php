<?php

namespace App\Domain\Sgc\Contratada\Produtos\Controller;

use App\Shared\Http\Controllers\Controller;
use App\Models\Contrato;
use App\Models\SgcvwEmpreendimentos;
use App\Domain\Sgc\Contratada\Produtos\Services\ProdutosService;
use App\Domain\Sgc\Contratada\Produtos\Fauna\Services\FaunaService;
use App\Domain\Sgc\Contratada\Produtos\Espeleologia\Services\EspeleoService;
use App\Models\SgcPmqa;
use App\Models\SgcPmqaParametroLista;
use App\Models\SgcFaunaCampanha;
use App\Models\SgcEspeleoCampanha;
use App\Models\SgcEspeleoProfissional;
use App\Models\SgcPmqaPonto;
use App\Models\SgcvwSubprodutos;
use App\Models\SgcEspeleoEstudosPosteriores;
use App\Models\SgcModulo;
use App\Models\SgcMalarigeno;
use App\Models\SgcRima;
use App\Models\SgcAsvCampanha;
use App\Models\SgcIndigenaCampanha;
use App\Models\SgcQuilombolaCampanha;
use App\Models\SgcAudienciaCampanha;
use App\Models\SgcPbaCampanha;
use App\Models\SgcPmqaExecCampanha;
use App\Domain\Sgc\Contratada\Produtos\Malarigeno\Requests\StoreMalarigenoRequest;
use App\Domain\Sgc\Contratada\Produtos\Malarigeno\Services\MalarigenoService;
use App\Domain\Sgc\Contratada\Produtos\Rima\Requests\StoreRimaRequest;
use App\Domain\Sgc\Contratada\Produtos\Rima\Services\RimaService;
use App\Domain\Sgc\Contratada\Produtos\Asv\Requests\StoreAsvSimplificadaRequest;
use App\Domain\Sgc\Contratada\Produtos\Asv\Services\AsvEntregaSimplificadaService;
use App\Domain\Sgc\Contratada\Produtos\Indigena\Requests\StoreIndigenaSimplificadaRequest;
use App\Domain\Sgc\Contratada\Produtos\Indigena\Services\IndigenaEntregaSimplificadaService;
use App\Domain\Sgc\Contratada\Produtos\Quilombola\Requests\StoreQuilombolaSimplificadaRequest;
use App\Domain\Sgc\Contratada\Produtos\Quilombola\Services\QuilombolaEntregaSimplificadaService;
use App\Domain\Sgc\Contratada\Produtos\Audiencia\Requests\StoreAudienciaSimplificadaRequest;
use App\Domain\Sgc\Contratada\Produtos\Audiencia\Services\AudienciaEntregaSimplificadaService;
use App\Domain\Sgc\Contratada\Produtos\Pba\Requests\StorePbaSimplificadaRequest;
use App\Domain\Sgc\Contratada\Produtos\Pba\Services\PbaEntregaSimplificadaService;
use App\Domain\Sgc\Contratada\Produtos\Fauna\Requests\StoreEntregaSimplificadaFaunaRequest;
use App\Domain\Sgc\Contratada\Produtos\Fauna\Services\SgcFaunaEntregaSimplificadaService;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ProdutosController extends Controller
{
    protected $produtosService;
    protected $faunaService;
    protected $espeleoService;

    public function __construct(
        ProdutosService $produtosService,
        FaunaService $faunaService,
        EspeleoService $espeleoService
    ) {
        $this->produtosService = $produtosService;
        $this->faunaService = $faunaService;
        $this->espeleoService = $espeleoService;
    }

    public function index(Request $request, $contrato, $produto): Response
    {
        $subprodutos = $this->produtosService->getSubprodutosByContrato($contrato, $produto);
        $contratoObj = Contrato::findOrFail($contrato);
        $mostrarArquivadas = $request->boolean('arquivadas');

        $campanhas = match ($produto) {
            'fauna'        => $this->getCampanhasFauna($contrato, $mostrarArquivadas),
            'pmqa', 'eia'  => $this->getCampanhasPmqa($contrato),
            'espeleologia' => $this->getCampanhasEspeleologia($contrato),
            'malarigeno'   => $this->getCampanhasMalarigeno($contrato),
            'rima'         => $this->getCampanhasRima($contrato),
            'asv'          => $this->getCampanhasAsv($contrato),
            'indigena'     => $this->getCampanhasIndigena($contrato),
            'quilombola'   => $this->getCampanhasQuilombola($contrato),
            'audiencia'    => $this->getCampanhasAudiencia($contrato),
            'pba'          => $this->getCampanhasPba($contrato),
             default        => collect(),
        };

        $totalArquivadas = $produto === 'fauna'
            ? SgcFaunaCampanha::where('id_contrato', $contrato)
                ->whereNotNull('arquivada_em')
                ->count()
            : 0;

        return inertia('Sgc/Contratada/Produtos/ListagemProdutos', [
            'subprodutos' => $subprodutos,
            'contrato' => $contrato,
            'produto' => $produto === 'pba' ? 'PBA' : ucfirst($produto),
            'contratos' => $contratoObj,
            'campanhas' => $campanhas,
            // O painel superior não depende do produto aberto. Mantemos a lista
            // de campanhas acima para a tabela atual e enviamos, separadamente,
            // apenas as pendências acionáveis de todo o contrato.
            'pendenciasContrato' => $this->getPendenciasContrato($contrato),
            'mostrarArquivadas' => $mostrarArquivadas,
            'totalArquivadas' => $totalArquivadas,
            'canApprove' => $this->usuarioPodeAprovarPmqa()
                && count(array_filter($campanhas->toArray(), fn($c) => $c['status'] === 'Em análise')) > 0,
        ]);
    }

    private function getPendenciasContrato($contrato)
    {
        $campanhasPorProduto = [
            'fauna' => $this->getCampanhasFauna($contrato),
            'espeleologia' => $this->getCampanhasEspeleologia($contrato),
            'malarigeno' => $this->getCampanhasMalarigeno($contrato),
            'rima' => $this->getCampanhasRima($contrato),
            'asv' => $this->getCampanhasAsv($contrato),
            'indigena' => $this->getCampanhasIndigena($contrato),
            'quilombola' => $this->getCampanhasQuilombola($contrato),
            'audiencia' => $this->getCampanhasAudiencia($contrato),
            'pba' => $this->getCampanhasPba($contrato),
        ];

        return collect($campanhasPorProduto)
            ->flatMap(fn ($campanhas, $produto) => $campanhas->map(
                fn ($campanha) => $this->normalizarPendenciaContrato($campanha, $produto, $contrato)
            ))
            ->filter()
            ->sortBy([
                ['prioridade', 'asc'],
                ['produto_nome', 'asc'],
                ['titulo', 'asc'],
            ])
            ->values();
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
            'produto_nome' => match ($produto) {
                'malarigeno' => 'Malarígeno',
                'rima' => 'RIMA',
                'asv' => 'ASV',
                'indigena' => 'Indígena',
                'quilombola' => 'Quilombola',
                'audiencia' => 'Audiência',
                'pba' => 'PBA',
                default => ucfirst($produto),
            },
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
        $id = $campanha['id'];
        $simplificada = $produto === 'fauna' && ($campanha['modo_preenchimento'] ?? null) === 'simplificado';

        if ($isFiscal) {
            $rota = match ($produto) {
                'fauna' => $simplificada
                    ? 'sgc.contratada.produtos.fauna.simplificada.analise'
                    : 'sgc.contratada.produtos.analise',
                'espeleologia' => 'sgc.contratada.produtos.espeleo.analise',
                'malarigeno' => 'sgc.contratada.produtos.malarigeno.analise',
                'rima' => 'sgc.contratada.produtos.rima.analise',
                'asv' => 'sgc.contratada.produtos.asv.analise',
                'indigena' => 'sgc.contratada.produtos.indigena.analise',
                'quilombola' => 'sgc.contratada.produtos.quilombola.analise',
                'audiencia' => 'sgc.contratada.produtos.audiencia.analise',
                'pba' => 'sgc.contratada.produtos.pba.analise',
                default => null,
            };

            return $rota ? [
                'label' => 'Analisar',
                'type' => 'success',
                'url' => route($rota, [$contrato, $produto, $id]),
            ] : null;
        }

        if (in_array($campanha['status'] ?? '', ['Reprovada', 'Rejeitada'], true)) {
            $rota = match ($produto) {
                'fauna' => $simplificada
                    ? 'sgc.contratada.produtos.fauna.simplificada.edit'
                    : 'sgc.contratada.produtos.edit',
                'espeleologia' => 'sgc.contratada.produtos.espeleo.show',
                'malarigeno' => 'sgc.contratada.produtos.malarigeno.edit',
                'rima' => 'sgc.contratada.produtos.rima.edit',
                'asv' => 'sgc.contratada.produtos.asv.edit',
                'indigena' => 'sgc.contratada.produtos.indigena.edit',
                'quilombola' => 'sgc.contratada.produtos.quilombola.edit',
                'audiencia' => 'sgc.contratada.produtos.audiencia.edit',
                'pba' => 'sgc.contratada.produtos.pba.edit',
                default => null,
            };

            return $rota ? [
                'label' => 'Editar',
                'type' => 'warning',
                'url' => route($rota, [$contrato, $produto, $id]),
            ] : null;
        }

        if (($campanha['status'] ?? '') === 'Em elaboração') {
            if ($simplificada) {
                return [
                    'label' => 'Continuar',
                    'type' => 'warning',
                    'url' => route('sgc.contratada.produtos.fauna.simplificada.show', [$contrato, $produto, $id]),
                ];
            }

            if (in_array($produto, ['asv', 'indigena', 'quilombola', 'audiencia', 'pba'], true)) {
                return [
                    'label' => 'Continuar',
                    'type' => 'warning',
                    'url' => route("sgc.contratada.produtos.{$produto}.edit", [$contrato, $produto, $id]),
                ];
            }

            return [
                'label' => 'Continuar',
                'type' => 'warning',
                'url' => route('sgc.contratada.produtos.create', [$contrato, $produto]) . '?' . http_build_query([
                    'subproduto' => $campanha['subproduto'] ?? null,
                    'id' => $id,
                ]),
            ];
        }

        return null;
    }

    public function create(Request $request, $contrato, $produto): Response
    {
        Log::info('Acessando create', ['contrato' => $contrato, 'produto' => $produto, 'subproduto' => $request->query('subproduto')]);
        $contratoObj = Contrato::findOrFail($contrato);
        $subproduto = $request->query('subproduto');

        if (!$subproduto && !in_array($produto, ['pmqa', 'eia', 'fauna', 'malarigeno', 'rima', 'asv', 'indigena', 'quilombola', 'audiencia', 'pba'])) {
            Log::warning('Subproduto não selecionado', ['contrato' => $contrato, 'produto' => $produto]);

            if ($produto === 'patrimonio') {
                return inertia('Sgc/Contratada/Produtos/Patrimonio/Create', [
                    'contrato' => $contrato,
                    'produto' => ucfirst($produto),
                    'contratos' => $contratoObj,
                    'error' => 'Subproduto não selecionado. Por favor, selecione um subproduto.',
                    'subproduto' => null,
                    'empreendimentos' => SgcvwEmpreendimentos::where('contrato_id', $contrato)
                        ->get(['id', 'cod_emp', 'coordenadas', 'subtrecho_ini', 'subtrecho_fin', 'km_ini', 'km_fin', 'tipo_de_intervencao', 'descricao', 'bioma'])
                        ->toArray(),
                    'paipa' => null,
                    'paipaId' => null,
                    'profissionais' => [],
                ]);
            }

            return inertia('Sgc/Contratada/Produtos/Espeleologia/Create', [
                'contrato' => $contrato,
                'produto' => ucfirst($produto),
                'contratos' => $contratoObj,
                'error' => 'Subproduto não selecionado. Por favor, selecione um subproduto.',
                'subproduto' => null,
                'empreendimentos' => [],
                'campanhaId' => null,
                'draftData' => [],
                'profissionais' => [],
            ]);
        }

        // Atualizar o método create()
        if ($produto === 'fauna') {
            return $this->createFauna($request, $contrato, $produto, $contratoObj, $subproduto);
        } elseif ($produto === 'pmqa' || $produto === 'eia') { // <-- MUDANÇA AQUI
            // Se o produto for 'pmqa' OU 'eia', chamamos a função de criação do PMQA.
            // Passamos a variável $produto original ('eia') para que a tela mantenha o título correto.
            return $this->createPmqa($request, $contrato, $produto, $contratoObj, $subproduto);

        } elseif ($produto === 'malarigeno') {
            return $this->createMalarigeno($request,$contrato,$produto,$contratoObj,$subproduto);

        } elseif ($produto === 'rima') {
            return $this->createRima($request, $contrato, $produto, $contratoObj, $subproduto);

        } elseif ($produto === 'asv') {
            return $this->createAsv($contrato, $produto, $contratoObj, $subproduto);

        } elseif ($produto === 'indigena') {
            return $this->createIndigena($contrato, $produto, $contratoObj, $subproduto);

        } elseif ($produto === 'quilombola') {
            return $this->createQuilombola($contrato, $produto, $contratoObj, $subproduto);

        } elseif ($produto === 'audiencia') {
            return $this->createAudiencia($contrato, $produto, $contratoObj, $subproduto);

        } elseif ($produto === 'pba') {
            return $this->createPba($contrato, $produto, $contratoObj, $subproduto);

        } elseif ($produto === 'patrimonio') {
            return $this->createPatrimonio($request, $contrato, $produto, $contratoObj, $subproduto);

        } else {
            return $this->createEspeleologia($request, $contrato, $produto, $contratoObj, $subproduto);
        }

        // Atualizar o método index()
        $campanhas = match ($produto) {
            'fauna' => $this->getCampanhasFauna($contrato),
            'pmqa' => $this->getCampanhasPmqa($contrato),
            default => $this->getCampanhasEspeleologia($contrato)
        };
    }

    private function createPatrimonio(
        Request $request,
        $contrato,
        $produto,
        $contratoObj,
        $subproduto
    ): Response {
        $empreendimentos = SgcvwEmpreendimentos::where('contrato_id', $contrato)
            ->get(['id', 'cod_emp', 'br', 'uf', 'coordenadas', 'subtrecho_ini', 'subtrecho_fin', 'subtrecho_ini2', 'subtrecho_fin3', 'subtrecho_ini3', 'subtrecho_fin32', 'km_ini', 'km_fin', 'km_ini2', 'km_fin2', 'km_ini3', 'km_fin3', 'extensao', 'tipo_de_intervencao', 'descricao', 'bioma'])
            ->toArray();

        return inertia('Sgc/Contratada/Produtos/Patrimonio/Create', [
            'contrato' => $contrato,
            'produto' => ucfirst($produto),
            'contratos' => $contratoObj,
            'subproduto' => $subproduto,
            'empreendimentos' => $empreendimentos,
            'paipa' => null,
            'paipaId' => $request->query('paipa_id'),
            'profissionais' => [],
        ]);
    }

    private function createMalarigeno(
        Request $request,
        $contrato,
        $produto,
        $contratoObj,
        $subproduto
    ): Response
    {
        $modulos = SgcModulo::query()
            ->select(['id', 'nome', 'nome_planilha_modelo', 'caminho_planilha_modelo', 'campos', 'created_at'])
            ->get();

        $empreendimentos = SgcvwEmpreendimentos::where('contrato_id', $contrato)
            ->pluck('cod_emp')
            ->toArray();

        return inertia('Sgc/Contratada/Produtos/Malarigeno/Create', [
            'contrato' => $contrato,
            'produto' => ucfirst($produto),
            'contratos' => $contratoObj,
            'subproduto' => $subproduto,
            'modulos' => $modulos,
            'empreendimentos' => $empreendimentos,
        ]);
    }

    public function store(Request $request, $contrato, $produto)
    {
        if ($produto === 'fauna') {
            $validated = $request->validate(
                (new StoreEntregaSimplificadaFaunaRequest())->rules(),
                (new StoreEntregaSimplificadaFaunaRequest())->messages()
            );
            $validated['contrato_id'] = $contrato;

            (new SgcFaunaEntregaSimplificadaService())->criar($validated);

            return redirect()
                ->route('sgc.contratada.produtos.index', [$contrato, $produto])
                ->with('success', 'Campanha simplificada de fauna salva com sucesso.');
        }

        if ($produto === 'malarigeno') {
            $validated = $request->validate(
                (new StoreMalarigenoRequest())->rules(),
                (new StoreMalarigenoRequest())->messages()
            );
            $validated['contrato_id'] = $contrato;

            $malarigeno = (new MalarigenoService())->store($validated);

            return redirect()
                ->route('sgc.contratada.produtos.malarigeno.show', [$contrato, $produto, $malarigeno->id])
                ->with('success', 'Malarígeno salvo com sucesso!');
        }

        if ($produto === 'rima') {
            $validated = request()->validate(
                (new StoreRimaRequest())->rules(),
                (new StoreRimaRequest())->messages()
            );
            $validated['contrato_id'] = $contrato;

            $rima = (new RimaService())->store($validated);

            return redirect()
                ->route('sgc.contratada.produtos.rima.show', [$contrato, $produto, $rima->id])
                ->with('success', 'RIMA salvo com sucesso!');
        }

        if ($produto === 'asv') {
            $validated = $request->validate((new StoreAsvSimplificadaRequest())->rules());
            $validated['contrato_id'] = $contrato;
            $asv = (new AsvEntregaSimplificadaService())->criar($validated);

            return redirect()
                ->route('sgc.contratada.produtos.asv.show', [$contrato, 'asv', $asv->id])
                ->with('success', 'Campanha ASV salva com sucesso.');
        }

        if ($produto === 'indigena') {
            $validated = $request->validate((new StoreIndigenaSimplificadaRequest())->rules());
            $validated['contrato_id'] = $contrato;

            $jaExiste = SgcIndigenaCampanha::where('id_contrato', $contrato)
                ->where('id_campanha', $validated['id_campanha'])
                ->where('subproduto', $validated['subproduto'])
                ->exists();

            if ($jaExiste) {
                throw ValidationException::withMessages([
                    'id_campanha' => 'Já existe uma campanha Indígena com este ID para o subproduto selecionado. Abra a campanha existente para visualizá-la ou editá-la.',
                ]);
            }

            $indigena = (new IndigenaEntregaSimplificadaService())->criar($validated);

            return redirect()
                ->route('sgc.contratada.produtos.indigena.show', [$contrato, 'indigena', $indigena->id])
                ->with('success', 'Campanha Indígena salva com sucesso.');
        }

        if ($produto === 'quilombola') {
            $validated = $request->validate((new StoreQuilombolaSimplificadaRequest())->rules());
            $validated['contrato_id'] = $contrato;

            if (SgcQuilombolaCampanha::where('id_contrato', $contrato)->where('id_campanha', $validated['id_campanha'])->where('subproduto', $validated['subproduto'])->exists()) {
                throw ValidationException::withMessages(['id_campanha' => 'Já existe uma campanha Quilombola com este ID para o subproduto selecionado. Abra a campanha existente para visualizá-la ou editá-la.']);
            }

            $quilombola = (new QuilombolaEntregaSimplificadaService())->criar($validated);

            return redirect()
                ->route('sgc.contratada.produtos.quilombola.show', [$contrato, 'quilombola', $quilombola->id])
                ->with('success', 'Campanha Quilombola salva com sucesso.');
        }

        if ($produto === 'audiencia') {
            $validated = $request->validate((new StoreAudienciaSimplificadaRequest())->rules());
            $validated['contrato_id'] = $contrato;
            if (SgcAudienciaCampanha::where('id_contrato', $contrato)->where('id_campanha', $validated['id_campanha'])->where('subproduto', $validated['subproduto'])->exists()) {
                throw ValidationException::withMessages(['id_campanha' => 'Já existe uma campanha de Audiência com este ID para o subproduto selecionado. Abra a campanha existente para visualizá-la ou editá-la.']);
            }
            $audiencia = (new AudienciaEntregaSimplificadaService())->criar($validated);
            return redirect()->route('sgc.contratada.produtos.audiencia.show', [$contrato, 'audiencia', $audiencia->id])->with('success', 'Campanha de Audiência salva com sucesso.');
        }

        if ($produto === 'pba') {
            $validated = $request->validate((new StorePbaSimplificadaRequest())->rules());
            $validated['contrato_id'] = $contrato;
            if (SgcPbaCampanha::where('id_contrato', $contrato)->where('id_campanha', $validated['id_campanha'])->where('subproduto', $validated['subproduto'])->exists()) {
                throw ValidationException::withMessages(['id_campanha' => 'Já existe uma campanha PBA com este ID para o subproduto selecionado. Abra a campanha existente para visualizá-la ou editá-la.']);
            }
            $pba = (new PbaEntregaSimplificadaService())->criar($validated);
            return redirect()->route('sgc.contratada.produtos.pba.show', [$contrato, 'pba', $pba->id])->with('success', 'Campanha PBA salva com sucesso.');
        }

        abort(404);
    }

    private function createAsv($contrato, $produto, $contratoObj, $subproduto): Response
    {
        return inertia('Sgc/Contratada/Produtos/Asv/Create', [
            'contrato' => $contrato,
            'produto' => ucfirst($produto),
            'contratos' => $contratoObj,
            'subproduto' => $subproduto,
            'modulos' => SgcModulo::select(['id', 'nome', 'nome_planilha_modelo'])->get(),
            'empreendimentos' => SgcvwEmpreendimentos::where('contrato_id', $contrato)->pluck('cod_emp')->toArray(),
        ]);
    }

    private function createIndigena($contrato, $produto, $contratoObj, $subproduto): Response
    {
        return inertia('Sgc/Contratada/Produtos/Indigena/Create', [
            'contrato' => $contrato,
            'produto' => 'Indígena',
            'contratos' => $contratoObj,
            'subproduto' => $subproduto,
            'modulos' => SgcModulo::select(['id', 'nome', 'nome_planilha_modelo'])->get(),
            'empreendimentos' => SgcvwEmpreendimentos::where('contrato_id', $contrato)->pluck('cod_emp')->toArray(),
        ]);
    }

    private function createQuilombola($contrato, $produto, $contratoObj, $subproduto): Response
    {
        return inertia('Sgc/Contratada/Produtos/Quilombola/Create', [
            'contrato' => $contrato,
            'produto' => 'Quilombola',
            'contratos' => $contratoObj,
            'subproduto' => $subproduto,
            'modulos' => SgcModulo::select(['id', 'nome', 'nome_planilha_modelo'])->get(),
            'empreendimentos' => SgcvwEmpreendimentos::where('contrato_id', $contrato)->pluck('cod_emp')->toArray(),
        ]);
    }

    private function createAudiencia($contrato, $produto, $contratoObj, $subproduto): Response
    {
        return inertia('Sgc/Contratada/Produtos/Audiencia/Create', [
            'contrato' => $contrato,
            'produto' => 'Audiência',
            'contratos' => $contratoObj,
            'subproduto' => $subproduto,
            'modulos' => SgcModulo::select(['id', 'nome', 'nome_planilha_modelo'])->get(),
            'empreendimentos' => SgcvwEmpreendimentos::where('contrato_id', $contrato)->pluck('cod_emp')->toArray(),
        ]);
    }

    private function createPba($contrato, $produto, $contratoObj, $subproduto): Response
    {
        return inertia('Sgc/Contratada/Produtos/Pba/Create', [
            'contrato' => $contrato,
            'produto' => 'PBA',
            'contratos' => $contratoObj,
            'subproduto' => $subproduto,
            'modulos' => SgcModulo::select(['id', 'nome', 'nome_planilha_modelo'])->get(),
            'empreendimentos' => SgcvwEmpreendimentos::where('contrato_id', $contrato)->pluck('cod_emp')->toArray(),
        ]);
    }

    private function createRima(
        Request $request,
        $contrato,
        $produto,
        $contratoObj,
        $subproduto
    ): Response
    {
        $modulos = SgcModulo::query()
            ->select(['id', 'nome', 'nome_planilha_modelo', 'caminho_planilha_modelo', 'campos', 'created_at'])
            ->get();

        $empreendimentos = SgcvwEmpreendimentos::where('contrato_id', $contrato)
            ->pluck('cod_emp')
            ->toArray();

        return inertia('Sgc/Contratada/Produtos/Rima/Create', [
            'contrato' => $contrato,
            'produto' => ucfirst($produto),
            'contratos' => $contratoObj,
            'subproduto' => $subproduto,
            'modulos' => $modulos,
            'empreendimentos' => $empreendimentos,
        ]);
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

    // --------------------------------------------------------------------------
    // Método 2: getCampanhasFauna
    // Mudança: exclui rascunhos da listagem — o usuário não deve ver rascunhos
    // na tabela principal, só campanhas já submetidas
    // --------------------------------------------------------------------------

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

    private function createEspeleologia(Request $request, $contrato, $produto, $contratoObj, $subproduto): Response
    {
        $draft = $this->espeleoService->obterOuCriarRascunho($contrato, $subproduto);

        $empreendimentos = SgcvwEmpreendimentos::where('contrato_id', $contrato)
            ->select('cod_emp', 'subtrecho_ini', 'subtrecho_fin', 'km_ini', 'km_fin', 'tipo_de_intervencao', 'descricao', 'bioma', 'coordenadas')
            ->get()
            ->map(function ($emp) {
                return [
                    'cod_emp' => $emp->cod_emp,
                    'subtrecho' => $emp->subtrecho_ini && $emp->subtrecho_fin ? $emp->subtrecho_ini . ' - ' . $emp->subtrecho_fin : '',
                    'segmento' => $emp->km_ini && $emp->km_fin ? $emp->km_ini . ' - ' . $emp->km_fin : '',
                    'extensao' => $emp->km_fin && $emp->km_ini ? $emp->km_fin - $emp->km_ini : 0,
                    'tipo_de_intervencao' => $emp->tipo_de_intervencao ?? '',
                    'descricao' => $emp->descricao ?? '',
                    'bioma' => $emp->bioma ?? '',
                    'coordenadas' => $emp->coordenadas
                ];
            });

        $profissionais = SgcEspeleoProfissional::where('id_contrato', $contrato)->get([
            'id',
            'profissional',
            'formacao',
            'telefone',
            'cpf',
            'email',
            'curriculum_lattes',
            'funcao',
            'ctf',
            'validade',
            'conselho_de_classe',
            'numero_de_registro',
            'status',
            'observacao'
        ])->toArray();

        // Carregar justificativas relacionadas, se existirem, ou usar valor padrão
        $justificativas = $draft->justificativas()->get()->map(function ($just) {
            return [
                'justificativa' => $just->justificativa,
                'tipo' => $just->tipo,
                'titulo' => $just->titulo,
                'codigo_sei' => $just->codigo_sei,
            ];
        })->all() ?: [['justificativa' => '', 'tipo' => 'complementar', 'titulo' => '', 'codigo_sei' => '']];

        // Carregar metodologia relacionada
        $metodologia = $draft->metodologia ? $draft->metodologia->metodologia : '';
         $resultadosAnexos = $draft->resultadoAnexos()->get()->map(function ($anexo) {
            return [
                'id' => $anexo->id,
                'nome_arquivo' => $anexo->nome_arquivo,
                'caminho' => $anexo->caminho,
                'url_publica' => Storage::url($anexo->caminho),
            ];
        })->toArray();

         // Carregar estudos posteriores já salvos para essa campanha
        $estudosPosteriores = SgcEspeleoEstudosPosteriores::where('campanha_id', $draft->id)
            ->get(['id', 'subproduto_id', 'quantidade', 'coordenadas', 'necessario'])
            ->toArray();

        // Buscar subprodutos da família Espeleologia
        $subprodutosEspeleologia = SgcvwSubprodutos::where('familia', 'Espeleologia')
            ->where('contrato_id', $contrato)
            ->orderBy('descricao_revisada')
            ->get(['id', 'descricao_revisada'])
            ->map(function ($s) {
                return [
                    'id' => $s->id,
                    'descricao_revisada' => $s->descricao_revisada
                ];
            })
            ->values()
            ->toArray();


        return inertia('Sgc/Contratada/Produtos/Espeleologia/Create', [
            'contrato' => $contrato,
            'produto' => ucfirst($produto),
            'contratos' => $contratoObj,
            'subproduto' => $subproduto,
            'empreendimentos' => $empreendimentos,
            'campanhaId' => $draft->id,
            'draftData' => $draft->toArray(),
            'profissionais' => $profissionais,
            'justificativas' => $justificativas,
            'metodologia' => $metodologia,
            'resultados_anexos' => $resultadosAnexos,
            'subprodutosEspeleologia' => $subprodutosEspeleologia,
            'estudosPosteriores' => $estudosPosteriores,
        ]);

    }

    private function getCampanhasEspeleologia($contrato)
    {
        return SgcEspeleoCampanha::where('id_contrato', $contrato)
            ->get(['id', 'id_campanha', 'cod_emp', 'status', 'subproduto'])
            ->map(function ($campanha) {
                return [
                    'id' => $campanha->id,
                    'id_campanha' => $campanha->id_campanha ?? 'N/A',
                    'empreendimento' => $campanha->cod_emp ?? 'N/A',
                    'data_inicial' => 'N/A',
                    'data_final' => 'N/A',
                    'status' => $campanha->status ?? 'Em análise',
                    'subproduto' => $campanha->subproduto ?? 'N/A',
                ];
            });
    }

    private function getCampanhasMalarigeno($contrato)
    {
        return SgcMalarigeno::where('id_contrato', $contrato)
            ->latest()
            ->get(['id', 'id_campanha', 'cod_emp', 'subproduto', 'status', 'created_at'])
            ->map(fn($campanha) => [
                'id' => $campanha->id,
                'id_campanha' => $campanha->id_campanha ?? 'N/A',
                'empreendimento' => $campanha->cod_emp ?? 'N/A',
                'data_inicial' => $campanha->created_at ? $campanha->created_at->format('d/m/Y') : 'N/A',
                'data_final' => 'N/A',
                'status' => $campanha->status ?? 'Em elaboração',
                'subproduto' => $campanha->subproduto ?? 'N/A',
            ]);
    }

    private function getCampanhasRima($contrato)
    {
        return SgcRima::where('id_contrato', $contrato)
            ->latest()
            ->get(['id', 'id_campanha', 'cod_emp', 'subproduto', 'status', 'created_at'])
            ->map(fn($campanha) => [
                'id' => $campanha->id,
                'id_campanha' => $campanha->id_campanha ?? 'N/A',
                'empreendimento' => $campanha->cod_emp ?? 'N/A',
                'data_inicial' => $campanha->created_at ? $campanha->created_at->format('d/m/Y') : 'N/A',
                'data_final' => 'N/A',
                'status' => $campanha->status ?? 'Em elaboração',
                'subproduto' => $campanha->subproduto ?? 'N/A',
            ]);
    }

    private function getCampanhasAsv($contrato)
    {
        return SgcAsvCampanha::where('id_contrato', $contrato)
            ->latest()
            ->get(['id', 'id_campanha', 'cod_emp', 'subproduto', 'status', 'created_at'])
            ->map(fn ($campanha) => [
                'id' => $campanha->id,
                'id_campanha' => $campanha->id_campanha ?? 'N/A',
                'empreendimento' => $campanha->cod_emp ?? 'N/A',
                'data_inicial' => $campanha->created_at ? $campanha->created_at->format('d/m/Y') : 'N/A',
                'data_final' => 'N/A',
                'status' => $campanha->status ?? 'Em elaboração',
                'subproduto' => $campanha->subproduto ?? 'N/A',
            ]);
    }

    private function getCampanhasIndigena($contrato)
    {
        return SgcIndigenaCampanha::where('id_contrato', $contrato)
            ->latest()
            ->get(['id', 'id_campanha', 'cod_emp', 'subproduto', 'status', 'created_at'])
            ->map(fn ($campanha) => [
                'id' => $campanha->id,
                'id_campanha' => $campanha->id_campanha ?? 'N/A',
                'empreendimento' => $campanha->cod_emp ?? 'N/A',
                'data_inicial' => $campanha->created_at ? $campanha->created_at->format('d/m/Y') : 'N/A',
                'data_final' => 'N/A',
                'status' => $campanha->status ?? 'Em elaboração',
                'subproduto' => $campanha->subproduto ?? 'N/A',
            ]);
    }

    private function getCampanhasQuilombola($contrato)
    {
        return SgcQuilombolaCampanha::where('id_contrato', $contrato)
            ->latest()
            ->get(['id', 'id_campanha', 'cod_emp', 'subproduto', 'status', 'created_at'])
            ->map(fn ($campanha) => [
                'id' => $campanha->id,
                'id_campanha' => $campanha->id_campanha ?? 'N/A',
                'empreendimento' => $campanha->cod_emp ?? 'N/A',
                'data_inicial' => $campanha->created_at ? $campanha->created_at->format('d/m/Y') : 'N/A',
                'data_final' => 'N/A',
                'status' => $campanha->status ?? 'Em elaboração',
                'subproduto' => $campanha->subproduto ?? 'N/A',
            ]);
    }

    private function getCampanhasAudiencia($contrato)
    {
        return SgcAudienciaCampanha::where('id_contrato', $contrato)->latest()
            ->get(['id', 'id_campanha', 'cod_emp', 'subproduto', 'status', 'created_at'])
            ->map(fn ($campanha) => ['id' => $campanha->id, 'id_campanha' => $campanha->id_campanha ?? 'N/A', 'empreendimento' => $campanha->cod_emp ?? 'N/A', 'data_inicial' => $campanha->created_at ? $campanha->created_at->format('d/m/Y') : 'N/A', 'data_final' => 'N/A', 'status' => $campanha->status ?? 'Em elaboração', 'subproduto' => $campanha->subproduto ?? 'N/A']);
    }

    private function getCampanhasPba($contrato)
    {
        return SgcPbaCampanha::where('id_contrato', $contrato)->latest()
            ->get(['id', 'id_campanha', 'cod_emp', 'subproduto', 'status', 'created_at'])
            ->map(fn ($campanha) => ['id' => $campanha->id, 'id_campanha' => $campanha->id_campanha ?? 'N/A', 'empreendimento' => $campanha->cod_emp ?? 'N/A', 'data_inicial' => $campanha->created_at ? $campanha->created_at->format('d/m/Y') : 'N/A', 'data_final' => 'N/A', 'status' => $campanha->status ?? 'Em elaboração', 'subproduto' => $campanha->subproduto ?? 'N/A']);
    }

    private function createPmqa(Request $request, $contrato, $produto, $contratoObj, $subproduto): Response
    {
        $id = $request->query('id');

        if ($id) {
            $pmqa = SgcPmqa::where('id', $id)
                ->where('id_contrato', $contrato)
                ->first();
        } else {
            $pmqa = SgcPmqa::where('id_contrato', $contrato)
                ->where('subproduto', $subproduto)
                ->whereIn('status_aprovacao', ['Em análise', 'Em elaboração'])
                ->latest()
                ->first();
        }

        if (!$pmqa) {
            $pmqa = new SgcPmqa([
                'id_contrato'  => $contrato,
                'subproduto'   => $subproduto,
                'status_aprovacao' => 'Em elaboração',
            ]);
        }

        $empreendimentos = SgcvwEmpreendimentos::where('contrato_id', $contrato)
            ->pluck('cod_emp')
            ->toArray();

        return inertia('Sgc/Contratada/Produtos/Pmqa/Create', [
            'contrato'        => $contrato,
            'produto'         => ucfirst($produto),
            'contratos'       => $contratoObj,
            'subproduto'      => $subproduto,
            'empreendimentos' => $empreendimentos,
            'pmqa'            => $pmqa,
            'canApprove'      => $this->usuarioPodeAprovarPmqa(),
        ]);
    }

    public function submeterPmqa(Request $request, $contrato, $produto, SgcPmqa $pmqa)
    {
        abort_if((int) $pmqa->id_contrato !== (int) $contrato, 404);

        if (!in_array($pmqa->status_aprovacao, ['Rascunho', 'Em elaboração', 'Rejeitada'])) {
            return back()->withErrors(['error' => 'Apenas estudos editáveis podem ser submetidos.']);
        }

        $pmqa->update([
            'status_aprovacao' => 'Em análise',
        ]);

        return redirect()
            ->route('sgc.contratada.produtos.index', [$contrato, $produto])
            ->with('success', 'PMQA submetido para análise.');
    }

    public function aprovarPmqa(Request $request, $contrato, $produto, $pmqa): RedirectResponse
    {
        abort_unless($this->usuarioPodeAprovarPmqa(), 403, 'Usuário sem autorização');

        $pmqaModel = SgcPmqa::where('id', $pmqa)
            ->where('id_contrato', $contrato)
            ->firstOrFail();

        abort_unless($pmqaModel->status_aprovacao === 'Em análise', 422, 'Este PMQA não está em análise.');

        $pmqaModel->update([
            'status_aprovacao' => 'Em elaboração',
            'aprovado_por'     => Auth::user()?->name,
            'aprovado_em'      => now(),
        ]);

        return back()->with('success', 'Campanha aprovada com sucesso!');
    }

        public function enviarAnaliseFasePmqa(Request $request, $contrato, $produto, $pmqa): RedirectResponse
    {
        $data = $request->validate([
            'fase' => 'required|in:apresentacao,configuracao,execucao,resultado,relatorio'
        ]);

        $pmqaModel = SgcPmqa::where('id', $pmqa)
            ->where('id_contrato', $contrato)
            ->firstOrFail();

        $campo = 'status_' . $data['fase'];
        
        abort_unless($pmqaModel->{$campo} === 'Em elaboração' || $pmqaModel->{$campo} === 'Reprovada' || $pmqaModel->{$campo} === 'Bloqueado' || empty($pmqaModel->{$campo}), 422, 'Esta fase não está em elaboração.');

        $pmqaModel->update([
            $campo => 'Em análise',
            'status_aprovacao' => 'Em análise'
        ]);

        return back()->with('success', 'Fase ' . ucfirst($data['fase']) . ' enviada para análise com sucesso!');
    }

    public function aprovarFasePmqa(Request $request, $contrato, $produto, $pmqa): RedirectResponse
    {
        abort_unless($this->usuarioPodeAprovarPmqa(), 403, 'Usuário sem autorização');

        $data = $request->validate([
            'fase' => 'required|in:apresentacao,configuracao,execucao,resultado,relatorio'
        ]);

        $pmqaModel = SgcPmqa::where('id', $pmqa)
            ->where('id_contrato', $contrato)
            ->firstOrFail();

        $campo = 'status_' . $data['fase'];
        
        abort_unless($pmqaModel->{$campo} === 'Em análise', 422, 'Esta fase não está em análise.');

        $pmqaModel->update([
            $campo => 'Aprovada',
            'status_aprovacao' => $data['fase'] === 'relatorio' ? 'Aprovada' : 'Em elaboração'
        ]);

        $fases = ['apresentacao', 'configuracao', 'execucao', 'resultado', 'relatorio'];
        $index = array_search($data['fase'], $fases);

        if ($index !== false && $index < count($fases) - 1) {
            $proximaFase = $fases[$index + 1];
            $campoProxima = 'status_' . $proximaFase;
            if ($pmqaModel->{$campoProxima} === 'Bloqueado' || empty($pmqaModel->{$campoProxima})) {
                $pmqaModel->update([
                    $campoProxima => 'Em elaboração'
                ]);
            }
        }

        return back()->with('success', 'Fase ' . ucfirst($data['fase']) . ' aprovada com sucesso!');
    }

    public function reprovarPmqa(Request $request, $contrato, $produto, $pmqa): RedirectResponse
    {
        abort_unless($this->usuarioPodeAprovarPmqa(), 403, 'Usuário sem autorização');

        $pmqaModel = SgcPmqa::where('id', $pmqa)
            ->where('id_contrato', $contrato)
            ->firstOrFail();

        abort_unless($pmqaModel->status_aprovacao === 'Em análise', 422, 'Este PMQA não está em análise.');

        $updateData = ['status_aprovacao' => 'Rejeitada'];

        $fases = ['apresentacao', 'configuracao', 'execucao', 'resultado', 'relatorio'];
        foreach ($fases as $fase) {
            if ($pmqaModel->{"status_{$fase}"} === 'Em análise') {
                $updateData["status_{$fase}"] = 'Reprovada';
            }
        }

        $pmqaModel->update($updateData);

        return back()->with('success', 'Campanha reprovada com sucesso!');
    }


    public function updatePmqa(Request $request, $contrato, $produto = 'eia')
    {
        $data = $request->validate([
            'id'            => 'nullable|exists:sgc_pmqa,id',
            'cod_emp'       => 'nullable',
            'tema'          => 'nullable',
            'especificacao' => 'nullable|string',
            'introducao'    => 'nullable|string',
            'justificativa' => 'nullable|string',
            'objetivos'     => 'nullable|string',
            'metodologia'   => 'nullable|string',
            'publico_alvo'  => 'nullable|string',
        ]);

        if (isset($data['id'])) {
            $pmqa = SgcPmqa::findOrFail($data['id']);
            $pmqa->update([
                'cod_emp'       => $data['cod_emp'] ?? null,
                'tema'          => is_array($data['tema'] ?? null) ? $data['tema']['id'] : ($data['tema'] ?? null),
                'especificacao' => $data['especificacao'] ?? null,
                'introducao'    => $data['introducao'] ?? null,
                'justificativa' => $data['justificativa'] ?? null,
                'objetivos'     => $data['objetivos'] ?? null,
                'metodologia'   => $data['metodologia'] ?? null,
                'publico_alvo'  => $data['publico_alvo'] ?? null,
            ]);
        } else {
            SgcPmqa::create([
                'id_contrato'   => $contrato,
                'subproduto'    => $request->input('subproduto') ?? 'EIA',
                'status_aprovacao' => 'Em elaboração',
                'cod_emp'       => $data['cod_emp'] ?? null,
                'tema'          => is_array($data['tema'] ?? null) ? $data['tema']['id'] : ($data['tema'] ?? null),
                'especificacao' => $data['especificacao'] ?? null,
                'introducao'    => $data['introducao'] ?? null,
                'justificativa' => $data['justificativa'] ?? null,
                'objetivos'     => $data['objetivos'] ?? null,
                'metodologia'   => $data['metodologia'] ?? null,
                'publico_alvo'  => $data['publico_alvo'] ?? null,
            ]);
        }

        return redirect()
            ->route('sgc.contratada.produtos.index', [
                'contrato'   => $contrato,
                'produto'    => strtolower($produto),
                'subproduto' => isset($pmqa) ? $pmqa->subproduto : ($request->input('subproduto') ?? 'EIA')
            ])
            ->with('success', 'Apresentação do PMQA salva com sucesso.');
    }



    private function getCampanhasPmqa($contrato)
    {
        $pmqas = SgcPmqa::where('id_contrato', $contrato)->get();
        $pmqaIds = $pmqas->pluck('id')->all();

        $resumoVinc = $this->getResumoVinculacoesPmqa($pmqaIds);
        $configuracoes = $this->getConfiguracoesPmqa($pmqaIds);

        return $pmqas->map(function ($pmqa) use ($resumoVinc, $configuracoes) {
            return [
                'id'             => $pmqa->id,
                'id_campanha'    => $pmqa->id,
                'id_contrato'    => $pmqa->id_contrato,
                'chave'          => $pmqa->chave ?? null,
                'tipo'           => $pmqa->tipo ?? 'PMQA',

                'empreendimento' => $pmqa->cod_emp ?? 'N/A',
                'subproduto'     => $pmqa->subproduto ?? $pmqa->tipo ?? 'PMQA',
                'data_inicial'   => $pmqa->created_at ? $pmqa->created_at->format('d/m/Y') : 'N/A',
                'data_final'     => 'N/A',
                'status'         => $pmqa->status_aprovacao ?? 'rascunho',
                'status_apresentacao' => $pmqa->status_apresentacao,
                'status_configuracao' => $pmqa->status_configuracao,
                'status_execucao'     => $pmqa->status_execucao,
                'status_resultado'    => $pmqa->status_resultado,
                'status_relatorio'    => $pmqa->status_relatorio,

                'vinculacoesResumo' => $resumoVinc[$pmqa->id] ?? [
                    'total_listas' => 0,
                    'total_pontos' => 0,
                    'total_pontos_vinculados' => 0,
                ],
                'configuracao' => $configuracoes[$pmqa->id] ?? [
                    'listas' => [],
                    'pontos_sem_lista' => [],
                ],

                'tema'           => $pmqa->tema,
                'cod_emp'        => $pmqa->cod_emp,
                'especificacao'  => $pmqa->especificacao,
                'introducao'     => $pmqa->introducao,
                'justificativa'  => $pmqa->justificativa,
                'objetivos'      => $pmqa->objetivos,
                'metodologia'    => $pmqa->metodologia,
                'publico_alvo'   => $pmqa->publico_alvo,

                'status_aprovacao' => $pmqa->status_aprovacao,
                'created_at'       => optional($pmqa->created_at)->toISOString(),
                'updated_at'       => optional($pmqa->updated_at)->toISOString(),
                'deleted_at'       => optional($pmqa->deleted_at)->toISOString(),
            ];
        });
    }

    private function getTabRascunhoPmqa(SgcPmqa $pmqa, Request $request, int $subStep): string
    {
        if ($request->has('tab')) {
            return (string) $request->query('tab');
        }

        if (!in_array($pmqa->status_aprovacao, ['Em elaboração', 'Rejeitada'])) {
            return 'apresentacao';
        }

        $temCampanhaExecucao = SgcPmqaExecCampanha::where('pmqa_id', $pmqa->id)->exists();
        if ($temCampanhaExecucao) {
            return 'execucao';
        }

        return $subStep >= 2 ? 'configuracao' : 'apresentacao';
    }

    private function getSubStepRascunhoPmqa(SgcPmqa $pmqa, Request $request): int
    {
        if ($request->has('subStep')) {
            return max(1, min(4, (int) $request->query('subStep')));
        }

        if (!in_array($pmqa->status_aprovacao, ['Em elaboração', 'Rejeitada'])) {
            return 1;
        }

        $temPontos = SgcPmqaPonto::where('pmqa_id', $pmqa->id)->exists();
        if (!$temPontos) {
            return 2;
        }

        $temListas = SgcPmqaParametroLista::where('pmqa_id', $pmqa->id)->exists();
        if (!$temListas) {
            return 3;
        }

        return 4;
    }

    private function getResumoVinculacoesPmqa(array $pmqaIds): array
    {
        if (empty($pmqaIds)) return [];

        $listasPorPmqa = SgcPmqaParametroLista::query()
            ->whereIn('pmqa_id', $pmqaIds)
            ->selectRaw('pmqa_id, COUNT(*) as total_listas')
            ->groupBy('pmqa_id')
            ->pluck('total_listas', 'pmqa_id')
            ->toArray();

        $pontosPorPmqa = SgcPmqaPonto::query()
            ->whereIn('pmqa_id', $pmqaIds)
            ->selectRaw('pmqa_id, COUNT(*) as total_pontos')
            ->groupBy('pmqa_id')
            ->pluck('total_pontos', 'pmqa_id')
            ->toArray();

        $pontosVinculadosPorPmqa = DB::table('sgc_pmqa_config_ponto_lista as vpl')
            ->whereIn('vpl.pmqa_id', $pmqaIds)
            ->selectRaw('vpl.pmqa_id, COUNT(DISTINCT vpl.ponto_id) as total_pontos_vinculados')
            ->groupBy('vpl.pmqa_id')
            ->pluck('total_pontos_vinculados', 'pmqa_id')
            ->toArray();

        $map = [];
        foreach ($pmqaIds as $id) {
            $map[$id] = [
                'total_listas' => $listasPorPmqa[$id] ?? 0,
                'total_pontos' => $pontosPorPmqa[$id] ?? 0,
                'total_pontos_vinculados' => $pontosVinculadosPorPmqa[$id] ?? 0,
            ];
        }

        return $map;
    }

    private function getConfiguracoesPmqa(array $pmqaIds): array
    {
        if (empty($pmqaIds)) return [];

        $map = [];
        foreach ($pmqaIds as $pmqaId) {
            $map[$pmqaId] = [
                'listas' => [],
                'pontos_sem_lista' => [],
            ];
        }

        $parametros = DB::table('sgc_pmqa_parametros_lista as l')
            ->leftJoin('sgc_pmqa_config_parametros as cp', 'cp.parametro_lista_id', '=', 'l.id')
            ->leftJoin('parametros as p', 'p.id', '=', 'cp.parametro_id')
            ->whereIn('l.pmqa_id', $pmqaIds)
            ->orderBy('l.id')
            ->orderBy('p.parametro')
            ->get([
                'l.pmqa_id',
                'l.id as lista_id',
                'l.nome as lista_nome',
                'l.medir_iqa',
                'p.id as parametro_id',
                'p.parametro as parametro_nome',
            ]);

        foreach ($parametros as $parametro) {
            $pmqaId = (int) $parametro->pmqa_id;
            $listaId = (int) $parametro->lista_id;

            if (!isset($map[$pmqaId]['listas'][$listaId])) {
                $map[$pmqaId]['listas'][$listaId] = [
                    'id' => $listaId,
                    'nome' => $parametro->lista_nome,
                    'medir_iqa' => (bool) $parametro->medir_iqa,
                    'parametros' => [],
                    'pontos' => [],
                ];
            }

            if ($parametro->parametro_id) {
                $map[$pmqaId]['listas'][$listaId]['parametros'][] = [
                    'id' => (int) $parametro->parametro_id,
                    'nome' => $parametro->parametro_nome,
                ];
            }
        }

        $pontos = DB::table('sgc_pmqa_pontos as p')
            ->leftJoin('sgc_pmqa_config_ponto_lista as pl', function ($join) {
                $join->on('pl.ponto_id', '=', 'p.id')
                    ->on('pl.pmqa_id', '=', 'p.pmqa_id');
            })
            ->leftJoin('sgc_pmqa_parametros_lista as l', 'l.id', '=', 'pl.lista_id')
            ->whereIn('p.pmqa_id', $pmqaIds)
            ->orderBy('l.id')
            ->orderBy('p.id')
            ->get([
                'p.pmqa_id',
                'l.id as lista_id',
                'p.id',
                'p.nome_ponto_coleta',
                'p.classe',
                'p.tipo_ambiente',
                'p.uf',
                'p.municipio',
                'p.bacia_hidrografica',
                'p.km_rodovia',
                'p.estaca',
                'p.lat_x',
                'p.long_y',
            ]);

        foreach ($pontos as $ponto) { 
            $pmqaId = (int) $ponto->pmqa_id;
            $listaId = $ponto->lista_id ? (int) $ponto->lista_id : null;

            $pontoData = [
                'id' => (int) $ponto->id,
                'nome_ponto_coleta' => $ponto->nome_ponto_coleta,
                'classe' => $ponto->classe,
                'tipo_ambiente' => $ponto->tipo_ambiente,
                'uf' => $ponto->uf,
                'municipio' => $ponto->municipio,
                'bacia_hidrografica' => $ponto->bacia_hidrografica,
                'km_rodovia' => $ponto->km_rodovia,
                'estaca' => $ponto->estaca,
                'lat_x' => $ponto->lat_x,
                'long_y' => $ponto->long_y,
            ];

            if ($listaId && isset($map[$pmqaId]['listas'][$listaId])) {
                $map[$pmqaId]['listas'][$listaId]['pontos'][] = $pontoData;
                continue;
            }

            $map[$pmqaId]['pontos_sem_lista'][] = $pontoData;
        }

        foreach ($map as $pmqaId => $configuracao) {
            $map[$pmqaId]['listas'] = array_values($configuracao['listas']);
        }

        return $map;
    }

    private function usuarioPodeAprovarPmqa(): bool
    {
        $user = Auth::user();

        if ($user instanceof \App\Models\User) {
            return $user->hasAnyRole(['Administrador', 'Fiscal']);
        }

        return false;
    }
}
