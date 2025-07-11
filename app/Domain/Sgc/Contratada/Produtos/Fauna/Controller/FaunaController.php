<?php

namespace App\Domain\Sgc\Contratada\Produtos\Fauna\Controller;

use App\Shared\Http\Controllers\Controller;
use App\Domain\Sgc\Contratada\Produtos\Fauna\Services\FaunaService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use App\Models\SgcFaunaCampanhaAbios;
use App\Models\SgcFaunaCampanha;
use App\Models\SgcFaunaModuloAmostral;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class FaunaController extends Controller
{
    protected $faunaService;

    public function __construct(FaunaService $faunaService)
    {
        $this->faunaService = $faunaService;
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
            'id_abio.*' => 'integer|exists:fauna_config_abio,id',
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
            'pontos_quelo_crocod.*.coordenadas' => 'nullable|string',
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
            'metodologias.*.grupo_faunistico' => 'required_with:metodologias|string|in:Avifauna,Herpetofauna,Mastofauna,Ictiofauna,Bentos',
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

        $formModuloAmostral = $modulosManuais->isNotEmpty() ? [
            'id' => $modulosManuais->last()->id,
            'data_cadastro' => $modulosManuais->last()->data_cadastro,
            'tamanho_modulo' => $modulosManuais->last()->tamanho_modulo,
            'uf' => $modulosManuais->last()->uf,
            'municipio' => $modulosManuais->last()->municipio,
            'bioma' => $modulosManuais->last()->bioma,
            'fitofisionomia' => $modulosManuais->last()->fitofisionomia,
            'latitude_inicial' => $modulosManuais->last()->latitude_inicial,
            'longitude_inicial' => $modulosManuais->last()->longitude_inicial,
            'latitude_final' => $modulosManuais->last()->latitude_final,
            'longitude_final' => $modulosManuais->last()->longitude_final,
            'arquivo' => $modulosManuais->last()->nome_arquivo,
            'obs' => $modulosManuais->last()->obs,
        ] : [
            'id' => null,
            'data_cadastro' => null,
            'tamanho_modulo' => null,
            'uf' => null,
            'municipio' => null,
            'bioma' => null,
            'fitofisionomia' => null,
            'latitude_inicial' => null,
            'longitude_inicial' => null,
            'latitude_final' => null,
            'longitude_final' => null,
            'arquivo' => null,
            'obs' => null,
        ];

        $formPontosAmostragem = $campanha->pontos_quelo_crocod->isNotEmpty() ? [
            'ponto_de_coleta' => $campanha->pontos_quelo_crocod->last()->ponto_de_coleta,
            'nome_curso_hidrico' => $campanha->pontos_quelo_crocod->last()->nome_curso_hidrico,
            'coordenadas' => $campanha->pontos_quelo_crocod->last()->coordenadas,
            'bacia' => $campanha->pontos_quelo_crocod->last()->bacia_hidrografica,
            'profundidade' => $campanha->pontos_quelo_crocod->last()->profundidade,
            'largura' => $campanha->pontos_quelo_crocod->last()->largura,
            'tipo_substrato' => $campanha->pontos_quelo_crocod->last()->tipo_substrato,
        ] : [
            'ponto_de_coleta' => null,
            'nome_curso_hidrico' => null,
            'coordenadas' => null,
            'bacia' => null,
            'profundidade' => null,
            'largura' => null,
            'tipo_substrato' => null,
        ];

        $formPontosCavernicola = $campanha->pontos_cavernicola->isNotEmpty() ? [
            'cavidade' => $campanha->pontos_cavernicola->last()->cavidade,
            'latitude' => $campanha->pontos_cavernicola->last()->latitude,
            'longitude' => $campanha->pontos_cavernicola->last()->longitude,
            'distancia_eixo_rodovia' => $campanha->pontos_cavernicola->last()->distancia_eixo_rodovia,
            'formacao_associada' => $campanha->pontos_cavernicola->last()->formacao_associada,
            'temperatura_media_interna' => $campanha->pontos_cavernicola->last()->temperatura_media_interna,
            'temperatura_media_externa' => $campanha->pontos_cavernicola->last()->temperatura_media_externa,
            'umidade_relativa_interna' => $campanha->pontos_cavernicola->last()->umidade_relativa_interna,
            'umidade_relativa_externa' => $campanha->pontos_cavernicola->last()->umidade_relativa_externa,
        ] : [
            'cavidade' => null,
            'latitude' => null,
            'longitude' => null,
            'distancia_eixo_rodovia' => null,
            'formacao_associada' => null,
            'temperatura_media_interna' => null,
            'temperatura_media_externa' => null,
            'umidade_relativa_interna' => null,
            'umidade_relativa_externa' => null,
        ];

        $formMetodologia = $campanha->metodologias->isNotEmpty() ? [
            'grupo_faunistico' => $campanha->metodologias->last()->grupo_faunistico,
            'metodologia' => $campanha->metodologias->last()->metodologia,
        ] : [
            'grupo_faunistico' => null,
            'metodologia' => null,
        ];

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
                        'coordenadas' => $ponto->coordenadas,
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
            'canApprove' => auth()->user()->hasRole('analista'),
        ]);
    }
}
