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
use App\Models\SgcFaunaProfissionais;
use App\Models\SgcFaunaAnexo;
use App\Models\Contrato;
use App\Domain\Sgc\Contratada\Produtos\Services\ProdutosService;
use Inertia\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

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

    public function updatePartial(Request $request, $contrato, $produto, $campanhaId): RedirectResponse
    {
        $campanha = SgcFaunaCampanha::findOrFail($campanhaId);
        if ($campanha->status !== 'Em elaboração') {
            return redirect()->back()->withErrors(['error' => 'Apenas campanhas em elaboração podem ser atualizadas parciais.']);
        }

        $validated = $request->validate([
            'aba' => 'required|string|in:apresentacao,metodologia,resultados,anexos',
            'data' => 'required|array',
        ]);

        $this->faunaService->atualizarParcialCampanha($campanha, $validated['aba'], $validated['data']);

        return redirect()->back()->with('success', 'Dados da aba salvos com sucesso!');
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
            'nao_se_aplica_quelo' => 'nullable|boolean',
            'nao_se_aplica_cavernicola' => 'nullable|boolean',
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
            'pontos_quelo_crocod.*.ponto_de_coleta' => 'required_without:nao_se_aplica_quelo|string',
            'pontos_quelo_crocod.*.nome_curso_hidrico' => 'required_without:nao_se_aplica_quelo|string',
            'pontos_quelo_crocod.*.latitude' => 'nullable|string',
            'pontos_quelo_crocod.*.longitude' => 'nullable|string',
            'pontos_quelo_crocod.*.bacia' => 'required_without:nao_se_aplica_quelo|string',
            'pontos_quelo_crocod.*.profundidade' => 'nullable|numeric',
            'pontos_quelo_crocod.*.largura' => 'required_without:nao_se_aplica_quelo|numeric',
            'pontos_quelo_crocod.*.tipo_substrato' => 'nullable|string',
            'pontos_cavernicola' => 'nullable|array',
            'pontos_cavernicola.*.cavidade' => 'required_without:nao_se_aplica_cavernicola|string',
            'pontos_cavernicola.*.latitude' => 'required_without:nao_se_aplica_cavernicola|numeric',
            'pontos_cavernicola.*.longitude' => 'required_without:nao_se_aplica_cavernicola|numeric',
            'pontos_cavernicola.*.distancia_eixo_rodovia' => 'required_without:nao_se_aplica_cavernicola|numeric',
            'pontos_cavernicola.*.formacao_associada' => 'required_without:nao_se_aplica_cavernicola|string',
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

            // Ajustar estrutura de profissionais para compatibilidade com atualizarCampanha
            if (!empty($validated['profissionais'])) {
                $validated['profissionais'] = array_map(function ($profissional) use ($contrato) {
                    $profissionalModel = SgcFaunaProfissionais::where('id_contrato', $contrato)
                        ->where('profissional', $profissional['profissional'])
                        ->first();
                    return [
                        'id_profissional' => $profissionalModel ? $profissionalModel->id : null,
                        'grupo_faunistico' => $profissional['grupo_faunistico'] ?? null,
                        'formacao' => $profissionalModel ? $profissionalModel->formacao : null,
                    ];
                }, $validated['profissionais']);
            }

            $campanhaId = $request->input('id_campanha');

            if ($campanhaId) {
                // Verificar se a campanha existe e está em elaboração
                $campanha = SgcFaunaCampanha::findOrFail($campanhaId);
                if ($campanha->status !== 'Em elaboração') {
                    return redirect()->back()->withErrors(['error' => 'A campanha não está em elaboração e não pode ser atualizada.']);
                }
                $campanhaId = $this->faunaService->atualizarCampanha($contrato, $campanhaId, $validated);
                $message = 'Campanha atualizada com sucesso!';
            } else {
                // Criar nova campanha se não houver id_campanha
                $campanhaId = $this->faunaService->salvarCampanha($contrato, $validated);
                $message = 'Campanha salva com sucesso!';
            }

            // Associar ABIOS, se fornecidos
            if (!empty($validated['id_abio'])) {
                SgcFaunaCampanhaAbios::where('campanha_id', $campanhaId)->delete();
                foreach ($validated['id_abio'] as $abioId) {
                    SgcFaunaCampanhaAbios::create([
                        'contrato_id' => $contrato,
                        'campanha_id' => $campanhaId,
                        'n_abio' => $abioId,
                    ]);
                }
            }

            // Salvar resultados com considerações, se fornecidos
            if ($campanhaId && !empty($validated['planilha']) && $validated['planilha']->isValid()) {
                $this->faunaService->salvarResultados($contrato, $validated['planilha'], $campanhaId, $validated['consideracoes'] ?? null);
            }

            DB::commit();
            return redirect()->route('sgc.contratada.produtos.index', [$contrato, $produto])
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('FaunaController: Erro ao salvar campanha', [
                'contrato' => $contrato,
                'produto' => $produto,
                'campanha_id' => $campanhaId ?? 'não fornecido',
                'consideracoes' => $validated['consideracoes'] ?? 'não fornecido',
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
        'resultadosTerrestre',
        'resultadosAquatica',
        'resultadosCavernicola',
        'resultados_consideracoes',
        'anexos'
    ])->findOrFail($campanhaId);

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

            // ⭐⭐⭐ RESULTADOS — agora as 3 tabelas SEPARADAS ⭐⭐⭐
            'resultadosTerrestre' => $campanha->resultadosTerrestre->map(fn($r) => $r->toArray())->toArray(),
            'resultadosAquatica' => $campanha->resultadosAquatica->map(fn($r) => $r->toArray())->toArray(),
            'resultadosCavernicola' => $campanha->resultadosCavernicola->map(fn($r) => $r->toArray())->toArray(),

            'consideracoes' => $campanha->resultados_consideracoes->consideracoes ?? null,

            'anexos' => $campanha->anexos->map(function ($anexo) {
                return [
                    'id' => $anexo->id,
                    'tipo_anexo' => $anexo->tipo_anexo,
                    'caminho' => Storage::url($anexo->caminho),
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

        $analises    = $this->faunaFiscalService->getAnalisesByCampanha($contrato, $campanha);
        $comentarios = $this->faunaService->getComentariosByCampanha($contrato, $campanha);

        // ----------------- FORM MÓDULO AMOSTRAL -----------------
        $formModuloAmostral = (
            $campanhaObj->relationLoaded('modulos_amostrais')
            && $campanhaObj->modulos_amostrais
            && $campanhaObj->modulos_amostrais->isNotEmpty()
        )
            ? $campanhaObj->modulos_amostrais->map(function ($modulo) {
                return [
                    'id'                => $modulo->id,
                    'data_cadastro'     => $modulo->data_cadastro,
                    'tamanho_modulo'    => $modulo->tamanho_modulo,
                    'uf'                => $modulo->uf,
                    'municipio'         => $modulo->municipio,
                    'bioma'             => $modulo->bioma,
                    'fitofisionomia'    => $modulo->fitofisionomia,
                    'latitude_inicial'  => $modulo->latitude_inicial,
                    'longitude_inicial' => $modulo->longitude_inicial,
                    'latitude_final'    => $modulo->latitude_final,
                    'longitude_final'   => $modulo->longitude_final,
                    'arquivo'           => $modulo->nome_arquivo,
                    'obs'               => $modulo->obs,
                ];
            })->toArray()
            : [];

        // ----------------- PONTOS QUELO/CROCOD -----------------
        $formPontosAmostragem = (
            $campanhaObj->relationLoaded('pontos_quelo_crocod')
            && $campanhaObj->pontos_quelo_crocod
            && $campanhaObj->pontos_quelo_crocod->isNotEmpty()
        )
            ? $campanhaObj->pontos_quelo_crocod->map(function ($ponto) {
                return [
                    'id'                 => $ponto->id,
                    'ponto_de_coleta'    => $ponto->ponto_de_coleta,
                    'nome_curso_hidrico' => $ponto->nome_curso_hidrico,
                    'latitude'           => $ponto->latitude,
                    'longitude'          => $ponto->longitude,
                    'bacia'              => $ponto->bacia_hidrografica,
                    'profundidade'       => $ponto->profundidade,
                    'largura'            => $ponto->largura,
                    'tipo_substrato'     => $ponto->tipo_substrato,
                ];
            })->toArray()
            : [];

        // ----------------- PONTOS CAVERNÍCOLA -----------------
        $formPontosCavernicola = (
            $campanhaObj->relationLoaded('pontos_cavernicola')
            && $campanhaObj->pontos_cavernicola
            && $campanhaObj->pontos_cavernicola->isNotEmpty()
        )
            ? $campanhaObj->pontos_cavernicola->map(function ($ponto) {
                return [
                    'id'                         => $ponto->id,
                    'cavidade'                   => $ponto->cavidade,
                    'latitude'                   => $ponto->latitude,
                    'longitude'                  => $ponto->longitude,
                    'distancia_eixo_rodovia'     => $ponto->distancia_eixo_rodovia,
                    'formacao_associada'         => $ponto->formacao_associada,
                    'temperatura_media_interna'  => $ponto->temperatura_media_interna,
                    'temperatura_media_externa'  => $ponto->temperatura_media_externa,
                    'umidade_relativa_interna'   => $ponto->umidade_relativa_interna,
                    'umidade_relativa_externa'   => $ponto->umidade_relativa_externa,
                ];
            })->toArray()
            : [];

        // ----------------- METODOLOGIA -----------------
        $formMetodologia = (
            $campanhaObj->relationLoaded('metodologias')
            && $campanhaObj->metodologias
            && $campanhaObj->metodologias->isNotEmpty()
        )
            ? $campanhaObj->metodologias->map(function ($metodologia) {
                return [
                    'id'              => $metodologia->id,
                    'grupo_faunistico'=> $metodologia->grupo_faunistico,
                    'metodologia'     => $metodologia->metodologia,
                ];
            })->toArray()
            : [];

        // ----------------- FORM RESULTADOS (último registro) -----------------
        $formResultados = (
            $campanhaObj->relationLoaded('resultados')
            && $campanhaObj->resultados
            && $campanhaObj->resultados->isNotEmpty()
        )
            ? [
                'id_campanha'               => $campanhaObj->resultados->last()->id_campanha,
                'modulo'                    => $campanhaObj->resultados->last()->modulo,
                'parcela'                   => $campanhaObj->resultados->last()->parcela,
                'id_armadilha'              => $campanhaObj->resultados->last()->id_armadilha,
                'grupo_amostrado'           => $campanhaObj->resultados->last()->grupo_amostrado,
                'data_registro'             => $campanhaObj->resultados->last()->data_registro,
                'hora_registro'             => $campanhaObj->resultados->last()->hora_registro,
                'categoria'                 => $campanhaObj->resultados->last()->categoria,
                'classe'                    => $campanhaObj->resultados->last()->classe,
                'ordem'                     => $campanhaObj->resultados->last()->ordem,
                'familia'                   => $campanhaObj->resultados->last()->familia,
                'genero'                    => $campanhaObj->resultados->last()->genero,
                'especie'                   => $campanhaObj->resultados->last()->especie,
                'nome_cientifico'           => $campanhaObj->resultados->last()->nome_cientifico,
                'nome_comum'                => $campanhaObj->resultados->last()->nome_comum,
                'sexo'                      => $campanhaObj->resultados->last()->sexo,
                'faixa_etaria'              => $campanhaObj->resultados->last()->faixa_etaria,
                'qnt_individuos'            => $campanhaObj->resultados->last()->qnt_individuos,
                'num_marcacao'              => $campanhaObj->resultados->last()->num_marcacao,
                'coletado'                  => $campanhaObj->resultados->last()->coletado,
                'num_tombamento'            => $campanhaObj->resultados->last()->num_tombamento,
                'dados_biometricos'         => $campanhaObj->resultados->last()->dados_biometricos,
                'comp_total'                => $campanhaObj->resultados->last()->comp_total,
                'cabeca'                    => $campanhaObj->resultados->last()->cabeca,
                'cauda'                     => $campanhaObj->resultados->last()->cauda,
                'femur'                     => $campanhaObj->resultados->last()->femur,
                'orelha'                    => $campanhaObj->resultados->last()->orelha,
                'peso'                      => $campanhaObj->resultados->last()->peso,
                'status_conservacao_federal'=> $campanhaObj->resultados->last()->status_conservacao_federal,
                'status_conservacao_iucn'   => $campanhaObj->resultados->last()->status_conservacao_iucn,
                'especies_bioindicadoras'   => $campanhaObj->resultados->last()->especies_bioindicadoras ?? null,
                'especies_alvo_monitoramento'=> $campanhaObj->resultados->last()->especies_alvo_monitoramento ?? null,
            ]
            : [
                'id_campanha'                 => null,
                'modulo'                      => null,
                'parcela'                     => null,
                'id_armadilha'                => null,
                'grupo_amostrado'             => null,
                'data_registro'               => null,
                'hora_registro'               => null,
                'categoria'                   => null,
                'classe'                      => null,
                'ordem'                       => null,
                'familia'                     => null,
                'genero'                      => null,
                'especie'                     => null,
                'nome_cientifico'             => null,
                'nome_comum'                  => null,
                'sexo'                        => null,
                'faixa_etaria'                => null,
                'qnt_individuos'              => null,
                'num_marcacao'                => null,
                'coletado'                    => null,
                'num_tombamento'              => null,
                'dados_biometricos'           => null,
                'comp_total'                  => null,
                'cabeca'                      => null,
                'cauda'                       => null,
                'femur'                       => null,
                'orelha'                      => null,
                'peso'                        => null,
                'status_conservacao_federal'  => null,
                'status_conservacao_iucn'     => null,
                'especies_bioindicadoras'     => null,
                'especies_alvo_monitoramento' => null,
            ];

        // ==========================================================
        //   NOVO: SEPARAÇÃO DOS RESULTADOS POR TIPO (TERRESTRE/AQUÁTICA/CAVERNÍCOLA)
        // ==========================================================
        $resultadosTerrestre   = [];
        $resultadosAquatica    = [];
        $resultadosCavernicola = [];

        if (
            $campanhaObj->relationLoaded('resultados')
            && $campanhaObj->resultados
            && $campanhaObj->resultados->isNotEmpty()
        ) {
            // -------- TERRESTRE --------
            $resultadosTerrestre = $campanhaObj->resultados
                ->where('grupo_amostrado', 'terrestre') // ajuste o valor conforme gravado no banco
                ->map(function ($r) {
                    return [
                        'id'                     => $r->id,
                        'campanha'               => $r->campanha,
                        'estacao_do_ano'         => $r->estacao_do_ano,
                        'data'                   => $r->data,
                        'horario'                => $r->horario,
                        'condicao_climatica'     => $r->condicao_climatica,
                        'temperatura'            => $r->temperatura,
                        'pluviosidade'           => $r->pluviosidade,
                        'municipio'              => $r->municipio,
                        'unidade_amostral'       => $r->unidade_amostral,
                        'ponto_amostral'         => $r->ponto_amostral,
                        'latitude'               => $r->latitude,
                        'longitude'              => $r->longitude,
                        'metodologia'            => $r->metodologia,
                        'tipo_metodologia'       => $r->tipo_metodologia,
                        'fitofisionomia'         => $r->fitofisionomia,
                        'habitat'                => $r->habitat,
                        'caracteristicas_ponto'  => $r->caracteristicas_ponto,

                        // TAXONOMIA
                        'classe'                 => $r->classe,
                        'ordem'                  => $r->ordem,
                        'familia'                => $r->familia,
                        'genero'                 => $r->genero,
                        'especie'                => $r->especie,
                        'nome_cientifico'        => $r->nome_cientifico,
                        'nome_comum'             => $r->nome_comum,

                        // ATRIBUTOS
                        'abundancia'             => $r->abundancia,
                        'sensibilidade'          => $r->sensibilidade,
                        'endemismo'              => $r->endemismo,
                        'observacao'             => $r->observacao,

                        // STATUS
                        'iucn'                   => $r->iucn,
                        'mma'                    => $r->mma,
                        'salve'                  => $r->salve,
                        'estado'                 => $r->estado,

                        // COLETA
                        'registro_fotografico'   => $r->registro_fotografico,
                        'coletado'               => $r->coletado,
                        'numero_tombo'           => $r->numero_tombo,
                    ];
                })
                ->values()
                ->toArray();

            // -------- AQUÁTICA --------
            $resultadosAquatica = $campanhaObj->resultados
                ->where('grupo_amostrado', 'aquatica')
                ->map(function ($r) {
                    return [
                        'id'                          => $r->id,
                        'campanha'                    => $r->campanha,
                        'estacao_do_ano'              => $r->estacao_do_ano,
                        'data'                        => $r->data,
                        'horario'                     => $r->horario,
                        'condicao_climatica'          => $r->condicao_climatica,
                        'temperatura'                 => $r->temperatura,
                        'pluviosidade'                => $r->pluviosidade,
                        'municipio'                   => $r->municipio,
                        'unidade_amostral'            => $r->unidade_amostral,
                        'ponto_amostral'              => $r->ponto_amostral,
                        'latitude'                    => $r->latitude,
                        'longitude'                   => $r->longitude,
                        'metodologia'                 => $r->metodologia,
                        'tipo_metodologia'            => $r->tipo_metodologia,
                        'fitofisionomia'              => $r->fitofisionomia,

                        'habitat_preferencial'        => $r->habitat_preferencial,
                        'tipo_ambiente'               => $r->tipo_ambiente,
                        'largura_media_rio'           => $r->largura_media_rio,
                        'profundidade_media'          => $r->profundidade_media,
                        'tipo_substrato'              => $r->tipo_substrato,
                        'caracteristicas_agua'        => $r->caracteristicas_agua,
                        'caracteristicas_entorno_ponto'=> $r->caracteristicas_entorno_ponto,

                        // TAXONOMIA
                        'classe'                      => $r->classe,
                        'ordem'                       => $r->ordem,
                        'familia'                     => $r->familia,
                        'genero'                      => $r->genero,
                        'especie'                     => $r->especie,
                        'nome_cientifico'             => $r->nome_cientifico,
                        'nome_comum'                  => $r->nome_comum,

                        'abundancia'                  => $r->abundancia,
                        'sensibilidade'               => $r->sensibilidade,
                        'endemismo'                   => $r->endemismo,
                        'observacao'                  => $r->observacao,

                        'iucn'                        => $r->iucn,
                        'mma'                         => $r->mma,
                        'salve'                       => $r->salve,
                        'estado'                      => $r->estado,

                        'registro_fotografico'        => $r->registro_fotografico,
                        'coletado'                    => $r->coletado,
                        'numero_tombo'                => $r->numero_tombo,
                    ];
                })
                ->values()
                ->toArray();

            // -------- CAVERNÍCOLA --------
            $resultadosCavernicola = $campanhaObj->resultados
                ->where('grupo_amostrado', 'cavernicola')
                ->map(function ($r) {
                    return [
                        'id'                         => $r->id,
                        'caverna'                    => $r->caverna,
                        'campanha'                   => $r->campanha,
                        'estacao_do_ano'             => $r->estacao_do_ano,
                        'data'                       => $r->data,
                        'horario'                    => $r->horario,
                        'condicao_climatica'         => $r->condicao_climatica,
                        'temperatura'                => $r->temperatura,
                        'pluviosidade'               => $r->pluviosidade,
                        'municipio'                  => $r->municipio,
                        'unidade_amostral'           => $r->unidade_amostral,
                        'ponto_amostral'             => $r->ponto_amostral,
                        'latitude'                   => $r->latitude,
                        'longitude'                  => $r->longitude,
                        'metodologia'                => $r->metodologia,
                        'tipo_metodologia'           => $r->tipo_metodologia,
                        'fitofisionomia'             => $r->fitofisionomia,
                        'substrato_amostrado'        => $r->substrato_amostrado,
                        'caracteristicas_entorno_ponto' => $r->caracteristicas_entorno_ponto,

                        // TAXONOMIA
                        'classe'                     => $r->classe,
                        'ordem'                      => $r->ordem,
                        'familia'                    => $r->familia,
                        'genero'                     => $r->genero,
                        'especie'                    => $r->especie,
                        'nome_cientifico'            => $r->nome_cientifico,
                        'nome_comum'                 => $r->nome_comum,

                        'abundancia'                 => $r->abundancia,
                        'categoria_ecologica'        => $r->categoria_ecologica,
                        'sensibilidade'              => $r->sensibilidade,
                        'endemismo'                  => $r->endemismo,
                        'observacao'                 => $r->observacao,

                        'presenca_guano'             => $r->presenca_guano,
                        'presenca_agua'              => $r->presenca_agua,
                        'conectividade_externa'      => $r->conectividade_externa,
                        'perturbacao_antropica'      => $r->perturbacao_antropica,

                        'iucn'                       => $r->iucn,
                        'mma'                        => $r->mma,
                        'salve'                      => $r->salve,
                        'estado'                     => $r->estado,

                        'registro_fotografico'       => $r->registro_fotografico,
                        'coletado'                   => $r->coletado,
                        'numero_tombo'               => $r->numero_tombo,
                    ];
                })
                ->values()
                ->toArray();
        }

        return Inertia::render('Sgc/Contratada/Produtos/Fauna/AnaliseCampanha', [
            'campanha' => [
                'id'                    => $campanhaObj->id,
                'id_campanha'           => $campanhaObj->id_campanha,
                'cod_emp'               => $campanhaObj->cod_emp,
                'familia'               => $campanhaObj->subproduto,
                'data_campanha_inicial' => $campanhaObj->data_ini,
                'data_campanha_final'   => $campanhaObj->data_fim,
                'periodo'               => $campanhaObj->periodo,
                'observacoes'           => $campanhaObj->observacoes,
                'nao_se_aplica'         => $campanhaObj->nao_se_aplica ?? false,
                'status'                => $campanhaObj->status,

                'formModuloAmostral'    => $formModuloAmostral,
                'formPontosAmostragem'  => $formPontosAmostragem,
                'formPontosCavernicola' => $formPontosCavernicola,
                'formMetodologia'       => $formMetodologia,
                'formResultados'        => $formResultados,

                'consideracoes'         => $campanhaObj->resultados_consideracoes->consideracoes ?? null,

                // NOVO: resultados separados por grupo para a aba de análise
                'resultadosTerrestre'   => $resultadosTerrestre,
                'resultadosAquatica'    => $resultadosAquatica,
                'resultadosCavernicola' => $resultadosCavernicola,

                'abios' => $campanhaObj->abios->map(function ($abio) {
                    return [
                        'id'   => $abio->n_abio,
                        'abio' => [
                            'numero_licenca' => $abio->abio->numero_licenca ?? 'N/A',
                        ],
                    ];
                })->toArray(),

                'profissionais' => $campanhaObj->profissionais->map(function ($prof) {
                    return [
                        'id'               => $prof->id,
                        'profissional'     => $prof->profissional->profissional ?? 'N/A',
                        'grupo_faunistico' => $prof->grupo_faunistico,
                        'formacao'         => $prof->profissional->formacao ?? 'N/A',
                        'funcao'           => $prof->profissional->funcao ?? 'N/A',
                        'ctf'              => $prof->profissional->ctf ?? 'N/A',
                    ];
                })->toArray(),

                'modulos_amostrais' => (
                    $campanhaObj->relationLoaded('modulos_amostrais')
                    && $campanhaObj->modulos_amostrais
                )
                    ? $campanhaObj->modulos_amostrais->map(function ($modulo) {
                        return [
                            'id'                => $modulo->id,
                            'data_cadastro'     => $modulo->data_cadastro,
                            'tamanho_modulo'    => $modulo->tamanho_modulo,
                            'uf'                => $modulo->uf,
                            'municipio'         => $modulo->municipio,
                            'bioma'             => $modulo->bioma,
                            'fitofisionomia'    => $modulo->fitofisionomia,
                            'latitude_inicial'  => $modulo->latitude_inicial,
                            'longitude_inicial' => $modulo->longitude_inicial,
                            'latitude_final'    => $modulo->latitude_final,
                            'longitude_final'   => $modulo->longitude_final,
                            'obs'               => $modulo->obs,
                            'arquivo'           => $modulo->nome_arquivo,
                        ];
                    })->toArray()
                    : [],

                'pontos_quelo_crocod' => (
                    $campanhaObj->relationLoaded('pontos_quelo_crocod')
                    && $campanhaObj->pontos_quelo_crocod
                )
                    ? $campanhaObj->pontos_quelo_crocod->map(function ($ponto) {
                        return [
                            'id'                 => $ponto->id,
                            'ponto_de_coleta'    => $ponto->ponto_de_coleta,
                            'nome_curso_hidrico' => $ponto->nome_curso_hidrico,
                            'latitude'           => $ponto->latitude,
                            'longitude'          => $ponto->longitude,
                            'bacia'              => $ponto->bacia_hidrografica,
                            'profundidade'       => $ponto->profundidade,
                            'largura'            => $ponto->largura,
                            'tipo_substrato'     => $ponto->tipo_substrato,
                        ];
                    })->toArray()
                    : [],

                'pontos_cavernicola' => (
                    $campanhaObj->relationLoaded('pontos_cavernicola')
                    && $campanhaObj->pontos_cavernicola
                )
                    ? $campanhaObj->pontos_cavernicola->map(function ($ponto) {
                        return [
                            'id'                         => $ponto->id,
                            'cavidade'                   => $ponto->cavidade,
                            'latitude'                   => $ponto->latitude,
                            'longitude'                  => $ponto->longitude,
                            'distancia_eixo_rodovia'     => $ponto->distancia_eixo_rodovia,
                            'formacao_associada'         => $ponto->formacao_associada,
                            'temperatura_media_interna'  => $ponto->temperatura_media_interna,
                            'temperatura_media_externa'  => $ponto->temperatura_media_externa,
                            'umidade_relativa_interna'   => $ponto->umidade_relativa_interna,
                            'umidade_relativa_externa'   => $ponto->umidade_relativa_externa,
                        ];
                    })->toArray()
                    : [],

                'metodologias' => (
                    $campanhaObj->relationLoaded('metodologias')
                    && $campanhaObj->metodologias
                )
                    ? $campanhaObj->metodologias->map(function ($metodologia) {
                        return [
                            'id'              => $metodologia->id,
                            'grupo_faunistico'=> $metodologia->grupo_faunistico,
                            'metodologia'     => $metodologia->metodologia,
                        ];
                    })->toArray()
                    : [],

                'anexos' => (
                    $campanhaObj->relationLoaded('anexos')
                    && $campanhaObj->anexos
                )
                    ? $campanhaObj->anexos->map(function ($anexo) {
                        return [
                            'id'          => $anexo->id,
                            'tipo_anexo'  => $anexo->tipo_anexo,
                            'caminho'     => $anexo->caminho,
                            'nome_arquivo'=> $anexo->nome_arquivo ?? basename($anexo->caminho),
                            'created_at'  => $anexo->created_at,
                        ];
                    })->toArray()
                    : [],
            ],
            'contrato'   => $campanhaObj->id_contrato,
            'produto'    => $campanhaObj->subproduto,
            'contratos'  => ['contratada' => 'Nome da Contratada', 'tipo_contrato' => 'Tipo'],
            'canApprove' => Auth::user()->perfis_id === 2 && $campanhaObj->status === 'Em análise',
            'analises'   => $analises ?? [],
            'comentarios'=> $comentarios ?? [],
        ]);
    }


    public function salvarAnalise(Request $request, $contrato, $produto, $campanha): RedirectResponse
    {
        if (Auth::user()->perfis_id !== 2) {
            return redirect()->back()->withErrors(['error' => 'Acesso negado. Apenas fiscais podem salvar análises.']);
        }

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
                'nome_cientifico' => $resultado->nome_cientifico,
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


        // if ($campanha->status !== 'Rejeitada') {
        //     return redirect()->route('sgc.contratada.produtos.show', [$contrato, $produto, $campanhaId])
        //         ->withErrors(['error' => 'Apenas campanhas rejeitadas podem ser editadas.']);
        // }

        if (!in_array($campanha->status, ['Rejeitada', 'Em elaboração'])) {
            return redirect()->route('sgc.contratada.produtos.show', [$contrato, $produto, $campanhaId])
                ->withErrors(['error' => 'Apenas campanhas rejeitadas ou em elaboração podem ser editadas.']);
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

        if (Auth::user()->perfis_id === 2) {
            return redirect()->route('sgc.contratada.produtos.show', [$contrato, $produto, $campanhaId])
                ->withErrors(['error' => 'Acesso negado. Fiscais não podem atualizar campanhas.']);
        }

        $campanha = SgcFaunaCampanha::findOrFail($campanhaId);

        if (!in_array($campanha->status, ['Rejeitada', 'Em elaboração'])) {
            return redirect()->route('sgc.contratada.produtos.show', [$contrato, $produto, $campanhaId])
                ->withErrors(['error' => 'Apenas campanhas rejeitadas ou em elaboração podem ser atualizadas.']);
        }

        try {
            $validated = $request->validate([
                'data_campanha_inicial' => 'nullable|date',
                'data_campanha_final' => 'nullable|date|after_or_equal:data_campanha_inicial',
                'periodo' => 'nullable|string|max:255',
                'observacoes' => 'nullable|string',
                'cod_emp' => 'required|string|max:255',
                'subproduto' => 'required|string|max:255',
                'nao_se_aplica_quelo' => 'nullable|boolean', 
                'nao_se_aplica_cavernicola' => 'nullable|boolean', 
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
                'modulos_amostrais.*.latitude_inicial' => 'nullable|numeric',
                'modulos_amostrais.*.longitude_inicial' => 'nullable|numeric',
                'modulos_amostrais.*.latitude_final' => 'nullable|numeric',
                'modulos_amostrais.*.longitude_final' => 'nullable|numeric',
                'modulos_amostrais.*.obs' => 'nullable|string',
                'modulos_amostrais.*.arquivo' => 'nullable|file|mimes:shp,zip|max:20480',
                'pontos_quelo_crocod' => 'nullable|array',
                'pontos_quelo_crocod.*.ponto_de_coleta' => 'required_without:nao_se_aplica_quelo|string',
                'pontos_quelo_crocod.*.nome_curso_hidrico' => 'required_without:nao_se_aplica_quelo|string',
                'pontos_quelo_crocod.*.latitude' => 'nullable|string',
                'pontos_quelo_crocod.*.longitude' => 'nullable|string',
                'pontos_quelo_crocod.*.profundidade' => 'nullable|numeric',
                'pontos_quelo_crocod.*.largura' => 'required_without:nao_se_aplica_quelo|numeric',
                'pontos_quelo_crocod.*.tipo_substrato' => 'nullable|string',
                'pontos_cavernicola' => 'nullable|array',
                'pontos_cavernicola.*.cavidade' => 'required_without:nao_se_aplica_cavernicola|string',
                'pontos_cavernicola.*.latitude' => 'required_without:nao_se_aplica_cavernicola|numeric',
                'pontos_cavernicola.*.longitude' => 'required_without:nao_se_aplica_cavernicola|numeric',
                'pontos_cavernicola.*.distancia_eixo_rodovia' => 'required_without:nao_se_aplica_cavernicola|numeric',
                'pontos_cavernicola.*.formacao_associada' => 'required_without:nao_se_aplica_cavernicola|string',
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

            // Forçar parsing correto de abios
            $validated['id_abio'] = array_map(function ($abio) {

                // Usar o ID da licença recebido diretamente do frontend
                return isset($abio['abio']['id']) ? (int) $abio['abio']['id'] : null;
            }, (array) $request->input('abios', []));

            // Remover valores nulos
            $validated['id_abio'] = array_filter($validated['id_abio'], fn($id) => !is_null($id));

            // Forçar parsing correto de profissionais
            $validated['profissionais'] = array_map(function ($profissional) {
                return [
                    'id_profissional' => isset($profissional['profissional']['id']) ? (int) $profissional['profissional']['id'] : null,
                    'grupo_faunistico' => $profissional['grupo_faunistico'] ?? null,
                    'formacao' => isset($profissional['profissional']['formacao']) ? $profissional['profissional']['formacao'] : null,
                ];
            }, (array) $request->input('profissionais', []));

            $validated['anexos'] = $request->file('anexos') ?? [];
            $validated['planilha'] = $request->file('planilha');

            DB::beginTransaction();
            $campanhaId = $this->faunaService->atualizarCampanha($contrato, $campanhaId, $validated);
            DB::commit();

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

    }

    public function destroyAnexo($contrato, $produto, $campanhaId, $anexoId): RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->back()->withErrors(['error' => 'Acesso negado. Você precisa estar autenticado.']);
        }
        try {
            $anexo = SgcFaunaAnexo::where('id', $anexoId)
                ->where('id_contrato', $contrato)
                ->where('id_campanha', $campanhaId)
                ->firstOrFail();
            
            $campanha = SgcFaunaCampanha::findOrFail($campanhaId);
            if (!in_array($campanha->status, ['Rejeitada', 'Em elaboração'])) {
                return redirect()->back()->withErrors(['error' => 'Apenas campanhas rejeitadas ou em elaboração podem ter anexos excluídos.']);
            }
            
            Storage::disk('public')->delete($anexo->caminho);
            $anexo->delete();
            return redirect()->back()->with('success', 'Anexo excluído com sucesso!');
        } catch (\Exception $e) {
            Log::error('FaunaController: Erro ao excluir anexo', [
                'contrato_id' => $contrato,
                'campanha_id' => $campanhaId,
                'anexo_id' => $anexoId,
                'erro' => $e->getMessage(),
            ]);
            return redirect()->back()->withErrors(['error' => 'Erro ao excluir anexo: ' . $e->getMessage()]);
        }
    }


}