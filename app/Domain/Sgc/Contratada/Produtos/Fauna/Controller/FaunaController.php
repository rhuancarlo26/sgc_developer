<?php

namespace App\Domain\Sgc\Contratada\Produtos\Fauna\Controller;

use App\Shared\Http\Controllers\Controller;
use App\Domain\Sgc\Contratada\Produtos\Fauna\Services\FaunaService;
use App\Domain\Sgc\Contratada\Produtos\Fauna\Services\FaunaFiscalService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use App\Models\SgcFaunaCampanhaAbios;
use App\Models\SgcFaunaCampanha;
use App\Models\SgcFaunaModuloAmostral;
use App\Models\SgcvwEmpreendimentos;
use App\Models\SgcFaunaAnaliseEtapa;
use App\Models\SgcFaunaComentarios;
use App\Models\Contrato;
use App\Domain\Sgc\Contratada\Produtos\Services\ProdutosService;
use Inertia\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class FaunaController extends Controller
{
    protected $faunaService;

    protected $produtosService;
    protected $faunaFiscalService;

    public function __construct(FaunaService $faunaService, FaunaFiscalService $faunaFiscalService, ProdutosService $produtosService)
    {
        $this->faunaService = $faunaService;
        $this->produtosService = $produtosService;
        $this->faunaFiscalService = $faunaFiscalService;
    }

    public function storeProfissional(Request $request, $contrato, $produto): RedirectResponse
    {
        $validated = $request->validate([
            'profissional' => 'required|string|max:255',
            'formacao' => 'required|string|max:255',
            'telefone' => 'nullable|string|max:255',
            'cpf' => 'nullable|string|max:255|regex:/^\d{3}\.\d{3}\.\d{3}-\d{2}$/',
            'email' => 'nullable|email|max:255',
            'curriculum_lattes' => 'nullable|string|max:255|url',
            'funcao' => 'nullable|string|max:255',
            'ctf' => 'nullable|string|max:255',
            'validade' => 'nullable|date',
            'conselho_de_classe' => 'required|string|in:Sim,Não',
            'numero_de_registro' => 'nullable|required_if:conselho_de_classe,Sim|integer',
            'status' => 'required|string|in:Ativo,Inativo',
            'observacao' => 'nullable|string',
        ]);

        try {
            $this->faunaService->salvarProfissional($contrato, $validated);
            return redirect()->back()->with('success', 'Profissional salvo com sucesso!');
        } catch (\Exception $e) {
            Log::error('FaunaController: Erro ao salvar profissional', [
                'contrato' => $contrato,
                'produto' => $produto,
                'erro' => $e->getMessage(),
            ]);
            return redirect()->back()->withErrors(['error' => 'Erro ao salvar profissional: ' . $e->getMessage()]);
        }
    }

    public function salvarCampanha(Request $request, $contrato, $produto): RedirectResponse
    {
        $validated = $request->validate([
            'data_campanha_inicial' => 'nullable|date',
            'data_campanha_final' => 'nullable|date',
            'periodo' => 'nullable|string|max:255',
            'observacoes' => 'nullable|string',
            'id_abio' => 'nullable|array',
            'cod_emp' => 'required|string|max:255',
            'subproduto' => 'required|string|max:255',
            'nao_se_aplica' => 'nullable|boolean',
            'profissionais' => 'nullable|array',
            'profissionais.*.profissional' => 'required_with:profissionais|string|max:255',
            'profissionais.*.grupo_faunistico' => 'required_with:profissionais|string|in:Avifauna,Herpetofauna,Mastofauna,Ictiofauna,Bentos',
            'modulos_amostrais' => 'nullable|array',
            'modulos_amostrais.*.data_cadastro' => 'nullable|date',
            'modulos_amostrais.*.tamanho_modulo' => 'nullable|in:1,2,3,4,5',
            'modulos_amostrais.*.uf' => 'nullable|string|size:2',
            'modulos_amostrais.*.municipio' => 'nullable|string|max:50',
            'modulos_amostrais.*.bioma' => 'nullable|string|max:30',
            'modulos_amostrais.*.fitofisionomia' => 'nullable|string',
            'modulos_amostrais.*.latitude_inicial' => 'nullable|numeric',
            'modulos_amostrais.*.longitude_inicial' => 'nullable|numeric',
            'modulos_amostrais.*.latitude_final' => 'nullable|numeric',
            'modulos_amostrais.*.longitude_final' => 'nullable|numeric',
            'modulos_amostrais.*.arquivo' => 'nullable|file|mimes:shp,zip|max:1024',
            'pontos_quelo_crocod' => 'nullable|array',
            'pontos_quelo_crocod.*.ponto_de_coleta' => 'required_without:nao_se_aplica|string',
            'pontos_quelo_crocod.*.nome_curso_hidrico' => 'required_without:nao_se_aplica|string',
            'pontos_quelo_crocod.*.latitude' => 'nullable|string',
            'pontos_quelo_crocod.*.longitude' => 'nullable|string',
            'pontos_quelo_crocod.*.bacia' => 'required_without:nao_se_aplica|string',
            'pontos_quelo_crocod.*.profundidade' => 'nullable|numeric',
            'pontos_quelo_crocod.*.largura' => 'required_without:nao_se_aplica|numeric',
            'pontos_quelo_crocod.*.tipo_substrato' => 'nullable|string',
            'pontos_cavernicola' => 'nullable|array',
            'pontos_cavernicola.*.cavidade' => 'required_without:nao_se_aplica|string',
            'pontos_cavernicola.*.latitude' => 'required_without:nao_se_aplica|numeric',
            'pontos_cavernicola.*.longitude' => 'required_without:nao_se_aplica|numeric',
            'pontos_cavernicola.*.distancia_eixo_rodovia' => 'required_without:nao_se_aplica|numeric',
            'pontos_cavernicola.*.formacao_associada' => 'required_without:nao_se_aplica|string',
            'pontos_cavernicola.*.temperatura_media_interna' => 'nullable|numeric',
            'pontos_cavernicola.*.temperatura_media_externa' => 'nullable|numeric',
            'pontos_cavernicola.*.umidade_relativa_interna' => 'nullable|numeric',
            'pontos_cavernicola.*.umidade_relativa_externa' => 'nullable|numeric',
            'metodologias' => 'nullable|array',
            'metodologias.*.grupo_faunistico' => 'nullable|string|in:Avifauna,Herpetofauna,Mastofauna,Ictiofauna,Bentos,Quelônios e Crocodilianos,Fauna Cavernícola,Invertebrados',
            'metodologias.*.metodologia' => 'required_with:metodologias|string',
            'consideracoes' => 'nullable|string',
            'planilha' => 'nullable|file|mimes:xlsx,xls|max:10240',
            'anexos.anuencia_proprietarios' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'anexos.registro_fotografico' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'anexos.dados_secundarios' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'anexos.art' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'anexos.ret' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'anexos.cr' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'anexos.ctf' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'anexos.anuencia_colecoes' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'anexos.oficio_atividades_campo' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'status' => 'required|string|in:Em análise,Aprovada,Rejeitada',
        ]);

        $validated['anexos'] = $request->file('anexos') ?? [];
        $validated['planilha'] = $request->file('planilha');

        try {
            DB::beginTransaction();

            $campanhaId = $this->faunaService->salvarCampanha($contrato, $validated);

            if (!empty($validated['id_abio'])) {
                foreach ($validated['id_abio'] as $abioId) {
                    SgcFaunaCampanhaAbios::create([
                        'contrato_id' => $contrato,
                        'campanha_id' => $campanhaId,
                        'n_abio' => $abioId,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('sgc.contratada.produtos.index', [$contrato, $produto])
                ->with('success', 'Campanha salva com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('FaunaController: Erro ao salvar campanha', [
                'contrato' => $contrato,
                'produto' => $produto,
                'erro' => $e->getMessage(),
                'linha' => $e->getLine(),
                'arquivo' => $e->getFile(),
            ]);
            return redirect()->back()->withErrors(['error' => 'Erro ao salvar campanha: ' . $e->getMessage()]);
        }
    }

    public function storeResultados(Request $request, $contrato, $produto): RedirectResponse
    {
        $validated = $request->validate([
            'planilha' => 'required|file|mimes:xlsx,xls|max:10240',
        ]);

        try {
            $campanha = SgcFaunaCampanha::where('id_contrato', $contrato)
                ->orderBy('created_at', 'desc')
                ->first();

            $campanhaId = $campanha ? $campanha->id : null;

            $result = $this->faunaService->salvarResultados($contrato, $validated['planilha'], $campanhaId);
            return redirect()->back()->with('success', $result['message']);
        } catch (\Exception $e) {
            Log::error('FaunaController: Erro ao salvar resultados', [
                'contrato' => $contrato,
                'produto' => $produto,
                'erro' => $e->getMessage(),
                'linha' => $e->getLine(),
                'arquivo' => $e->getFile(),
            ]);
            return redirect()->back()->withErrors(['error' => 'Erro ao salvar resultados: ' . $e->getMessage()]);
        }
    }

    public function show($contrato, $produto, $campanhaId)
    {
        $campanha = SgcFaunaCampanha::with([
            'abios.abio',
            'profissionais.profissional',
            'modulos_amostrais',
            'pontos_quelo_crocod',
            'pontos_cavernicola',
            'metodologias',
            'resultados',
            'resultados_consideracoes',
            'anexos'
        ])->findOrFail($campanhaId);

        // Log para depuração
        Log::debug('FaunaController: Dados da campanha', [
            'campanha_id' => $campanhaId,
            'resultados_count' => $campanha->resultados->count(),
            'resultados' => $campanha->resultados->toArray(),
            'anexos_count' => $campanha->anexos->count(),
            'anexos' => $campanha->anexos->toArray(),
        ]);

        if (!$campanha->relationLoaded('modulos_amostrais') || $campanha->modulos_amostrais === null) {
            $campanha->load('modulos_amostrais');
        }

        $modulosManuais = SgcFaunaModuloAmostral::where('campanha_id', $campanhaId)->get();

        $formModuloAmostral = $modulosManuais->isNotEmpty() ? $modulosManuais->map(function ($modulo) {
            return [
                'id' => $modulo->id,
                'data_cadastro' => $modulo->data_cadastro,
                'tamanho_modulo' => $modulo->tamanho_modulo,
                'uf' => $modulo->uf,
                'municipio' => $modulo->municipio,
                'bioma' => $modulo->bioma,
                'fitofisionomia' => $modulo->fitofisionomia,
                'latitude_inicial' => $modulo->latitude_inicial,
                'longitude_inicial' => $modulo->longitude_inicial,
                'latitude_final' => $modulo->latitude_final,
                'longitude_final' => $modulo->longitude_final,
                'arquivo' => $modulo->nome_arquivo,
                'obs' => $modulo->obs,
            ];
        })->toArray() : [];

        $formPontosAmostragem = $campanha->pontos_quelo_crocod->isNotEmpty() ? $campanha->pontos_quelo_crocod->map(function ($ponto) {
            return [
                'id' => $ponto->id,
                'ponto_de_coleta' => $ponto->ponto_de_coleta,
                'nome_curso_hidrico' => $ponto->nome_curso_hidrico,
                'latitude' => $ponto->latitude,
                'longitude' => $ponto->longitude,
                'bacia' => $ponto->bacia_hidrografica,
                'profundidade' => $ponto->profundidade,
                'largura' => $ponto->largura,
                'tipo_substrato' => $ponto->tipo_substrato,
            ];
        })->toArray() : [];

        $formPontosCavernicola = $campanha->pontos_cavernicola->isNotEmpty() ? $campanha->pontos_cavernicola->map(function ($ponto) {
            return [
                'id' => $ponto->id,
                'cavidade' => $ponto->cavidade,
                'latitude' => $ponto->latitude,
                'longitude' => $ponto->longitude,
                'distancia_eixo_rodovia' => $ponto->distancia_eixo_rodovia,
                'formacao_associada' => $ponto->formacao_associada,
                'temperatura_media_interna' => $ponto->temperatura_media_interna,
                'temperatura_media_externa' => $ponto->temperatura_media_externa,
                'umidade_relativa_interna' => $ponto->umidade_relativa_interna,
                'umidade_relativa_externa' => $ponto->umidade_relativa_externa,
            ];
        })->toArray() : [];

        $formMetodologia = $campanha->metodologias->isNotEmpty() ? $campanha->metodologias->map(function ($metodologia) {
            return [
                'id' => $metodologia->id,
                'grupo_faunistico' => $metodologia->grupo_faunistico,
                'metodologia' => $metodologia->metodologia,
            ];
        })->toArray() : [];

        $formResultados = $campanha->resultados->isNotEmpty() ? [
            'id_campanha' => $campanha->resultados->last()->id_campanha,
            'modulo' => $campanha->resultados->last()->modulo,
            'parcela' => $campanha->resultados->last()->parcela,
            'id_armadilha' => $campanha->resultados->last()->id_armadilha,
            'grupo_amostrado' => $campanha->resultados->last()->grupo_amostrado,
            'data_registro' => $campanha->resultados->last()->data_registro,
            'hora_registro' => $campanha->resultados->last()->hora_registro,
            'categoria' => $campanha->resultados->last()->categoria,
            'classe' => $campanha->resultados->last()->classe,
            'ordem' => $campanha->resultados->last()->ordem,
            'familia' => $campanha->resultados->last()->familia,
            'genero' => $campanha->resultados->last()->genero,
            'especie' => $campanha->resultados->last()->especie,
            'nome_comum' => $campanha->resultados->last()->nome_comum,
            'sexo' => $campanha->resultados->last()->sexo,
            'faixa_etaria' => $campanha->resultados->last()->faixa_etaria,
            'qnt_individuos' => $campanha->resultados->last()->qnt_individuos,
            'num_marcacao' => $campanha->resultados->last()->num_marcacao,
            'coletado' => $campanha->resultados->last()->coletado,
            'num_tombamento' => $campanha->resultados->last()->num_tombamento,
            'dados_biometricos' => $campanha->resultados->last()->dados_biometricos,
            'comp_total' => $campanha->resultados->last()->comp_total,
            'cabeca' => $campanha->resultados->last()->cabeca,
            'cauda' => $campanha->resultados->last()->cauda,
            'femur' => $campanha->resultados->last()->femur,
            'orelha' => $campanha->resultados->last()->orelha,
            'peso' => $campanha->resultados->last()->peso,
            'status_conservacao_federal' => $campanha->resultados->last()->status_conservacao_federal,
            'status_conservacao_iucn' => $campanha->resultados->last()->status_conservacao_iucn,
            'especies_bioindicadoras' => $campanha->resultados->last()->especies_bioindicadoras ?? null,
            'especies_alvo_monitoramento' => $campanha->resultados->last()->especies_alvo_monitoramento ?? null,

        ] : [
            'id_campanha' => null,
            'modulo' => null,
            'parcela' => null,
            'id_armadilha' => null,
            'grupo_amostrado' => null,
            'data_registro' => null,
            'hora_registro' => null,
            'categoria' => null,
            'classe' => null,
            'ordem' => null,
            'familia' => null,
            'genero' => null,
            'especie' => null,
            'nome_comum' => null,
            'sexo' => null,
            'faixa_etaria' => null,
            'qnt_individuos' => null,
            'num_marcacao' => null,
            'coletado' => null,
            'num_tombamento' => null,
            'dados_biometricos' => null,
            'comp_total' => null,
            'cabeca' => null,
            'cauda' => null,
            'femur' => null,
            'orelha' => null,
            'peso' => null,
            'status_conservacao_federal' => null,
            'status_conservacao_iucn' => null,
            'especies_bioindicadoras' => null,
            'especies_alvo_monitoramento' => null,
        ];

        $modulosAmostrais = $modulosManuais->map(function ($modulo) {
            return [
                'id' => $modulo->id,
                'data_cadastro' => $modulo->data_cadastro,
                'tamanho_modulo' => $modulo->tamanho_modulo,
                'uf' => $modulo->uf,
                'municipio' => $modulo->municipio,
                'bioma' => $modulo->bioma,
                'fitofisionomia' => $modulo->fitofisionomia,
                'latitude_inicial' => $modulo->latitude_inicial,
                'longitude_inicial' => $modulo->longitude_inicial,
                'latitude_final' => $modulo->latitude_final,
                'longitude_final' => $modulo->longitude_final,
                'obs' => $modulo->obs,
                'arquivo' => $modulo->nome_arquivo,
            ];
        })->toArray();

        return Inertia::render('Sgc/Contratada/Produtos/Fauna/VisualizarCampanha', [
            'campanha' => [
                'id' => $campanha->id,
                'id_campanha' => $campanha->id_campanha,
                'cod_emp' => $campanha->cod_emp,
                'familia' => $campanha->subproduto,
                'data_campanha_inicial' => $campanha->data_ini,
                'data_campanha_final' => $campanha->data_fim,
                'periodo' => $campanha->periodo,
                'observacoes' => $campanha->observacoes,
                'nao_se_aplica' => $campanha->nao_se_aplica ?? false,
                'status' => $campanha->status,
                'formModuloAmostral' => $formModuloAmostral,
                'formPontosAmostragem' => $formPontosAmostragem,
                'formPontosCavernicola' => $formPontosCavernicola,
                'formMetodologia' => $formMetodologia,
                'formResultados' => $formResultados,
                'consideracoes' => $campanha->resultados_consideracoes->consideracoes ?? null,
                'abios' => $campanha->abios->map(function ($abio) {
                    return [
                        'id' => $abio->n_abio,
                        'abio' => ['numero_licenca' => $abio->abio->numero_licenca ?? 'N/A'],
                    ];
                })->toArray(),
                'profissionais' => $campanha->profissionais->map(function ($prof) {
                    return [
                        'id' => $prof->id,
                        'profissional' => $prof->profissional->profissional ?? 'N/A',
                        'grupo_faunistico' => $prof->grupo_faunistico,
                        'formacao' => $prof->profissional->formacao ?? 'N/A',
                        'funcao' => $prof->profissional->funcao ?? 'N/A',
                        'ctf' => $prof->profissional->ctf ?? 'N/A',
                    ];
                })->toArray(),
                'modulos_amostrais' => $modulosAmostrais,
                'pontos_quelo_crocod' => $campanha->pontos_quelo_crocod->map(function ($ponto) {
                    return [
                        'id' => $ponto->id,
                        'ponto_de_coleta' => $ponto->ponto_de_coleta,
                        'nome_curso_hidrico' => $ponto->nome_curso_hidrico,
                        'latitude' => $ponto->latitude,
                        'longitude' => $ponto->longitude,
                        'bacia' => $ponto->bacia_hidrografica,
                        'profundidade' => $ponto->profundidade,
                        'largura' => $ponto->largura,
                        'tipo_substrato' => $ponto->tipo_substrato,
                    ];
                })->toArray(),
                'pontos_cavernicola' => $campanha->pontos_cavernicola->map(function ($ponto) {
                    return [
                        'id' => $ponto->id,
                        'cavidade' => $ponto->cavidade,
                        'latitude' => $ponto->latitude,
                        'longitude' => $ponto->longitude,
                        'distancia_eixo_rodovia' => $ponto->distancia_eixo_rodovia,
                        'formacao_associada' => $ponto->formacao_associada,
                        'temperatura_media_interna' => $ponto->temperatura_media_interna,
                        'temperatura_media_externa' => $ponto->temperatura_media_externa,
                        'umidade_relativa_interna' => $ponto->umidade_relativa_interna,
                        'umidade_relativa_externa' => $ponto->umidade_relativa_externa,
                    ];
                })->toArray(),
                'metodologias' => $campanha->metodologias->map(function ($metodologia) {
                    return [
                        'id' => $metodologia->id,
                        'grupo_faunistico' => $metodologia->grupo_faunistico,
                        'metodologia' => $metodologia->metodologia,
                    ];
                })->toArray(),
                'resultados' => $campanha->resultados->map(function ($resultado) {
                    return [
                        'id' => $resultado->id,
                        'modulo' => $resultado->modulo,
                        'parcela' => $resultado->parcela,
                        'id_armadilha' => $resultado->id_armadilha,
                        'grupo_amostrado' => $resultado->grupo_amostrado,
                        'data_registro' => $resultado->data_registro,
                        'hora_registro' => $resultado->hora_registro,
                        'categoria' => $resultado->categoria,
                        'classe' => $resultado->classe,
                        'ordem' => $resultado->ordem,
                        'familia' => $resultado->familia,
                        'genero' => $resultado->genero,
                        'especie' => $resultado->especie,
                        'nome_comum' => $resultado->nome_comum,
                        'sexo' => $resultado->sexo,
                        'faixa_etaria' => $resultado->faixa_etaria,
                        'qnt_individuos' => $resultado->qnt_individuos,
                        'num_marcacao' => $resultado->num_marcacao,
                        'coletado' => $resultado->coletado,
                        'num_tombamento' => $resultado->num_tombamento,
                        'dados_biometricos' => $resultado->dados_biometricos,
                        'comp_total' => $resultado->comp_total,
                        'cabeca' => $resultado->cabeca,
                        'cauda' => $resultado->cauda,
                        'femur' => $resultado->femur,
                        'orelha' => $resultado->orelha,
                        'peso' => $resultado->peso,
                        'status_conservacao_federal' => $resultado->status_conservacao_federal,
                        'status_conservacao_iucn' => $resultado->status_conservacao_iucn,
                        'especies_bioindicadoras' => $resultado->especies_bioindicadoras ?? null,
                        'especies_alvo_monitoramento' => $resultado->especies_alvo_monitoramento ?? null,
                    ];
                })->toArray(),
                'anexos' => $campanha->anexos->map(function ($anexo) {
                    return [
                        'id' => $anexo->id,
                        'tipo_anexo' => $anexo->tipo_anexo,
                        'caminho' => $anexo->caminho,
                        'nome_arquivo' => $anexo->nome_arquivo ?? basename($anexo->caminho),
                        'created_at' => $anexo->created_at,
                    ];
                })->toArray(),
            ],
            'contrato' => $campanha->id_contrato,
            'produto' => $campanha->subproduto,
            'contratos' => ['contratada' => 'Nome da Contratada', 'tipo_contrato' => 'Tipo'],
            'canApprove' => Auth::user()->perfis_id === 2 && $campanha->status === 'Em análise',
        ]);
    }

    public function salvarComentario(Request $request, $contrato, $produto, $campanha): RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->back()->withErrors(['error' => 'Acesso negado. Você precisa estar autenticado.']);
        }

        try {
            $validated = $request->validate([
                'analise_id' => 'required|integer|exists:sgc_fauna_analise_etapas,id',
                'etapa' => 'required|string|in:apresentacao_geral,caracterizacao_area,modulos_amostrais,pontos_quelo_crocod,pontos_cavernicola,metodologia,resultados,anexos',
                'comentario' => 'required|string|max:1000',
                'id_modulo' => 'nullable|integer',
            ]);

            $this->faunaService->salvarComentario($contrato, $campanha, $validated);
            return redirect()->route('sgc.contratada.produtos.edit', [$contrato, $produto, $campanha])
                ->with('success', 'Comentário salvo com sucesso!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('FaunaController: Erro de validação ao salvar comentário', [
                'contrato_id' => $contrato,
                'campanha_id' => $campanha,
                'errors' => $e->errors(),
            ]);
            return redirect()->back()->withErrors($e->errors());
        } catch (\Exception $e) {
            Log::error('FaunaController: Erro ao salvar comentário', [
                'contrato_id' => $contrato,
                'campanha_id' => $campanha,
                'erro' => $e->getMessage(),
            ]);
            return redirect()->back()->withErrors(['error' => 'Erro ao salvar comentário: ' . $e->getMessage()]);
        }
    }
    
    public function destroyComentario($contrato, $produto, $campanha, $comentarioId): RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->back()->withErrors(['error' => 'Acesso negado. Você precisa estar autenticado.']);
        }

        try {
            $this->faunaService->excluirComentario($contrato, $campanha, $comentarioId, Auth::id());
            return redirect()->route('sgc.contratada.produtos.edit', [$contrato, $produto, $campanha])
                ->with('success', 'Comentário excluído com sucesso!');
        } catch (\Exception $e) {
            Log::error('FaunaController: Erro ao excluir comentário', [
                'contrato_id' => $contrato,
                'campanha_id' => $campanha,
                'comentario_id' => $comentarioId,
                'erro' => $e->getMessage(),
            ]);
            return redirect()->back()->withErrors(['error' => 'Erro ao excluir comentário: ' . $e->getMessage()]);
        }
    }

    public function analise ($contrato, $produto, $campanha)
    {
        if (Auth::user()->perfis_id !== 2) {
            return redirect()->route('sgc.contratada.produtos.show', [$contrato, $produto, $campanha])
                ->withErrors(['error' => 'Acesso negado. Apenas fiscais podem analisar campanhas.']);
        }

        $campanhaObj = SgcFaunaCampanha::with([
            'abios.abio',
            'profissionais.profissional',
            'modulos_amostrais',
            'pontos_quelo_crocod',
            'pontos_cavernicola',
            'metodologias',
            'resultados',
            'resultados_consideracoes',
            'anexos',
        ])->findOrFail($campanha);

        if ($campanhaObj->status !== 'Em análise') {
            return redirect()->route('sgc.contratada.produtos.show', [$contrato, $produto, $campanha])
                ->withErrors(['error' => 'Campanha não está em análise.']);
        }

        $analises = $this->faunaFiscalService->getAnalisesByCampanha($contrato, $campanha);
        $comentarios = $this->faunaService->getComentariosByCampanha($contrato, $campanha);

        // Verificar se a relação modulos_amostrais está carregada e é uma coleção
        $formModuloAmostral = ($campanhaObj->relationLoaded('modulos_amostrais') && $campanhaObj->modulos_amostrais && $campanhaObj->modulos_amostrais->isNotEmpty()) ? 
            $campanhaObj->modulos_amostrais->map(function ($modulo) {
                return [
                    'id' => $modulo->id,
                    'data_cadastro' => $modulo->data_cadastro,
                    'tamanho_modulo' => $modulo->tamanho_modulo,
                    'uf' => $modulo->uf,
                    'municipio' => $modulo->municipio,
                    'bioma' => $modulo->bioma,
                    'fitofisionomia' => $modulo->fitofisionomia,
                    'latitude_inicial' => $modulo->latitude_inicial,
                    'longitude_inicial' => $modulo->longitude_inicial,
                    'latitude_final' => $modulo->latitude_final,
                    'longitude_final' => $modulo->longitude_final,
                    'arquivo' => $modulo->nome_arquivo,
                    'obs' => $modulo->obs,
                ];
            })->toArray() : [];

        // Verificar se a relação pontos_quelo_crocod está carregada e é uma coleção
        $formPontosAmostragem = ($campanhaObj->relationLoaded('pontos_quelo_crocod') && $campanhaObj->pontos_quelo_crocod && $campanhaObj->pontos_quelo_crocod->isNotEmpty()) ? 
            $campanhaObj->pontos_quelo_crocod->map(function ($ponto) {
                return [
                    'id' => $ponto->id,
                    'ponto_de_coleta' => $ponto->ponto_de_coleta,
                    'nome_curso_hidrico' => $ponto->nome_curso_hidrico,
                    // 'coordenadas' => $ponto->coordenadas,
                    'latitude' => $ponto->latitude,
                    'longitude' => $ponto->longitude,
                    'bacia' => $ponto->bacia_hidrografica,
                    'profundidade' => $ponto->profundidade,
                    'largura' => $ponto->largura,
                    'tipo_substrato' => $ponto->tipo_substrato,
                ];
            })->toArray() : [];

        $formPontosCavernicola = ($campanhaObj->relationLoaded('pontos_cavernicola') && $campanhaObj->pontos_cavernicola && $campanhaObj->pontos_cavernicola->isNotEmpty()) ? 
            $campanhaObj->pontos_cavernicola->map(function ($ponto) {
                return [
                    'id' => $ponto->id,
                    'cavidade' => $ponto->cavidade,
                    'latitude' => $ponto->latitude,
                    'longitude' => $ponto->longitude,
                    'distancia_eixo_rodovia' => $ponto->distancia_eixo_rodovia,
                    'formacao_associada' => $ponto->formacao_associada,
                    'temperatura_media_interna' => $ponto->temperatura_media_interna,
                    'temperatura_media_externa' => $ponto->temperatura_media_externa,
                    'umidade_relativa_interna' => $ponto->umidade_relativa_interna,
                    'umidade_relativa_externa' => $ponto->umidade_relativa_externa,
                ];
            })->toArray() : [];

        // Verificar se a relação metodologias está carregada e é uma coleção
        $formMetodologia = ($campanhaObj->relationLoaded('metodologias') && $campanhaObj->metodologias && $campanhaObj->metodologias->isNotEmpty()) ? 
            $campanhaObj->metodologias->map(function ($metodologia) {
                return [
                    'id' => $metodologia->id,
                    'grupo_faunistico' => $metodologia->grupo_faunistico,
                    'metodologia' => $metodologia->metodologia,
                ];
            })->toArray() : [];

        // Verificar se a relação resultados está carregada e é uma coleção
        $formResultados = ($campanhaObj->relationLoaded('resultados') && $campanhaObj->resultados && $campanhaObj->resultados->isNotEmpty()) ? [
            'id_campanha' => $campanhaObj->resultados->last()->id_campanha,
            'modulo' => $campanhaObj->resultados->last()->modulo,
            'parcela' => $campanhaObj->resultados->last()->parcela,
            'id_armadilha' => $campanhaObj->resultados->last()->id_armadilha,
            'grupo_amostrado' => $campanhaObj->resultados->last()->grupo_amostrado,
            'data_registro' => $campanhaObj->resultados->last()->data_registro,
            'hora_registro' => $campanhaObj->resultados->last()->hora_registro,
            'categoria' => $campanhaObj->resultados->last()->categoria,
            'classe' => $campanhaObj->resultados->last()->classe,
            'ordem' => $campanhaObj->resultados->last()->ordem,
            'familia' => $campanhaObj->resultados->last()->familia,
            'genero' => $campanhaObj->resultados->last()->genero,
            'especie' => $campanhaObj->resultados->last()->especie,
            'nome_comum' => $campanhaObj->resultados->last()->nome_comum,
            'sexo' => $campanhaObj->resultados->last()->sexo,
            'faixa_etaria' => $campanhaObj->resultados->last()->faixa_etaria,
            'qnt_individuos' => $campanhaObj->resultados->last()->qnt_individuos,
            'num_marcacao' => $campanhaObj->resultados->last()->num_marcacao,
            'coletado' => $campanhaObj->resultados->last()->coletado,
            'num_tombamento' => $campanhaObj->resultados->last()->num_tombamento,
            'dados_biometricos' => $campanhaObj->resultados->last()->dados_biometricos,
            'comp_total' => $campanhaObj->resultados->last()->comp_total,
            'cabeca' => $campanhaObj->resultados->last()->cabeca,
            'cauda' => $campanhaObj->resultados->last()->cauda,
            'femur' => $campanhaObj->resultados->last()->femur,
            'orelha' => $campanhaObj->resultados->last()->orelha,
            'peso' => $campanhaObj->resultados->last()->peso,
            'status_conservacao_federal' => $campanhaObj->resultados->last()->status_conservacao_federal,
            'status_conservacao_iucn' => $campanhaObj->resultados->last()->status_conservacao_iucn,
            'especies_bioindicadoras' => $campanhaObj->resultados->last()->especies_bioindicadoras ?? null,
            'especies_alvo_monitoramento' => $campanhaObj->resultados->last()->especies_alvo_monitoramento ?? null,
        ] : [
            'id_campanha' => null,
            'modulo' => null,
            'parcela' => null,
            'id_armadilha' => null,
            'grupo_amostrado' => null,
            'data_registro' => null,
            'hora_registro' => null,
            'categoria' => null,
            'classe' => null,
            'ordem' => null,
            'familia' => null,
            'genero' => null,
            'especie' => null,
            'nome_comum' => null,
            'sexo' => null,
            'faixa_etaria' => null,
            'qnt_individuos' => null,
            'num_marcacao' => null,
            'coletado' => null,
            'num_tombamento' => null,
            'dados_biometricos' => null,
            'comp_total' => null,
            'cabeca' => null,
            'cauda' => null,
            'femur' => null,
            'orelha' => null,
            'peso' => null,
            'status_conservacao_federal' => null,
            'status_conservacao_iucn' => null,
            'especies_bioindicadoras' => null,
            'especies_alvo_monitoramento' => null,
        ];

        return Inertia::render('Sgc/Contratada/Produtos/Fauna/AnaliseCampanha', [
            'campanha' => [
                'id' => $campanhaObj->id,
                'id_campanha' => $campanhaObj->id_campanha,
                'cod_emp' => $campanhaObj->cod_emp,
                'familia' => $campanhaObj->subproduto,
                'data_campanha_inicial' => $campanhaObj->data_ini,
                'data_campanha_final' => $campanhaObj->data_fim,
                'periodo' => $campanhaObj->periodo,
                'observacoes' => $campanhaObj->observacoes,
                'nao_se_aplica' => $campanhaObj->nao_se_aplica ?? false,
                'status' => $campanhaObj->status,
                'formModuloAmostral' => $formModuloAmostral,
                'formPontosAmostragem' => $formPontosAmostragem,
                'formPontosCavernicola' => $formPontosCavernicola,
                'formMetodologia' => $formMetodologia,
                'formResultados' => $formResultados,
                'consideracoes' => $campanhaObj->resultados_consideracoes->consideracoes ?? null,
                'abios' => $campanhaObj->abios->map(function ($abio) {
                    return [
                        'id' => $abio->n_abio,
                        'abio' => ['numero_licenca' => $abio->abio->numero_licenca ?? 'N/A'],
                    ];
                })->toArray(),
                'profissionais' => $campanhaObj->profissionais->map(function ($prof) {
                    return [
                        'id' => $prof->id,
                        'profissional' => $prof->profissional->profissional ?? 'N/A',
                        'grupo_faunistico' => $prof->grupo_faunistico,
                        'formacao' => $prof->profissional->formacao ?? 'N/A',
                        'funcao' => $prof->profissional->funcao ?? 'N/A',
                        'ctf' => $prof->profissional->ctf ?? 'N/A',
                    ];
                })->toArray(),
                'modulos_amostrais' => ($campanhaObj->relationLoaded('modulos_amostrais') && $campanhaObj->modulos_amostrais) ? $campanhaObj->modulos_amostrais->map(function ($modulo) {
                    return [
                        'id' => $modulo->id,
                        'data_cadastro' => $modulo->data_cadastro,
                        'tamanho_modulo' => $modulo->tamanho_modulo,
                        'uf' => $modulo->uf,
                        'municipio' => $modulo->municipio,
                        'bioma' => $modulo->bioma,
                        'fitofisionomia' => $modulo->fitofisionomia,
                        'latitude_inicial' => $modulo->latitude_inicial,
                        'longitude_inicial' => $modulo->longitude_inicial,
                        'latitude_final' => $modulo->latitude_final,
                        'longitude_final' => $modulo->longitude_final,
                        'obs' => $modulo->obs,
                        'arquivo' => $modulo->nome_arquivo,
                    ];
                })->toArray() : [],
                'pontos_quelo_crocod' => ($campanhaObj->relationLoaded('pontos_quelo_crocod') && $campanhaObj->pontos_quelo_crocod) ? $campanhaObj->pontos_quelo_crocod->map(function ($ponto) {
                    return [
                        'id' => $ponto->id,
                        'ponto_de_coleta' => $ponto->ponto_de_coleta,
                        'nome_curso_hidrico' => $ponto->nome_curso_hidrico,
                        'latitude' => $ponto->latitude,
                        'longitude' => $ponto->longitude,
                        'bacia' => $ponto->bacia_hidrografica,
                        'profundidade' => $ponto->profundidade,
                        'largura' => $ponto->largura,
                        'tipo_substrato' => $ponto->tipo_substrato,
                    ];
                })->toArray() : [],
                'pontos_cavernicola' => ($campanhaObj->relationLoaded('pontos_cavernicola') && $campanhaObj->pontos_cavernicola) ? $campanhaObj->pontos_cavernicola->map(function ($ponto) {
                    return [
                        'id' => $ponto->id,
                        'cavidade' => $ponto->cavidade,
                        'latitude' => $ponto->latitude,
                        'longitude' => $ponto->longitude,
                        'distancia_eixo_rodovia' => $ponto->distancia_eixo_rodovia,
                        'formacao_associada' => $ponto->formacao_associada,
                        'temperatura_media_interna' => $ponto->temperatura_media_interna,
                        'temperatura_media_externa' => $ponto->temperatura_media_externa,
                        'umidade_relativa_interna' => $ponto->umidade_relativa_interna,
                        'umidade_relativa_externa' => $ponto->umidade_relativa_externa,
                    ];
                })->toArray() : [],
                'metodologias' => ($campanhaObj->relationLoaded('metodologias') && $campanhaObj->metodologias) ? $campanhaObj->metodologias->map(function ($metodologia) {
                    return [
                        'id' => $metodologia->id,
                        'grupo_faunistico' => $metodologia->grupo_faunistico,
                        'metodologia' => $metodologia->metodologia,
                    ];
                })->toArray() : [],
                'resultados' => ($campanhaObj->relationLoaded('resultados') && $campanhaObj->resultados) ? $campanhaObj->resultados->map(function ($resultado) {
                    return [
                        'id' => $resultado->id,
                        'modulo' => $resultado->modulo,
                        'parcela' => $resultado->parcela,
                        'id_armadilha' => $resultado->id_armadilha,
                        'grupo_amostrado' => $resultado->grupo_amostrado,
                        'data_registro' => $resultado->data_registro,
                        'hora_registro' => $resultado->hora_registro,
                        'categoria' => $resultado->categoria,
                        'classe' => $resultado->classe,
                        'ordem' => $resultado->ordem,
                        'familia' => $resultado->familia,
                        'genero' => $resultado->genero,
                        'especie' => $resultado->especie,
                        'nome_comum' => $resultado->nome_comum,
                        'sexo' => $resultado->sexo,
                        'faixa_etaria' => $resultado->faixa_etaria,
                        'qnt_individuos' => $resultado->qnt_individuos,
                        'num_marcacao' => $resultado->num_marcacao,
                        'coletado' => $resultado->coletado,
                        'num_tombamento' => $resultado->num_tombamento,
                        'dados_biometricos' => $resultado->dados_biometricos,
                        'comp_total' => $resultado->comp_total,
                        'cabeca' => $resultado->cabeca,
                        'cauda' => $resultado->cauda,
                        'femur' => $resultado->femur,
                        'orelha' => $resultado->orelha,
                        'peso' => $resultado->peso,
                        'status_conservacao_federal' => $resultado->status_conservacao_federal,
                        'status_conservacao_iucn' => $resultado->status_conservacao_iucn,
                        'especies_bioindicadoras' => $resultado->especies_bioindicadoras ?? null,
                        'especies_alvo_monitoramento' => $resultado->especies_alvo_monitoramento ?? null,
                    ];
                })->toArray() : [],
                'anexos' => ($campanhaObj->relationLoaded('anexos') && $campanhaObj->anexos) ? $campanhaObj->anexos->map(function ($anexo) {
                    return [
                        'id' => $anexo->id,
                        'tipo_anexo' => $anexo->tipo_anexo,
                        'caminho' => $anexo->caminho,
                        'nome_arquivo' => $anexo->nome_arquivo ?? basename($anexo->caminho),
                        'created_at' => $anexo->created_at,
                    ];
                })->toArray() : [],
            ],
            'contrato' => $campanhaObj->id_contrato,
            'produto' => $campanhaObj->subproduto,
            'contratos' => ['contratada' => 'Nome da Contratada', 'tipo_contrato' => 'Tipo'],
            'canApprove' => Auth::user()->perfis_id === 2 && $campanhaObj->status === 'Em análise',
            'analises' => $analises ?? [],
            'comentarios' => $comentarios ?? [],
        ]);
    }

    public function salvarAnalise(Request $request, $contrato, $produto, $campanha): RedirectResponse
    {
        if (Auth::user()->perfis_id !== 2) {
            return redirect()->back()->withErrors(['error' => 'Acesso negado. Apenas fiscais podem salvar análises.']);
        }

        \Log::info('Dados recebidos em salvarAnalise:', [
            'request' => $request->all(),
            'contrato' => $contrato,
            'produto' => $produto,
            'campanha' => $campanha,
        ]);

        try {
            $validated = $request->validate([
                'etapa' => 'required|string|in:apresentacao_geral,caracterizacao_area,modulos_amostrais,pontos_quelo_crocod,pontos_cavernicola,metodologia,resultados,anexos',
                'status' => 'required|string|in:Aprovada,Rejeitada',
                'observacoes' => 'nullable|string',
            ]);

            // Verificar se há análises anteriores rejeitadas para a mesma etapa
            $hasPreviousRejection = SgcFaunaAnaliseEtapa::where('id_contrato', $contrato)
                ->where('id_campanha', $campanha)
                ->where('etapa', $validated['etapa'])
                ->where('fiscal_id', Auth::id())
                ->where('status', 'Rejeitada')
                ->exists();

            // Adicionar nova_analise se houver rejeição anterior
            if ($hasPreviousRejection) {
                $validated['nova_analise'] = true;
            }

            $this->faunaFiscalService->salvarAnaliseEtapa($contrato, $campanha, $validated);
            return redirect()->route('sgc.contratada.produtos.analise', [$contrato, $produto, $campanha])
                ->with('success', 'Análise da etapa salva com sucesso!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('FaunaController: Erro de validação ao salvar análise', [
                'contrato_id' => $contrato,
                'campanha_id' => $campanha,
                'errors' => $e->errors(),
            ]);
            return redirect()->back()->withErrors($e->errors());
        } catch (\Exception $e) {
            \Log::error('FaunaController: Erro ao salvar análise da etapa', [
                'contrato_id' => $contrato,
                'campanha_id' => $campanha,
                'erro' => $e->getMessage(),
            ]);
            return redirect()->back()->withErrors(['error' => 'Erro ao salvar análise: ' . $e->getMessage()]);
        }
    }

    public function finalizarAvaliacao($contrato, $produto, $campanha, Request $request)
    {
        $service = new FaunaFiscalService();
        try {
            $service->finalizarAvaliacaoCampanha($contrato, $campanha);
            return Inertia::location(route('sgc.contratada.produtos.index', [$contrato, $produto]));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function edit(Request $request, $contrato, $produto, $campanhaId)
    {
        $campanha = SgcFaunaCampanha::with([
            'abios' => fn($query) => $query->with([
                'abio' => fn($q) => $q->select('id', 'numero_licenca')
            ]),
            'profissionais' => fn($query) => $query->with(['profissional' => fn($q) => $q->select('id', 'profissional', 'formacao')]),
            'modulos_amostrais',
            'pontos_quelo_crocod',
            'pontos_cavernicola',
            'metodologias',
            'analises',
            'anexos',
            'resultados', // Garante que a relação está carregada
            'resultados_consideracoes',
        ])->findOrFail($campanhaId);

        $subproduto = $request->query('subproduto');

        $empreendimentos = SgcvwEmpreendimentos::where('contrato_id', $contrato)
            ->pluck('cod_emp')
            ->toArray();

        $abios = $this->produtosService->getAbios();
        $profissionais = $this->faunaService->getProfissionaisByContrato($contrato);

        // Lista de UFs brasileiras
        $ufs = [
            ['uf' => 'AC'], ['uf' => 'AL'], ['uf' => 'AP'], ['uf' => 'AM'], ['uf' => 'BA'],
            ['uf' => 'CE'], ['uf' => 'DF'], ['uf' => 'ES'], ['uf' => 'GO'], ['uf' => 'MA'],
            ['uf' => 'MT'], ['uf' => 'MS'], ['uf' => 'MG'], ['uf' => 'PA'], ['uf' => 'PB'],
            ['uf' => 'PR'], ['uf' => 'PE'], ['uf' => 'PI'], ['uf' => 'RJ'], ['uf' => 'RN'],
            ['uf' => 'RS'], ['uf' => 'RO'], ['uf' => 'RR'], ['uf' => 'SC'], ['uf' => 'SP'],
            ['uf' => 'SE'], ['uf' => 'TO']
        ];

        // Lista de biomas brasileiros
        $biomas = [
            'Amazônia', 'Caatinga', 'Cerrado', 'Mata Atlântica', 'Pampa', 'Pantanal'
        ];

        // Mapeamento dos resultados
        $resultados = $campanha->resultados->map(function ($resultado) {
            return [
                'id' => $resultado->id,
                'id_campanha' => $resultado->id_campanha,
                'modulo' => $resultado->modulo,
                'parcela' => $resultado->parcela,
                'id_armadilha' => $resultado->id_armadilha,
                'grupo_amostrado' => $resultado->grupo_amostrado,
                'data_registro' => $resultado->data_registro,
                'hora_registro' => $resultado->hora_registro,
                'categoria' => $resultado->categoria,
                'classe' => $resultado->classe,
                'ordem' => $resultado->ordem,
                'familia' => $resultado->familia,
                'genero' => $resultado->genero,
                'especie' => $resultado->especie,
                'nome_comum' => $resultado->nome_comum,
                'sexo' => $resultado->sexo,
                'faixa_etaria' => $resultado->faixa_etaria,
                'qnt_individuos' => $resultado->qnt_individuos,
                'num_marcacao' => $resultado->num_marcacao,
                'coletado' => $resultado->coletado,
                'num_tombamento' => $resultado->num_tombamento,
                'dados_biometricos' => $resultado->dados_biometricos,
                'comp_total' => $resultado->comp_total,
                'cabeca' => $resultado->cabeca,
                'cauda' => $resultado->cauda,
                'femur' => $resultado->femur,
                'orelha' => $resultado->orelha,
                'peso' => $resultado->peso,
                'status_conservacao_federal' => $resultado->status_conservacao_federal,
                'status_conservacao_iucn' => $resultado->status_conservacao_iucn,
                'especies_bioindicadoras' => $resultado->especies_bioindicadoras ?? null,
                'especies_alvo_monitoramento' => $resultado->especies_alvo_monitoramento ?? null,
            ];
        })->toArray();


        if ($campanha->status !== 'Rejeitada') {
            return redirect()->route('sgc.contratada.produtos.show', [$contrato, $produto, $campanhaId])
                ->withErrors(['error' => 'Apenas campanhas rejeitadas podem ser editadas.']);
        }

        if (Auth::user()->perfis_id === 2) {
            return redirect()->route('sgc.contratada.produtos.show', [$contrato, $produto, $campanhaId])
                ->withErrors(['error' => 'Acesso negado. Fiscais não podem editar campanhas.']);
        }

        // Carregar comentários associados à campanha
        $comentarios = SgcFaunaComentarios::where('campanha_id', $campanhaId)
        ->whereNull('deleted_at')
        ->get();

        return Inertia::render('Sgc/Contratada/Produtos/Fauna/EditarCampanha', [
            'campanha' => array_merge($campanha->toArray(), [
            'resultados' => $resultados,
            'resultados_consideracoes' => $campanha->resultados_consideracoes->consideracoes ?? null,
        ]),
            'contrato' => $contrato,
            'produto' => $produto,
            'abios' => $abios,
            'empreendimentos' => $empreendimentos,
            'subproduto' => $subproduto,
            'profissionais' => $profissionais,
            'ufs' => $ufs,
            'biomas' => $biomas,
            'contratos' => [
                'tipo_contrato' => $campanha->tipo_contrato,
                'contratada' => $campanha->contratada,
            ],
            'comentarios' => $comentarios, 
        ]);
    }

    public function update(Request $request, $contrato, $produto, $campanhaId): RedirectResponse
    {
        Log::info('FaunaController: Requisição recebida em update', [
            'contrato' => $contrato,
            'campanha_id' => $campanhaId,
            'request_all' => $request->all(),
            'profissionais' => $request->input('profissionais'),
            'id_abio' => $request->input('id_abio'),
        ]);

        if (Auth::user()->perfis_id === 2) {
            return redirect()->route('sgc.contratada.produtos.show', [$contrato, $produto, $campanhaId])
                ->withErrors(['error' => 'Acesso negado. Fiscais não podem atualizar campanhas.']);
        }

        $campanha = SgcFaunaCampanha::findOrFail($campanhaId);
        if ($campanha->status !== 'Rejeitada') {
            return redirect()->route('sgc.contratada.produtos.show', [$contrato, $produto, $campanhaId])
                ->withErrors(['error' => 'Apenas campanhas rejeitadas podem ser atualizadas.']);
        }

        try {
            $validated = $request->validate([
                'data_campanha_inicial' => 'nullable|date',
                'data_campanha_final' => 'nullable|date|after_or_equal:data_campanha_inicial',
                'periodo' => 'nullable|string|max:255',
                'observacoes' => 'nullable|string',
                'cod_emp' => 'required|string|max:255',
                'subproduto' => 'required|string|max:255',
                'nao_se_aplica' => 'nullable|boolean',
                'abios' => 'nullable|array',
                'profissionais' => 'nullable|array',
                'profissionais.*.grupo_faunistico' => 'nullable|string|in:Avifauna,Herpetofauna,Mastofauna,Ictiofauna,Bentos',
                'profissionais.*.profissional.formacao' => 'nullable|string|max:255',
                'modulos_amostrais' => 'nullable|array',
                'modulos_amostrais.*.id' => 'nullable|integer|exists:sgc_fauna_modulos_amostrais,id',
                'modulos_amostrais.*.data_cadastro' => 'nullable|date',
                'modulos_amostrais.*.tamanho_modulo' => 'nullable|int|max:255',
                'modulos_amostrais.*.uf' => 'nullable|string|max:2',
                'modulos_amostrais.*.municipio' => 'nullable|string|max:255',
                'modulos_amostrais.*.bioma' => 'nullable|string|max:255',
                'modulos_amostrais.*.fitofisionomia' => 'nullable|string',
                'modulos_amostrais.*.latitude_inicial' => 'nullable|numeric|between:-90,90',
                'modulos_amostrais.*.longitude_inicial' => 'nullable|numeric|between:-180,180',
                'modulos_amostrais.*.latitude_final' => 'nullable|numeric|between:-90,90',
                'modulos_amostrais.*.longitude_final' => 'nullable|numeric|between:-180,180',
                'modulos_amostrais.*.obs' => 'nullable|string',
                'modulos_amostrais.*.arquivo' => 'nullable|file|mimes:shp,zip|max:20480',
                'pontos_quelo_crocod' => 'nullable|array',
                'pontos_quelo_crocod.*.ponto_de_coleta' => 'nullable|string',
                'pontos_quelo_crocod.*.nome_curso_hidrico' => 'nullable|string',
                'pontos_quelo_crocod.*.latitude' => 'nullable|string',
                'pontos_quelo_crocod.*.longitude' => 'nullable|string',
                'pontos_quelo_crocod.*.profundidade' => 'nullable|numeric',
                'pontos_quelo_crocod.*.largura' => 'nullable|numeric',
                'pontos_quelo_crocod.*.tipo_substrato' => 'nullable|string',
                'pontos_cavernicola' => 'nullable|array',
                'pontos_cavernicola.*.cavidade' => 'nullable|string',
                'pontos_cavernicola.*.latitude' => 'nullable|numeric',
                'pontos_cavernicola.*.longitude' => 'nullable|numeric',
                'pontos_cavernicola.*.distancia_eixo_rodovia' => 'nullable|numeric',
                'pontos_cavernicola.*.formacao_associada' => 'nullable|string',
                'pontos_cavernicola.*.temperatura_media_interna' => 'nullable|numeric',
                'pontos_cavernicola.*.temperatura_media_externa' => 'nullable|numeric',
                'pontos_cavernicola.*.umidade_relativa_interna' => 'nullable|numeric',
                'pontos_cavernicola.*.umidade_relativa_externa' => 'nullable|numeric',
                'metodologias' => 'nullable|array',
                'metodologias.*.grupo_faunistico' => 'nullable|string|in:Avifauna,Herpetofauna,Mastofauna,Ictiofauna,Bentos,Quelônios e Crocodilianos,Fauna Cavernícola,Invertebrados',
                'metodologias.*.metodologia' => 'nullable|string',
                'consideracoes' => 'nullable|string',
                'planilha' => 'nullable|file|mimes:xlsx,xls|max:10240',
                'anexos.anuencia_proprietarios' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
                'anexos.registro_fotografico' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
                'anexos.dados_secundarios' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
                'anexos.art' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
                'anexos.ret' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
                'anexos.cr' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
                'anexos.ctf' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
                'anexos.anuencia_colecoes' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
                'anexos.oficio_atividades_campo' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            ]);

            // Log detalhado antes do parsing
            Log::info('FaunaController: Antes do parsing', [
                'id_abio_raw' => $request->input('id_abio'),
                'profissionais_raw' => $request->input('profissionais'),
                'abios_raw' => $request->input('abios'),
            ]);

            // Forçar parsing correto de abios
            $validated['id_abio'] = array_map(function ($abio) {
                Log::info('FaunaController: Processando abio', [
                    'abio_raw' => $abio,
                    'id_abio' => isset($abio['abio']['id']) ? (int) $abio['abio']['id'] : null,
                ]);
                // Usar o ID da licença recebido diretamente do frontend
                return isset($abio['abio']['id']) ? (int) $abio['abio']['id'] : null;
            }, (array) $request->input('abios', []));

            // Remover valores nulos
            $validated['id_abio'] = array_filter($validated['id_abio'], fn($id) => !is_null($id));

            // Forçar parsing correto de profissionais
            $validated['profissionais'] = array_map(function ($profissional) {
                Log::info('FaunaController: Processando profissional', [
                    'profissional_raw' => $profissional,
                    'id_profissional' => isset($profissional['profissional']['id']) ? (int) $profissional['profissional']['id'] : null,
                    'formacao' => isset($profissional['profissional']['formacao']) ? $profissional['profissional']['formacao'] : null,
                ]);
                return [
                    'id_profissional' => isset($profissional['profissional']['id']) ? (int) $profissional['profissional']['id'] : null,
                    'grupo_faunistico' => $profissional['grupo_faunistico'] ?? null,
                    'formacao' => isset($profissional['profissional']['formacao']) ? $profissional['profissional']['formacao'] : null,
                ];
            }, (array) $request->input('profissionais', []));

            // Log após o parsing
            Log::info('FaunaController: Após o parsing', [
                'id_abio' => $validated['id_abio'],
                'profissionais' => $validated['profissionais'],
            ]);

            $validated['anexos'] = $request->file('anexos') ?? [];
            $validated['planilha'] = $request->file('planilha');

            DB::beginTransaction();
            $campanhaId = $this->faunaService->atualizarCampanha($contrato, $campanhaId, $validated);
            DB::commit();
            Log::info('FaunaController: Campanha atualizada com sucesso', [
                'contrato' => $contrato,
                'produto' => $produto,
                'campanha_id' => $campanhaId,
            ]);
            return redirect()->route('sgc.contratada.produtos.index', [$contrato, $produto])
                ->with('success', 'Campanha atualizada com sucesso!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('FaunaController: Erro de validação', [
                'contrato' => $contrato,
                'campanha_id' => $campanhaId,
                'errors' => $e->errors(),
            ]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('FaunaController: Erro ao atualizar campanha', [
                'contrato' => $contrato,
                'produto' => $produto,
                'campanha_id' => $campanhaId,
                'erro' => $e->getMessage(),
                'linha' => $e->getLine(),
                'arquivo' => $e->getFile(),
            ]);
            return redirect()->back()->withErrors(['error' => 'Erro ao atualizar campanha: ' . $e->getMessage()]);
        }

        Log::info('FaunaController: Planilha recebida em update', [
            'nome' => $request->hasFile('planilha') ? $request->file('planilha')->getClientOriginalName() : 'Nenhuma',
        ]);
    }



}