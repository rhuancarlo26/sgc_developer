<?php

namespace App\Domain\Sgc\Contratada\Produtos\Controller;

use App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\Parametro\Services\ParametroService;
use App\Models\SgcPatrimonioEquipe;
use App\Shared\Http\Controllers\Controller;
use App\Models\Contrato;
use App\Models\SgcvwEmpreendimentos;
use App\Domain\Sgc\Contratada\Produtos\Services\ProdutosService;
use App\Domain\Sgc\Contratada\Produtos\Fauna\Services\FaunaService;
use App\Domain\Sgc\Contratada\Produtos\Espeleologia\Services\EspeleoService;
use App\Domain\Sgc\Contratada\Produtos\PMQA\app\Services\PmqaService;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Execucao\app\Services\CampanhaService;
use App\Models\SgcFaunaCampanha;
use App\Models\SgcEspeleoCampanha;
use App\Models\SgcEspeleoProfissional;
use App\Models\SgcPmqaPonto;
use App\Models\SgcvwSubprodutos;
use App\Models\SgcEspeleoEstudosPosteriores;
use App\Models\SgcPatrimonioPaipa;
use App\Models\SgcPmqa;
use App\Models\SgcPmqaParametroLista;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProdutosController extends Controller
{
    protected $produtosService;
    protected $faunaService;
    protected $espeleoService;
    protected $parametroService;
    protected $execucaoCampanhaService;
    protected $pmqaService;

    public function __construct(
        ProdutosService $produtosService,
        FaunaService $faunaService,
        EspeleoService $espeleoService,
        ParametroService $parametroService,
        CampanhaService $execucaoCampanhaService,
        PmqaService $pmqaService
    ) {
        $this->produtosService = $produtosService;
        $this->faunaService = $faunaService;
        $this->espeleoService = $espeleoService;
        $this->parametroService = $parametroService;
        $this->execucaoCampanhaService = $execucaoCampanhaService;
        $this->pmqaService = $pmqaService;
    }

    public function index(Request $request, $contrato, $produto): Response
    {
        $subprodutos = $this->produtosService->getSubprodutosByContrato($contrato, $produto);
        $contratoObj = Contrato::findOrFail($contrato);

        $campanhas = match ($produto) {
            'fauna'        => $this->getCampanhasFauna($contrato),
            'pmqa', 'eia' => $this->pmqaService->getCampanhas($contrato),
            'espeleologia' => $this->getCampanhasEspeleologia($contrato),
            'patrimonio'   => $this->getCampanhasPatrimonio($contrato),
            default        => collect(),
        };

        // $campanhas = $produto === 'fauna'
        //     ? $this->getCampanhasFauna($contrato)
        //     : $this->getCampanhasEspeleologia($contrato);

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
        $campanhaId = $request->query('campanha_id');

        if ($campanhaId && in_array($produto, ['pmqa', 'eia'])) {
            return $this->createPmqaByCampanha($request, $contrato, $produto, $contratoObj, $campanhaId);
        }

        //CONFERIR ESSES TRECHOS
        // if (!$subproduto) {
        // Log::info('Acessando create', ['contrato' => $contrato, 'produto' => $produto, 'subproduto' => $request->query('subproduto')]);
        // $contratoObj = Contrato::findOrFail($contrato);
        // $subproduto = $request->query('subproduto');

        if (!$subproduto) {
            Log::warning('Subproduto não selecionado', ['contrato' => $contrato, 'produto' => $produto]);
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

        if ($produto === 'fauna') {
            return $this->createFauna($request, $contrato, $produto, $contratoObj, $subproduto);
        } elseif ($produto === 'patrimonio') {
            return $this->createPatrimonio($request, $contrato, $produto, $contratoObj, $subproduto);
        } elseif ($produto === 'pmqa' || $produto === 'eia') {
            return $this->createPmqa($request, $contrato, $produto, $contratoObj, $subproduto);
        } else {
            return $this->createEspeleologia($request, $contrato, $produto, $contratoObj, $subproduto);
        }

        $campanhas = match ($produto) {
            'fauna' => $this->getCampanhasFauna($contrato),
            'pmqa' => $this->getCampanhasPmqa($contrato),
            default => $this->getCampanhasEspeleologia($contrato)
        };
    }

    private function createFauna(Request $request, $contrato, $produto, $contratoObj, $subproduto): Response
    {
        $draft = SgcFaunaCampanha::where('id_contrato', $contrato)
            ->where('subproduto', $subproduto)
            ->where('status', 'Em elaboração')
            ->first();

        if (!$draft) {
            $draft = SgcFaunaCampanha::create([
                'id_contrato' => $contrato,
                'subproduto' => $subproduto,
                'status' => 'Em elaboração',
            ]);
        }

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
            'campanhaId' => $draft->id,
            'draftData' => $draft->toArray(),
        ]);
    }

    private function getCampanhasFauna($contrato)
    {
        return SgcFaunaCampanha::where('id_contrato', $contrato)
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
    }

    private function createEspeleologia(Request $request, $contrato, $produto, $contratoObj, $subproduto): Response
    {
        $draft = SgcEspeleoCampanha::where('id_contrato', $contrato)
            ->where('subproduto', $subproduto)
            ->where('status', 'Em elaboração')
            ->first();

        if (!$draft) {
            $draft = SgcEspeleoCampanha::create([
                'id_contrato' => $contrato,
                'id_campanha' => '3',
                'subproduto' => $subproduto,
                'status' => 'Em elaboração',
            ]);
        }

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

        $estudosPosteriores = SgcEspeleoEstudosPosteriores::where('campanha_id', $draft->id)
            ->get(['id', 'subproduto_id', 'quantidade', 'coordenadas', 'necessario'])
            ->toArray();

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

    private function createPmqa(Request $request, $contrato, $produto, $contratoObj, $subproduto): Response
    {

        $pmqa = $this->pmqaService->criarCampanha($contrato, $subproduto);

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
        ]);
    }

    public function aprovarPmqa(Request $request, $contrato, $produto, $pmqa): RedirectResponse
    {
        $pmqaModel = SgcPmqa::where('id', $pmqa)
            ->where('id_contrato', $contrato)
            ->firstOrFail();

        $pmqaModel->update([
            'status_aprovacao' => 'Em elaboração',
            'aprovado_por'     => Auth::getName(),
            'aprovado_em'      => now(),
        ]);

        return back()->with('success', 'Campanha aprovada com sucesso!');
    }

    // public function reprovarPmqa(Request $request, $contrato, $produto, $pmqa): \Illuminate\Http\RedirectResponse
    // {
    //     $data = $request->validate([
    //         'motivo' => 'nullable|string|max:1000',
    //     ]);

    //     $pmqaModel = SgcPmqa::where('id', $pmqa)
    //         ->where('id_contrato', $contrato)
    //         ->firstOrFail();

    //     $pmqaModel->update([
    //         'status_aprovacao'  => 'Reprovado',
    //         'reprovado_por'     => Auth::id(),
    //         'reprovado_em'      => now(),
    //         'motivo_reprovacao' => $data['motivo'] ?? null,
    //     ]);

    //     return back()->with('error', 'Campanha reprovada.');
    // }



    public function updatePmqa(Request $request, $contrato)
    {
        $data = $request->validate([
            'id'            => 'required|exists:sgc_pmqa,id',
            'cod_emp'       => 'nullable|string',
            'tema'          => 'nullable',
            'especificacao' => 'nullable|string',
            'introducao'    => 'nullable|string',
            'justificativa' => 'nullable|string',
            'objetivos'     => 'nullable|string',
            'metodologia'   => 'nullable|string',
            'publico_alvo'  => 'nullable|string',
        ]);

        $pmqa = SgcPmqa::findOrFail($data['id']);

        $pmqa->update([
            'cod_emp'       => $data['cod_emp'],
            'tema'          => is_array($data['tema']) ? $data['tema']['id'] : $data['tema'],
            'especificacao' => $data['especificacao'],
            'introducao'    => $data['introducao'],
            'justificativa' => $data['justificativa'],
            'objetivos'     => $data['objetivos'],
            'metodologia'   => $data['metodologia'],
            'publico_alvo'  => $data['publico_alvo'],
        ]);

        return redirect()
            ->route('sgc.contratada.produtos.index', [
                'contrato' => $contrato,
                'produto'  => 'eia',
            ])
            ->with('success', 'Campanha de Fauna salva com sucesso!');
    }



    private function getCampanhasPmqa($contrato)
    {
        $pmqas = SgcPmqa::where('id_contrato', $contrato)->get();

        $resumoVinc = $this->getResumoVinculacoesPmqa($pmqas->pluck('id')->all());

        return $pmqas->map(function ($pmqa) use ($resumoVinc) {
            return [
                'id'             => $pmqa->id,
                'id_campanha'    => $pmqa->id,
                'id_contrato'    => $pmqa->id_contrato,
                'chave'          => $pmqa->chave ?? null,
                'tipo'           => $pmqa->tipo ?? 'PMQA',

                'empreendimento' => $pmqa->cod_emp ?? 'N/A',
                'subproduto'     => $pmqa->tipo ?? 'PMQA',
                'data_inicial'   => $pmqa->created_at ? $pmqa->created_at->format('d/m/Y') : 'N/A',
                'data_final'     => 'N/A',
                'status'         => $pmqa->status_aprovacao ?? 'rascunho',

                'vinculacoesResumo' => $resumoVinc[$pmqa->id] ?? [
                    'total_listas' => 0,
                    'total_pontos' => 0,
                    'total_pontos_vinculados' => 0,
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


    private function createPmqaByCampanha(
        Request $request,
        $contrato,
        $produto,
        $contratoObj,
        $campanhaId
    ): Response {
        $pmqa = SgcPmqa::where('id', $campanhaId)
            ->where('id_contrato', $contrato)
            ->firstOrFail();

        $empreendimentos = SgcvwEmpreendimentos::where('contrato_id', $contrato)
            ->pluck('cod_emp')
            ->toArray();

        $searchParams = $request->only(['columns', 'value']);
        $tabParametros = $this->parametroService->index($pmqa, $searchParams);
        $listasVinculacoes = SgcPmqaParametroLista::with(['pontos'])
            ->where('pmqa_id', $pmqa->id)
            ->get();

        $vinculacoes = SgcPmqaPonto::with(['lista', 'vinculacao'])
            ->where('pmqa_id', $pmqa->id)
            ->get();
        Log::info('Create PMQA payload', [
            'pmqa_id' => $pmqa->id,
            'vinculacoes_count' => $vinculacoes->count(),
            'component' => 'Sgc/Contratada/Produtos/Pmqa/Create',
        ]);

        $searchExec = $request->only(['columns', 'value']);
        $execucao = $this->execucaoCampanhaService->index($pmqa, $searchExec);

        return inertia('Sgc/Contratada/Produtos/Pmqa/Create', [
            'contrato'        => $contrato,
            'produto'         => ucfirst($produto),
            'contratos'       => $contratoObj,
            'subproduto'      => $pmqa->subproduto,
            'empreendimentos' => $empreendimentos,
            'pmqa'            => $pmqa,
            'subStep'         => (int) $request->query('subStep', 1),
            ...$tabParametros,
            'vinculacoes' => $vinculacoes,
            'listasVinculacoes' => $listasVinculacoes,
            ...$execucao,
        ]);
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

    private function getVinculacoesParaTabela(int $pmqaId): array
    {
        $rows = DB::table('sgc_pmqa_parametros_lista as l')
            ->leftJoin('sgc_pmqa_config_ponto_lista as pl', 'pl.lista_id', '=', 'l.id')
            ->leftJoin('sgc_pmqa_pontos as p', 'p.id', '=', 'pl.ponto_id')
            ->where('l.pmqa_id', $pmqaId)
            ->orderBy('l.id')
            ->orderBy('p.id')
            ->get([
                'l.id as lista_id',
                'l.nome as lista_nome',
                'p.id as ponto_id',
                'p.nome_ponto_coleta',
            ]);

        $grouped = [];

        foreach ($rows as $r) {
            if (!isset($grouped[$r->lista_id])) {
                $grouped[$r->lista_id] = [
                    'id' => (int) $r->lista_id,
                    'nome' => $r->lista_nome,
                    'pontos' => [],
                ];
            }

            if ($r->ponto_id) {
                $grouped[$r->lista_id]['pontos'][] = [
                    'id' => (int) $r->ponto_id,
                    'nome_ponto_coleta' => $r->nome_ponto_coleta,
                ];
            }
        }

        return [
            'data' => array_values($grouped),
            'links' => [],
        ];
    }

    private function createPatrimonio(Request $request, $contrato, $produto, $contratoObj, $subproduto): Response
    {
      $paipaId = $request->query('paipa_id');

      $paipa = $paipaId
        ? SgcPatrimonioPaipa::where('contrato_id', $contrato)->findOrFail($paipaId)
        : SgcPatrimonioPaipa::where('contrato_id', $contrato)
          ->where('subproduto', $subproduto)
          ->where('status', 'Em elaboração')
          ->first();

      if (!$paipa) {
        $paipa = SgcPatrimonioPaipa::create([
          'contrato_id' => $contrato,
          'subproduto' => $subproduto,
          'status' => 'Em elaboração',
          'versao' => 1,
        ]);
      }

      $paipa->load('equipe');

      $empreendimentos = SgcvwEmpreendimentos::where('contrato_id', $contrato)
            ->get(['id',
              'cod_emp',
              'br',
              'uf',
              'subtrecho_ini',
              'subtrecho_fin',
              'subtrecho_ini2',
              'subtrecho_fin3',
              'subtrecho_ini3',
              'subtrecho_fin32',
              'km_ini',
              'km_fin',
              'km_ini2',
              'km_fin2',
              'km_ini3',
              'km_fin3',
              'extensao',
              'tipo_de_intervencao',
              'descricao',
              'bioma',
              'coordenadas',
              ])
            ->map(function ($empreendimento) {
                return [
                  'id' => $empreendimento->id,
                  'cod_emp' => $empreendimento->cod_emp,
                  'br' => $empreendimento->br,
                  'uf' => $empreendimento->uf,
                  'subtrecho_ini' => $empreendimento->subtrecho_ini,
                  'subtrecho_fin' => $empreendimento->subtrecho_fin,
                  'subtrecho_ini2' => $empreendimento->subtrecho_ini2,
                  'subtrecho_fin3' => $empreendimento->subtrecho_fin3,
                  'subtrecho_ini3' => $empreendimento->subtrecho_ini3,
                  'subtrecho_fin32' => $empreendimento->subtrecho_fin32,
                  'km_ini' => $empreendimento->km_ini,
                  'km_fin' => $empreendimento->km_fin,
                  'km_ini2' => $empreendimento->km_ini2,
                  'km_fin2' => $empreendimento->km_fin2,
                  'km_ini3' => $empreendimento->km_ini3,
                  'km_fin3' => $empreendimento->km_fin3,
                  'extensao' => $empreendimento->extensao,
                  'tipo_de_intervencao' => $empreendimento->tipo_de_intervencao,
                  'descricao' => $empreendimento->descricao,
                  'bioma' => $empreendimento->bioma,
                  'coordenadas' => $empreendimento->coordenadas,
                ];
            })
            ->toArray();

      $profissionais = SgcPatrimonioEquipe::ativo()
        ->orderBy('nome')
        ->get([
          'id',
          'nome',
          'cpf',
          'cnpj',
          'email',
          'profissao',
          'funcao',
          'conselho_classe',
          'numero_registro',
          'carteira_profissional',
          'ct',
          'obs',
        ]);

        return inertia('Sgc/Contratada/Produtos/Patrimonio/Create', [
            'contrato' => $contrato,
            'produto' => ucfirst($produto),
            'contratos' => $contratoObj,
            'subproduto' => $subproduto,
            'empreendimentos' => $empreendimentos,
            'paipa' => $paipa,
            'paipaId' => $paipa->id,
            'profissionais' => $profissionais,
        ]);
    }

    private function getCampanhasPatrimonio($contrato)
    {
      return SgcPatrimonioPaipa::where('contrato_id', $contrato)
        ->with('empreendimento')
        ->get()
        ->map(function ($paipa) {
          return [
            'id' => $paipa->id,
            'id_campanha' => $paipa->id,
            'empreendimento' => $paipa->empreendimento?->cod_emp ?? 'N/A',
            'data_inicial' => $paipa->created_at?->format('d/m/Y') ?? 'N/A',
            'data_final' => 'N/A',
            'status' => $paipa->status ?? 'Em elaboração',
            'subproduto' => $paipa->subproduto ?? 'N/A',
          ];
        });
    }
}
