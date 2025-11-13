<?php

namespace App\Domain\Sgc\Contratada\Produtos\Controller;

use App\Shared\Http\Controllers\Controller;
use App\Models\Contrato;
use App\Models\SgcvwEmpreendimentos;
use App\Domain\Sgc\Contratada\Produtos\Services\ProdutosService;
use App\Domain\Sgc\Contratada\Produtos\Fauna\Services\FaunaService;
use App\Domain\Sgc\Contratada\Produtos\Espeleologia\Services\EspeleoService;
use App\Models\SgcFaunaCampanha;
use App\Models\SgcEspeleoCampanha;
use App\Models\SgcEspeleoProfissional;
use Illuminate\Http\Request;
use Inertia\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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


        $keys = array_keys((array) $request->all());
        $trecho = $keys[0] ?? null;

        $campanhas = $produto === 'fauna'
            ? $this->getCampanhasFauna($contrato, $this->bota_os_espacos_de_volta($trecho))
            : $this->getCampanhasEspeleologia($contrato, $this->bota_os_espacos_de_volta($trecho));

        return inertia('Sgc/Contratada/Produtos/Fauna/Fauna', [
            'subprodutos' => $subprodutos,
            'contrato' => $contrato,
            'produto' => ucfirst($produto),
            'requests' => $request,
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
        } else {
            return $this->createEspeleologia($request, $contrato, $produto, $contratoObj, $subproduto);
        }
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

    private function getCampanhasFauna($contrato, $subproduto)
    {
        $arr_pesquisa = ['id_contrato' => $contrato];
        if($subproduto) {
            $arr_pesquisa['subproduto'] = $subproduto;
        }
        return SgcFaunaCampanha::where($arr_pesquisa)
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
            ->select('cod_emp', 'subtrecho_ini', 'subtrecho_fin', 'km_ini', 'km_fin', 'tipo_de_intervencao', 'descricao', 'bioma')
            ->get()
            ->map(function ($emp) {
                return [
                    'cod_emp' => $emp->cod_emp,
                    'subtrecho' => $emp->subtrecho_ini && $emp->subtrecho_fin ? $emp->subtrecho_ini . ' - ' . $emp->subtrecho_fin : '',
                    'segmento' => $emp->km_ini && $emp->km_fin ? $emp->km_ini . ' - ' . $emp->km_fin : '',
                    'extensao' => $emp->km_fin && $emp->km_ini ? $emp->km_fin . ' - ' . $emp->km_ini : 0,
                    'tipo_de_intervencao' => $emp->tipo_de_intervencao ?? '',
                    'descricao' => $emp->descricao ?? '',
                    'bioma' => $emp->bioma ?? '',
                ];
            });

        $profissionais = SgcEspeleoProfissional::where('id_contrato', $contrato)->get([
            'id', 'profissional', 'formacao', 'telefone', 'cpf', 'email', 'curriculum_lattes',
            'funcao', 'ctf', 'validade', 'conselho_de_classe', 'numero_de_registro', 'status', 'observacao'
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
        // Carregar todos os subprodutos de Pontencial Espeleológico, filtrando pela palavra "potencial" e "espeleológico"
        $subprodutos_potencial_espeleologico = $this->getSubprodutosPotencialEspeleologico($contrato);


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
            'subprodutos_potencial_espeleologico' => $subprodutos_potencial_espeleologico,
        ]);

    }
    private function getSubprodutosPotencialEspeleologico($contrato)
    {
        // Lógica para buscar os subprodutos de potencial espeleológico
        // Exemplo fictício:
        return SgcEspeleoCampanha::where('id_contrato', $contrato)
            ->where('subproduto', 'like', '%potencial%')
            ->where('subproduto', 'like', '%espeleológico%')
            ->pluck('subproduto')
            ->unique()
            ->toArray();
    }

    private function getCampanhasEspeleologia($contrato, $subproduto)
    {
        $arr_pesquisa = ['id_contrato' => $contrato];
        if($subproduto) {
            $arr_pesquisa['subproduto'] = $subproduto;
        }
        return SgcEspeleoCampanha::where($arr_pesquisa)
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

    private function bota_os_espacos_de_volta($string)
    {
        return str_replace('_', ' ', $string);
    }
}
