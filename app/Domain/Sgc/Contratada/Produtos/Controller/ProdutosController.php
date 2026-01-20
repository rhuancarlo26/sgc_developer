<?php

namespace App\Domain\Sgc\Contratada\Produtos\Controller;

use App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\Parametro\Services\ParametroService;
use App\Shared\Http\Controllers\Controller;
use App\Models\Contrato;
use App\Models\SgcvwEmpreendimentos;
use App\Domain\Sgc\Contratada\Produtos\Services\ProdutosService;
use App\Domain\Sgc\Contratada\Produtos\Fauna\Services\FaunaService;
use App\Domain\Sgc\Contratada\Produtos\Espeleologia\Services\EspeleoService;
use App\Models\ServicoPmqaParametro;
use App\Models\SgcFaunaCampanha;
use App\Models\SgcEspeleoCampanha;
use App\Models\SgcEspeleoProfissional;
use App\Models\SgcPmqaCampanha;
use App\Models\SgcPmqaPonto;
use App\Models\SgcvwSubprodutos;
use App\Models\SgcEspeleoEstudosPosteriores;
use App\Models\SgcPmqa;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProdutosController extends Controller
{
    protected $produtosService;
    protected $faunaService;
    protected $espeleoService;
    protected $parametroService;

    public function __construct(
        ProdutosService $produtosService,
        FaunaService $faunaService,
        EspeleoService $espeleoService,
        ParametroService $parametroService
    ) {
        $this->produtosService = $produtosService;
        $this->faunaService = $faunaService;
        $this->espeleoService = $espeleoService;
        $this->parametroService = $parametroService;
    }

    public function index(Request $request, $contrato, $produto): Response
    {
        $subprodutos = $this->produtosService->getSubprodutosByContrato($contrato, $produto);
        $contratoObj = Contrato::findOrFail($contrato);

        $campanhas = match ($produto) {
            'fauna'        => $this->getCampanhasFauna($contrato),
            'pmqa', 'eia'  => $this->getCampanhasPmqa($contrato),
            'espeleologia' => $this->getCampanhasEspeleologia($contrato),
            default        => collect(), // segurança
        };

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
        Log::info('Acessando create', ['contrato' => $contrato, 'produto' => $produto, 'subproduto' => $request->query('subproduto')]);
        $contratoObj = Contrato::findOrFail($contrato);
        $subproduto = $request->query('subproduto');
        $campanhaId = $request->query('campanha_id');

        if ($campanhaId && in_array($produto, ['pmqa', 'eia'])) {
            return $this->createPmqaByCampanha($request, $contrato, $produto, $contratoObj, $campanhaId);
        }

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

        // Atualizar o método create()
        if ($produto === 'fauna') {
            return $this->createFauna($request, $contrato, $produto, $contratoObj, $subproduto);
        } elseif ($produto === 'pmqa' || $produto === 'eia') { // <-- MUDANÇA AQUI
            // Se o produto for 'pmqa' OU 'eia', chamamos a função de criação do PMQA.
            // Passamos a variável $produto original ('eia') para que a tela mantenha o título correto.
            return $this->createPmqa($request, $contrato, $produto, $contratoObj, $subproduto);
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
            Log::info('Draft criado para Fauna', ['draft_id' => $draft->id]);
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

    private function createPmqa(Request $request, $contrato, $produto, $contratoObj, $subproduto): Response
    {

        $pmqa = SgcPmqa::firstOrCreate(
            ['id_contrato' => $contrato],
            [
                'status_aprovacao' => 'rascunho',
                'subproduto'       => $subproduto,
            ]
        );

        $searchParams = $request->only(['columns', 'value']);
        $tabParametros = $this->parametroService->index($pmqa, $searchParams);

        $empreendimentos = SgcvwEmpreendimentos::where('contrato_id', $contrato)
            ->pluck('cod_emp')
            ->toArray();

        return inertia('Sgc/Contratada/Produtos/Pmqa/Create', [
            'contrato'        => $contrato,
            'produto'         => ucfirst($produto),
            'contratos'       => $contratoObj,
            'subproduto'      => $subproduto,
            'empreendimentos' => $empreendimentos,
            'pmqa'            => $pmqa, // ✅ sempre com ID
            ...$tabParametros,
        ]);
    }



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
            'tema_id'       => is_array($data['tema']) ? $data['tema']['id'] : $data['tema'],
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
                'produto'  => 'fauna',
            ])
            ->with('success', 'Campanha de Fauna salva com sucesso!');
    }



    private function getCampanhasPmqa($contrato)
    {
        return SgcPmqa::where('id_contrato', $contrato)
            ->get()
            ->map(function ($pmqa) {
                return [
                    // 🔹 Identificação
                    'id'             => $pmqa->id,
                    'id_campanha'    => $pmqa->id,
                    'id_contrato'    => $pmqa->id_contrato,
                    'chave'          => $pmqa->chave ?? null,
                    'tipo'           => $pmqa->tipo ?? 'PMQA',

                    // 🔹 Campos usados na LISTAGEM
                    'empreendimento' => $pmqa->cod_emp ?? 'N/A',
                    'subproduto'     => $pmqa->tipo ?? 'PMQA',
                    'data_inicial'   => $pmqa->created_at
                        ? $pmqa->created_at->format('d/m/Y')
                        : 'N/A',
                    'data_final'     => 'N/A',
                    'status'         => $pmqa->status_aprovacao ?? 'rascunho',

                    // 🔹 Campos da APRESENTAÇÃO (modal)
                    'tema'           => $pmqa->tema,
                    'cod_emp'        => $pmqa->cod_emp,
                    'especificacao'  => $pmqa->especificacao,
                    'introducao'     => $pmqa->introducao,
                    'justificativa'  => $pmqa->justificativa,
                    'objetivos'      => $pmqa->objetivos,
                    'metodologia'    => $pmqa->metodologia,
                    'publico_alvo'   => $pmqa->publico_alvo,

                    // 🔹 Status e datas completas
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
        dd( $searchParams );
        $tabParametros = $this->parametroService->index($pmqa, $searchParams);

        return inertia('Sgc/Contratada/Produtos/Pmqa/Create', [
            'contrato'        => $contrato,
            'produto'         => ucfirst($produto),
            'contratos'       => $contratoObj,
            'subproduto'      => $pmqa->subproduto,
            'empreendimentos' => $empreendimentos,
            'pmqa'            => $pmqa,
            'subStep'         => (int) $request->query('subStep', 1),
            ...$tabParametros,
        ]);
    }
}
